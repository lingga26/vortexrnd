<?php

namespace App\Http\Controllers;

use App\Models\DigitalSignage;
use App\Models\Toko;
use App\Models\ModelSignage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DigitalSignageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DigitalSignage::with(['toko', 'modelSignage']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_signage', 'like', "%$search%")
                  ->orWhere('device_id', 'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%")
                  ->orWhereHas('toko', function ($tq) use ($search) {
                      $tq->where('nama_toko', 'like', "%$search%")
                         ->orWhere('kode_toko', 'like', "%$search%")
                         ->orWhere('area', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('area')) {
            $area = $request->area;
            $query->whereHas('toko', fn ($q) => $q->where('area', $area));
        }

        if ($request->filled('status_konten')) {
            $sk = $request->status_konten;
            if ($sk === 'Tidak Diketahui') {
                $query->where(function ($q) {
                    $q->whereNull('status_konten')
                      ->orWhere('status_konten', '')
                      ->orWhere('status_konten', 'Tidak Diketahui')
                      ->orWhereNotIn('status_konten', ['Sudah Terbaru', 'Belum Terbaru']);
                });
            } else {
                $query->where('status_konten', $sk);
            }
        }

        if ($request->boolean('baru_aktivasi')) {
            $query->where('tanggal_aktivasi', '>=', now()->subDays(30)->toDateString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $signages = $query->orderBy('nama_signage')->paginate(10)->withQueryString();

        return view('signage.index', compact('signages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modelSignage = \App\Models\ModelSignage::orderBy('nama_model', 'asc')->get();
        $toko = \App\Models\Toko::orderBy('nama_toko', 'asc')->get();  

        return view('signage.create', compact('modelSignage', 'toko'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Cek apakah device_id ini sudah ada secara global di database (tanpa batasan tenant)
        $existingGlobal = DigitalSignage::withoutGlobalScopes()
            ->where('device_id', $request->device_id)
            ->with('toko')
            ->first();

        // Jika device_id sudah ada dan sudah dimiliki oleh client lain yang aktif (bukan milik tenant ini)
        if ($existingGlobal && !empty($existingGlobal->client_id) && (int) $existingGlobal->client_id !== (int) \App\Support\Tenant::id()) {
            $namaSignage = $existingGlobal->nama_signage;
            return back()->withInput()->withErrors([
                'device_id' => "Device ID '{$request->device_id}' sudah terdaftar dan aktif di client/domain lain! Tidak dapat menggunakan ID yang sama."
            ]);
        }

        // Jika perangkat ditemukan tapi milik client ini atau belum bertuan (pending/global domain utama),
        // kita izinkan untuk diklaim/dipindahkan ke subdomain ini.
        $pending = $existingGlobal ? $existingGlobal : null;

        // Cek duplikat khusus di dalam tenant yang sama (jika bukan perangkat yang sedang di-claim ulang)
        $existingInTenant = DigitalSignage::where('device_id', $request->device_id)
            ->when($pending, fn ($q) => $q->where('id', '!=', $pending->id))
            ->first();

        if ($existingInTenant) {
            return back()->withInput()->withErrors([
                'device_id' => "Device ID '{$request->device_id}' sudah terdaftar pada signage di toko ini."
            ]);
        }

        $validated = $request->validate([
            'nama_signage'      => 'required|string|max:255',
            'device_id'         => ['required', 'string', 'max:100'],
            'serial_number'     => 'nullable|string|max:255',
            'toko_id'           => ['nullable', \App\Support\Tenant::exists('toko')],
            'model_signage_id'  => 'nullable|exists:model_signage,id',
            'resolusi_width'    => 'nullable|integer|min:1',
            'resolusi_height'   => 'nullable|integer|min:1',
            'orientasi'         => 'nullable|in:Landscape Kiri,Landscape Kanan,Portrait',
            'status'            => 'nullable|in:Aktif,Nonaktif,Maintenance',
        ], [
            'device_id.required'    => 'Device ID wajib diisi.',
            'nama_signage.required' => 'Nama signage wajib diisi.',
        ]);

        try {
            $token = $pending?->device_token
                ?: \Illuminate\Support\Facades\Cache::pull('deleted_device_token:' . $validated['device_id'])
                ?: null; // kosong = token dari aplikasi TV diadopsi saat ping pertama (DeviceAuth)

            // Gunakan claimOrCreate dengan parameter array tunggal agar sesuai dengan definisi model
            DigitalSignage::claimOrCreate([
                'device_id'        => $validated['device_id'],
                'nama_signage'     => $validated['nama_signage'],
                'serial_number'    => $validated['serial_number'] ?? $validated['device_id'],
                'device_token'     => $token,
                'client_id'        => \App\Support\Tenant::id(), // Memindahkan/mengikat ke client subdomain aktif
                'toko_id'          => $validated['toko_id'] ?? null,
                'model_signage_id' => $validated['model_signage_id'] ?? null,
                'resolusi_width'   => $validated['resolusi_width'] ?? 1920,
                'resolusi_height'  => $validated['resolusi_height'] ?? 1080,
                'orientasi'        => $validated['orientasi'] ?? 'Landscape Kiri',
                'status'           => $validated['status'] ?? 'Aktif',
                'status_konten'    => 'Tidak Diketahui',
                'koneksi_status'   => 'offline',
                'last_ping'        => null,
                'tanggal_aktivasi' => now()->toDateString(),
                'versi_konten'     => 'v1.0',
                'unduh_terakhir'   => null,
                'capture_terakhir' => null,
                'needs_update'     => 0,
            ]);

            return redirect()->route('signage.index')
                             ->with('success', "Signage '{$validated['nama_signage']}' berhasil diaktifkan di subdomain ini.");
        } catch (\Exception $e) {
            Log::error('DigitalSignage store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalSignage $signage)
    {
        $signage->load(['toko', 'modelSignage', 'distribusiJadwal.jadwalTayang.fileIklan']);
        return view('signage.show', compact('signage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalSignage $signage)
    {
        $signage->load(['toko', 'modelSignage']);
        
        $toko = \App\Models\Toko::orderBy('nama_toko', 'asc')->get();
        $modelSignage = \App\Models\ModelSignage::orderBy('nama_model', 'asc')->get();

        return view('signage.edit', compact('signage', 'toko', 'modelSignage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalSignage $signage)
    {
        $existing = DigitalSignage::withoutGlobalScopes()
            ->where('device_id', $request->device_id)
            ->where('id', '!=', $signage->id)
            ->with('toko')
            ->first();

        if ($existing) {
            $namaSignage = $existing->nama_signage;
            $namaToko = $existing->toko?->nama_toko;
            $lokasiInfo = $namaToko ? "'$namaSignage' (Toko: $namaToko)" : "'$namaSignage'";
            return back()->withInput()->withErrors([
                'device_id' => "Device ID '{$request->device_id}' sudah terdaftar pada signage {$lokasiInfo}! Tidak dapat menggunakan ID signage yang sama."
            ]);
        }

        $validated = $request->validate([
            'nama_signage'      => 'required|string|max:255',
            'device_id'         => 'required|string|max:100|unique:digital_signage,device_id,' . $signage->id,
            'serial_number'     => 'nullable|string|max:255',
            'toko_id'           => ['nullable', \App\Support\Tenant::exists('toko')],
            'model_signage_id'  => 'nullable|exists:model_signage,id',
            'resolusi_width'    => 'nullable|integer|min:1',
            'resolusi_height'   => 'nullable|integer|min:1',
            'orientasi'         => 'nullable|in:Landscape Kiri,Landscape Kanan,Portrait',
            'status'            => 'nullable|in:Aktif,Nonaktif,Maintenance',
        ], [
            'device_id.unique' => 'Device ID ini sudah digunakan oleh signage lain.',
        ]);

        try {
            $signage->update($validated);

            return redirect()->route('signage.index')
                             ->with('success', "Signage '{$signage->nama_signage}' berhasil diperbarui.");
        } catch (\Exception $e) {
            Log::error('DigitalSignage update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalSignage $signage)
    {
        try {
            $signage->distribusiJadwal()->delete();
            $signageName = $signage->nama_signage;

            if ($signage->device_token) {
                \Illuminate\Support\Facades\Cache::forever('deleted_device_token:' . $signage->device_id, $signage->device_token);
            }
            $signage->delete();

            return redirect()->route('signage.index')
                             ->with('success', "Signage '{$signageName}' berhasil dihapus.");
        } catch (\Exception $e) {
            Log::error('DigitalSignage destroy error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}