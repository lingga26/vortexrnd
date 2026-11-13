<?php

namespace App\Http\Controllers;

use App\Models\FileIklan;
use Illuminate\Http\Request;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileIklanController extends Controller
{
    // Lokasi media diatur terpusat di App\Support\MediaStorage:
    //   client 1 / domain utama -> {MEDIA_PATH}/iklan
    //   client subdomain        -> {MEDIA_PATH}/{subdomain}/iklan
    private function mediaSub(): string
    {
        return MediaStorage::subAktif();
    }

    private function mediaDir(): string
    {
        return MediaStorage::dirAktif();
    }

    public function index(Request $request)
    {
        $query = FileIklan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_iklan', 'like', "%$search%")
                  ->orWhere('pelanggan', 'like', "%$search%")
                  ->orWhere('tipe', 'like', "%$search%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $fileIklan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('file-iklan.index', compact('fileIklan'));
    }

    public function create()
    {
        return view('file-iklan.create');
    }

    public function store(Request $request)
    {
        Log::info('=== UPLOAD ATTEMPT ===', [
            'has_file' => $request->hasFile('file_video'),
        ]);

        $validated = $request->validate([
            'pelanggan'     => 'required|string|max:255',
            'tipe'          => 'required|in:CONTENT,BUMPER,PROMO',
            'nama_iklan'    => 'required|string|max:255',
            'durasi_detik'  => 'nullable|integer|min:1',
            'catatan'       => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'file_video'    => 'required|file|mimes:mp4,avi,mov,mkv|max:122880',
        ], [
            'file_video.required' => 'File video wajib diupload.',
            'file_video.file'     => 'Harap upload file video yang valid.',
            'file_video.mimes'    => 'Format video harus MP4, AVI, MOV, atau MKV.',
            'file_video.max'      => 'Ukuran video tidak boleh lebih dari 120 MB.',
            'file_video.uploaded' => 'File video gagal terupload (melebihi limit upload PHP hosting).',
        ]);

        try {
            $catatanValue = $request->input('catatan') ?? $request->input('keterangan') ?? null;
            $data = [
                'pelanggan'    => $validated['pelanggan'],
                'tipe'         => $validated['tipe'],
                'nama_iklan'   => $validated['nama_iklan'],
                'durasi_detik' => $validated['durasi_detik'] ?? null,
                'catatan'      => $catatanValue,
                'has_video'    => false,
                'aktif'        => 1,
            ];

            if ($request->hasFile('file_video')) {
                $file = $request->file('file_video');
                
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $_FILES['file_video']['size'];
                
                $fileName = time() . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
                
                // ✅ Gunakan path absolut ke public_html (bukan public_path() Laravel)
                $destinationPath = $this->mediaDir();
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }
                
                $tmpPath = $file->getRealPath();
                $fullPath = $destinationPath . '/' . $fileName;
                
                if (!move_uploaded_file($tmpPath, $fullPath)) {
                    throw new \Exception('Gagal memindahkan file upload.');
                }
                
                // Path untuk database: relatif ke public_html/media/
                $path = $this->mediaSub() . '/' . $fileName;

                Log::info('File uploaded successfully', [
                    'path' => $path, 
                    'size' => $fileSize,
                    'full_path' => $fullPath
                ]);

                $data['file_path']    = $path;
                $data['file_name']    = $fileName;
                $data['ukuran_bytes'] = $fileSize;
                $data['has_video']    = true;
            } else {
                throw new \Exception('File video tidak terdeteksi.');
            }

            $fileIklan = FileIklan::create($data);
            
            Log::info('Record created', ['id' => $fileIklan->id]);

            return redirect()->route('file-iklan.index')
                             ->with('success', 'File iklan berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return back()
                ->withErrors(['file_video' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(FileIklan $fileIklan)
    {
        return view('file-iklan.edit', compact('fileIklan'));
    }

    public function update(Request $request, FileIklan $fileIklan)
    {
        Log::info('=== UPDATE ATTEMPT ===', ['id' => $fileIklan->id]);

        $validated = $request->validate([
            'pelanggan'    => 'required|string|max:255',
            'tipe'         => 'required|in:CONTENT,BUMPER,PROMO',
            'nama_iklan'   => 'required|string|max:255',
            'durasi_detik' => 'nullable|integer|min:1',
            'catatan'      => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'file_video'   => 'nullable|file|mimes:mp4,avi,mov,mkv|max:122880',
        ], [
            'file_video.file'     => 'Harap upload file video yang valid.',
            'file_video.mimes'    => 'Format video harus MP4, AVI, MOV, atau MKV.',
            'file_video.max'      => 'Ukuran video tidak boleh lebih dari 120 MB.',
            'file_video.uploaded' => 'File video gagal terupload (melebihi limit upload PHP hosting).',
        ]);

        try {
            $catatanValue = $request->input('catatan') ?? $request->input('keterangan') ?? null;
            $data = [
                'pelanggan'    => $validated['pelanggan'],
                'tipe'         => $validated['tipe'],
                'nama_iklan'   => $validated['nama_iklan'],
                'durasi_detik' => $validated['durasi_detik'] ?? null,
                'catatan'      => $catatanValue,
            ];

            $fileLama = null;

            if ($request->hasFile('file_video')) {
                // Hapus file lama (di folder media maupun lokasi lama storage/app/public)
                $fileLama = $fileIklan->file_path;

                $file = $request->file('file_video');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $_FILES['file_video']['size'];
                
                $fileName = time() . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;
                
                $destinationPath = $this->mediaDir();
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }
                
                $tmpPath = $file->getRealPath();
                $fullPath = $destinationPath . '/' . $fileName;
                
                if (!move_uploaded_file($tmpPath, $fullPath)) {
                    throw new \Exception('Gagal memindahkan file upload.');
                }
                
                $path = $this->mediaSub() . '/' . $fileName;

                $data['file_path']    = $path;
                $data['file_name']    = $fileName;
                $data['ukuran_bytes'] = $fileSize;
                $data['has_video']    = true;
                
                Log::info('New file uploaded', ['path' => $path]);
            }

            $fileIklan->update($data);

            if (!empty($fileLama) && $fileLama !== ($data['file_path'] ?? null)) {
                $this->hapusFileFisik($fileLama);
            }
            Log::info('Record updated', ['id' => $fileIklan->id]);

            return redirect()->route('file-iklan.index')
                             ->with('success', 'File iklan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            
            return back()
                ->withErrors(['file_video' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(FileIklan $fileIklan)
    {
        try {
            $path = $fileIklan->file_path;

            // Hapus data dulu; file fisik baru dihapus setelah data berhasil terhapus.
            $fileIklan->delete();
            Log::info('Record deleted', ['id' => $fileIklan->id]);

            $hasil = $this->hapusFileFisik($path);

            $pesan = 'File iklan berhasil dihapus.';
            if ($hasil['gagal'] > 0) {
                $pesan .= ' Peringatan: file fisik gagal dihapus dari server (cek izin folder media).';
            }

            return redirect()->route('file-iklan.index')->with('success', $pesan);

        } catch (\Exception $e) {
            Log::error('Delete error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus file fisik bila tidak lagi dipakai record lain.
     * Mencari di MEDIA_PATH dan di storage/app/public (lokasi lama).
     */
    private function hapusFileFisik(?string $path): array
    {
        $kosong = ['ditemukan' => 0, 'terhapus' => 0, 'gagal' => 0];

        if (empty($path)) {
            return $kosong;
        }

        // Query langsung (tanpa scope client): jangan hapus bila masih dipakai record mana pun.
        if (DB::table('file_iklan')->where('file_path', $path)->exists()) {
            return $kosong;
        }

        $hasil = MediaStorage::hapus($path);
        Log::info('File fisik dihapus', ['path' => $path] + $hasil);

        return $hasil;
    }
}
