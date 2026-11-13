<?php

namespace App\Http\Controllers;

use App\Models\DistribusiJadwal;
use App\Models\DigitalSignage;
use App\Models\JadwalTayang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class DistribusiJadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = DistribusiJadwal::with(['digitalSignage.toko', 'jadwalTayang']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($w) use ($search) {
                $w->whereHas('digitalSignage', function ($q) use ($search) {
                    $q->where('nama_signage', 'like', "%$search%")
                      ->orWhere('device_id', 'like', "%$search%");
                })
                ->orWhereHas('digitalSignage.toko', function ($q) use ($search) {
                    $q->where('nama_toko', 'like', "%$search%")
                      ->orWhere('area', 'like', "%$search%");
                })
                ->orWhereHas('jadwalTayang', function ($q) use ($search) {
                    $q->where('nama_jadwal', 'like', "%$search%");
                });
            });
        }

        // Satu baris = satu signage + periode + frekuensi yang sama,
        // di dalamnya bisa berisi banyak jadwal tayang dengan jam berbeda.
        $grouped = $query->orderBy('created_at', 'desc')->get()
            ->groupBy(fn ($d) => implode('|', [
                $d->signage_id,
                $d->tanggal_mulai?->format('Y-m-d'),
                $d->tanggal_selesai?->format('Y-m-d'),
                $d->frekuensi_hari, $d->frekuensi_tanggal, $d->frekuensi_bulan,
            ]))
            ->values();

        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $distribusi = new LengthAwarePaginator(
            $grouped->forPage($page, $perPage)->values(),
            $grouped->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('distribusi.index', compact('distribusi'));
    }

    /**
     * Helper untuk menormalisasi input jadwal dari form (mendukung format items[], slots[], atau array paralel)
     */
    private function normalizeScheduleItems(Request $request): array
    {
        $rawItems = $request->input('items');

        if (!is_array($rawItems) || empty($rawItems)) {
            $rawItems = $request->input('slots');
        }

        if (!is_array($rawItems) || empty($rawItems)) {
            $jids    = (array) $request->input('jadwal_tayang_id', []);
            $mulai   = (array) $request->input('waktu_mulai', []);
            $selesai = (array) $request->input('waktu_selesai', []);
            $ids     = (array) $request->input('distribusi_item_id', []);

            $rawItems = [];
            foreach ($jids as $idx => $jid) {
                $rawItems[$idx] = [
                    'id'               => $ids[$idx] ?? null,
                    'jadwal_tayang_id' => $jid,
                    'waktu_mulai'      => $mulai[$idx] ?? '00:00',
                    'waktu_selesai'    => $selesai[$idx] ?? '23:59',
                ];
            }
        }

        return is_array($rawItems) ? $rawItems : [];
    }

    // STEP 1: Form distribusi batch (multi-jadwal + multi-jam tayang)
    public function create()
    {
        $jadwal = JadwalTayang::withCount('fileIklan')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $draft  = session('distribusi_draft', []);
        return view('distribusi.create', compact('jadwal', 'draft'));
    }

    // STEP 1 -> STEP 2: simpan draft batch ke session, lanjut pilih lokasi
    public function lanjut(Request $request)
    {
        $rawItems = $this->normalizeScheduleItems($request);
        $request->merge([
            'items' => $rawItems,
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        $validated = $request->validate([
            'tanggal_mulai'              => 'required|date',
            'tanggal_selesai'            => 'required|date|after_or_equal:tanggal_mulai',
            'frekuensi_hari'             => 'required',
            'aktif'                      => 'nullable|boolean',
            'items'                      => 'required|array|min:1',
            'items.*.jadwal_tayang_id'   => ['nullable', \App\Support\Tenant::exists('jadwal_tayang')],
            'items.*.waktu_mulai'        => 'nullable',
            'items.*.waktu_selesai'      => 'nullable',
        ]);

        $validItems = [];
        $jamTerdaftar = [];

        foreach ($request->input('items', []) as $idx => $it) {
            $jid = $it['jadwal_tayang_id'] ?? null;
            if (!empty($jid)) {
                $wm = !empty($it['waktu_mulai']) ? trim($it['waktu_mulai']) : '00:00';
                $ws = !empty($it['waktu_selesai']) ? trim($it['waktu_selesai']) : '23:59';

                $wm = substr($wm, 0, 5);
                $ws = substr($ws, 0, 5);
                $keyJam = "$wm-$ws";

                if (in_array($keyJam, $jamTerdaftar)) {
                    $rowNo = sprintf('%02d', $idx + 1);
                    return back()->withInput()->withErrors([
                        'items' => "Format jam tayang $wm - $ws terduplikasi pada Baris #$rowNo. Setiap baris jadwal harus memiliki format jam tayang yang berbeda."
                    ]);
                }
                $jamTerdaftar[] = $keyJam;

                $validItems[] = [
                    'jadwal_tayang_id' => (int) $jid,
                    'waktu_mulai'      => $wm,
                    'waktu_selesai'    => $ws,
                ];
            }
        }

        if (empty($validItems)) {
            return back()->withInput()->withErrors(['items' => 'Pilih minimal satu jadwal tayang beserta jam tayangnya.']);
        }

        $draft = [
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'frekuensi_hari'  => $validated['frekuensi_hari'],
            'aktif'           => $request->boolean('aktif', true),
            'items'           => $validItems,
        ];

        session(['distribusi_draft' => $draft]);
        return redirect()->route('distribusi.lokasi');
    }

    // STEP 2: tabel pilih lokasi tayang / signage
    public function lokasi()
    {
        $draft = session('distribusi_draft');
        if (!$draft || empty($draft['items'])) {
            return redirect()->route('distribusi.create');
        }

        $jadwalIds = collect($draft['items'])->pluck('jadwal_tayang_id')->unique();
        $jadwalMap = JadwalTayang::withCount('fileIklan')
            ->whereIn('id', $jadwalIds)
            ->get()
            ->keyBy('id');

        $signage = DigitalSignage::with(['toko', 'distribusiJadwal.jadwalTayang'])
            ->get()
            ->sortBy(fn ($s) => sprintf('%s|%s|%s|%s',
                $s->toko?->provinsi ?? 'ZZZ',
                $s->toko?->kota ?? '',
                $s->toko?->nama_toko ?? '',
                $s->nama_signage))
            ->values();

        return view('distribusi.lokasi', compact('draft', 'jadwalMap', 'signage'));
    }

    // STEP 2 -> simpan: buat distribusi batch multi-jam untuk semua signage terpilih & push ke TV
    public function simpanLokasi(Request $request)
    {
        $draft = session('distribusi_draft');
        if (!$draft || empty($draft['items'])) {
            return redirect()->route('distribusi.create');
        }

        $request->validate([
            'signage_ids'   => 'required|array|min:1',
            'signage_ids.*' => [\App\Support\Tenant::exists('digital_signage')],
        ], [
            'signage_ids.required' => 'Pilih minimal satu lokasi tayang.',
            'signage_ids.min'      => 'Pilih minimal satu lokasi tayang.',
        ]);

        $ids = array_unique($request->signage_ids);
        $timpaJamSama = $request->boolean('timpa_jam_sama');

        // Kumpulkan daftar format jam tayang dari batch draft ini
        $draftJamList = [];
        foreach ($draft['items'] as $it) {
            $draftJamList[] = [
                'mulai'   => substr($it['waktu_mulai'], 0, 5),
                'selesai' => substr($it['waktu_selesai'], 0, 5),
                'id'      => $it['jadwal_tayang_id'],
            ];
        }

        // Cek apakah ada jadwal dengan format jam yang sama pada signage terpilih
        $conflicts = DistribusiJadwal::whereIn('signage_id', $ids)
            ->where(function ($query) use ($draftJamList) {
                foreach ($draftJamList as $jam) {
                    $query->orWhere(function ($q) use ($jam) {
                        $q->whereRaw("SUBSTRING(waktu_mulai, 1, 5) = ?", [$jam['mulai']])
                          ->whereRaw("SUBSTRING(waktu_selesai, 1, 5) = ?", [$jam['selesai']]);
                    });
                }
            })
            ->with(['digitalSignage.toko', 'jadwalTayang'])
            ->get();

        if ($conflicts->isNotEmpty() && !$timpaJamSama) {
            $daftarBentrok = $conflicts->map(function ($d) {
                $signage = $d->digitalSignage;
                $toko = $signage?->toko?->nama_toko;
                $nama = $signage?->nama_signage ?? 'Signage #' . $d->signage_id;
                $label = $toko ? "$toko - $nama" : $nama;
                $jadwal = $d->jadwalTayang?->nama_jadwal ?? 'Jadwal';
                $jam = substr($d->waktu_mulai, 0, 5) . ' - ' . substr($d->waktu_selesai, 0, 5);
                return "$label (sudah ada '$jadwal' pada jam $jam)";
            })->unique()->values()->all();

            $pesan = "Distribusi tidak bisa di-push karena ada format jam yang sama pada " . count($daftarBentrok) . " signage: " . implode('; ', array_slice($daftarBentrok, 0, 3));
            if (count($daftarBentrok) > 3) {
                $pesan .= ' dan ' . (count($daftarBentrok) - 3) . ' signage lainnya';
            }
            $pesan .= ". Centang 'Timpa jadwal lama jika format jam sama' di bawah tabel jika Anda sengaja ingin memperbarui jadwal pada jam tersebut.";

            return back()->withInput()->withErrors(['signage_ids' => $pesan]);
        }

        DB::transaction(function () use ($ids, $draft, $draftJamList, $timpaJamSama) {
            if ($timpaJamSama) {
                DistribusiJadwal::whereIn('signage_id', $ids)
                    ->where(function ($query) use ($draftJamList) {
                        foreach ($draftJamList as $jam) {
                            $query->orWhere(function ($q) use ($jam) {
                                $q->whereRaw("SUBSTRING(waktu_mulai, 1, 5) = ?", [$jam['mulai']])
                                  ->whereRaw("SUBSTRING(waktu_selesai, 1, 5) = ?", [$jam['selesai']]);
                            });
                        }
                    })
                    ->delete();
            }

            foreach ($draft['items'] as $item) {
                foreach ($ids as $id) {
                    DistribusiJadwal::create([
                        'client_id'        => \App\Support\Tenant::id(),
                        'signage_id'       => $id,
                        'jadwal_tayang_id' => $item['jadwal_tayang_id'],
                        'tanggal_mulai'    => $draft['tanggal_mulai'],
                        'tanggal_selesai'  => $draft['tanggal_selesai'],
                        'waktu_mulai'      => $item['waktu_mulai'],
                        'waktu_selesai'    => $item['waktu_selesai'],
                        'frekuensi_hari'   => $draft['frekuensi_hari'],
                        'aktif'            => $draft['aktif'] ?? true,
                    ]);
                }
            }

            DigitalSignage::whereIn('id', $ids)->update([
                'needs_update'  => 1,
                'status_konten' => 'Belum Terbaru',
            ]);
        });

        session()->forget('distribusi_draft');

        $totalJadwal = count($draft['items']);
        return redirect()->route('distribusi.index')
            ->with('success', count($ids) . " signage berhasil didistribusikan dengan $totalJadwal jadwal tayang berbeda jam. Konten akan dikirim ke TV.");
    }

    public function batal()
    {
        session()->forget('distribusi_draft');
        return redirect()->route('distribusi.index');
    }

    public function store(Request $request)
    {
        $request->merge([
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        $validated = $request->validate([
            'signage_id'        => ['required', \App\Support\Tenant::exists('digital_signage')],
            'jadwal_tayang_id'  => ['required', \App\Support\Tenant::exists('jadwal_tayang')],
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_mulai'       => 'required',
            'waktu_selesai'     => 'required',
            'frekuensi_hari'    => 'required',
            'aktif'             => 'nullable|boolean',
        ]);

        $waktuMulai = substr($validated['waktu_mulai'], 0, 5);
        $waktuSelesai = substr($validated['waktu_selesai'], 0, 5);

        $conflict = DistribusiJadwal::where('signage_id', $validated['signage_id'])
            ->whereRaw("SUBSTRING(waktu_mulai, 1, 5) = ?", [$waktuMulai])
            ->whereRaw("SUBSTRING(waktu_selesai, 1, 5) = ?", [$waktuSelesai])
            ->with('jadwalTayang')
            ->first();

        if ($conflict) {
            $namaJadwal = $conflict->jadwalTayang?->nama_jadwal ?? 'Jadwal';
            return back()->withInput()->withErrors([
                'waktu_mulai' => "Signage ini sudah memiliki jadwal tayang '$namaJadwal' pada jam $waktuMulai - $waktuSelesai. Jadwal lain hanya dapat ditambahkan pada format jam yang berbeda."
            ]);
        }

        DistribusiJadwal::create($validated);

        return redirect()->route('distribusi.index')->with('success', 'Berhasil disimpan!');
    }

    // Baris-baris distribusi yang satu grup (signage + periode + frekuensi yang sama)
    private function grupDistribusi(DistribusiJadwal $d)
    {
        $query = DistribusiJadwal::where('signage_id', $d->signage_id)
            ->whereDate('tanggal_mulai', $d->tanggal_mulai)
            ->whereDate('tanggal_selesai', $d->tanggal_selesai)
            ->where('frekuensi_hari', $d->frekuensi_hari);

        if ($d->frekuensi_tanggal === null) {
            $query->whereNull('frekuensi_tanggal');
        } else {
            $query->where('frekuensi_tanggal', $d->frekuensi_tanggal);
        }

        if ($d->frekuensi_bulan === null) {
            $query->whereNull('frekuensi_bulan');
        } else {
            $query->where('frekuensi_bulan', $d->frekuensi_bulan);
        }

        return $query->orderBy('waktu_mulai')->orderBy('id');
    }

    public function edit(DistribusiJadwal $distribusi)
    {
        $signage = DigitalSignage::with('toko')->get();
        $jadwal  = JadwalTayang::withCount('fileIklan')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $grup    = $this->grupDistribusi($distribusi)->get();
        return view('distribusi.edit', compact('distribusi', 'signage', 'jadwal', 'grup'));
    }

    public function update(Request $request, DistribusiJadwal $distribusi)
    {
        $rawItems = $this->normalizeScheduleItems($request);

        $request->merge([
            'signage_id' => $request->input('signage_id', $distribusi->signage_id),
            'aktif'      => $request->has('aktif') ? $request->boolean('aktif') : ($distribusi->aktif ?? true),
            'items'      => $rawItems,
        ]);

        $validated = $request->validate([
            'signage_id'                 => ['required', \App\Support\Tenant::exists('digital_signage')],
            'tanggal_mulai'              => 'required|date',
            'tanggal_selesai'            => 'required|date|after_or_equal:tanggal_mulai',
            'frekuensi_hari'             => 'required',
            'aktif'                      => 'nullable|boolean',
            'items'                      => 'required|array|min:1',
            'items.*.id'                 => 'nullable|integer',
            'items.*.jadwal_tayang_id'   => ['nullable', \App\Support\Tenant::exists('jadwal_tayang')],
            'items.*.waktu_mulai'        => 'nullable',
            'items.*.waktu_selesai'      => 'nullable',
        ]);

        $validItems = [];
        $jamTerdaftar = [];

        foreach ($validated['items'] as $idx => $it) {
            $jid = $it['jadwal_tayang_id'] ?? null;
            if (!empty($jid)) {
                $wm = !empty($it['waktu_mulai']) ? trim($it['waktu_mulai']) : substr($distribusi->waktu_mulai ?? '00:00', 0, 5);
                $ws = !empty($it['waktu_selesai']) ? trim($it['waktu_selesai']) : substr($distribusi->waktu_selesai ?? '23:59', 0, 5);

                $wm = substr($wm, 0, 5);
                $ws = substr($ws, 0, 5);
                $keyJam = "$wm-$ws";

                if (in_array($keyJam, $jamTerdaftar)) {
                    $rowNo = sprintf('%02d', $idx + 1);
                    return back()->withInput()->withErrors([
                        'items' => "Format jam tayang $wm - $ws terduplikasi pada Baris #$rowNo. Setiap baris jadwal harus memiliki format jam tayang yang berbeda."
                    ]);
                }
                $jamTerdaftar[] = $keyJam;

                $validItems[] = [
                    'id'               => !empty($it['id']) ? (int) $it['id'] : null,
                    'jadwal_tayang_id' => (int) $jid,
                    'waktu_mulai'      => $wm,
                    'waktu_selesai'    => $ws,
                ];
            }
        }

        if (empty($validItems)) {
            return back()->withInput()->withErrors(['items' => 'Pilih minimal satu jadwal tayang.']);
        }

        $common = [
            'signage_id'      => (int) $validated['signage_id'],
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'frekuensi_hari'  => $validated['frekuensi_hari'],
            'aktif'           => $request->boolean('aktif', true),
        ];

        $signageLama = $distribusi->signage_id;

        DB::transaction(function () use ($validItems, $common, $distribusi) {
            $grup    = $this->grupDistribusi($distribusi)->get()->keyBy('id');
            $dipakai = [];

            foreach ($validItems as $item) {
                $data = $common + [
                    'jadwal_tayang_id' => $item['jadwal_tayang_id'],
                    'waktu_mulai'      => $item['waktu_mulai'],
                    'waktu_selesai'    => $item['waktu_selesai'],
                ];

                if (!empty($item['id']) && $grup->has($item['id'])) {
                    $grup[$item['id']]->update($data);
                    $dipakai[] = (int) $item['id'];
                } else {
                    $data['frekuensi_tanggal'] = $distribusi->frekuensi_tanggal;
                    $data['frekuensi_bulan']   = $distribusi->frekuensi_bulan;
                    $dipakai[] = DistribusiJadwal::create($data)->id;
                }
            }

            // Hapus jadwal dari grup ini yang dihapus oleh user pada form edit
            if ($grup->isNotEmpty()) {
                DistribusiJadwal::whereIn('id', $grup->keys())
                    ->whereNotIn('id', $dipakai)
                    ->delete();
            }
        });

        // Push ke TV (signage lama & baru jika berpindah)
        DigitalSignage::whereIn('id', array_unique([$signageLama, (int) $validated['signage_id']]))
            ->update(['needs_update' => 1, 'status_konten' => 'Belum Terbaru']);

        return redirect()->route('distribusi.index')->with('success', 'Distribusi berhasil diperbarui.');
    }

    public function destroy(DistribusiJadwal $distribusi)
    {
        $signageId = $distribusi->signage_id;
        $this->grupDistribusi($distribusi)->delete();

        DigitalSignage::where('id', $signageId)->update(['needs_update' => 1, 'status_konten' => 'Belum Terbaru']);

        return redirect()->route('distribusi.index')->with('success', 'Berhasil dihapus!');
    }
}