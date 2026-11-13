<?php

namespace App\Http\Controllers;

use App\Support\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Halaman perawatan media untuk superadmin (pengganti terminal).
 * Hanya menjalankan perintah artisan yang ada di daftar putih di bawah.
 */
class MediaToolController extends Controller
{
    private const AKSI = [
        'yatim-cek'       => ['media:bersihkan-yatim', []],
        'yatim-hapus'     => ['media:bersihkan-yatim', ['--hapus' => true]],
        'pindah-cek'      => ['media:pindah-subdomain', ['--dry-run' => true]],
        'pindah-jalankan' => ['media:pindah-subdomain', []],
    ];

    private const JUDUL = [
        'yatim-cek'       => 'Cek file yatim',
        'yatim-hapus'     => 'Hapus file yatim',
        'pindah-cek'      => 'Cek rencana pemindahan folder',
        'pindah-jalankan' => 'Jalankan pemindahan folder',
    ];

    public function index()
    {
        $lokasi = [];

        foreach ([
            'Folder media (MEDIA_PATH)'           => MediaStorage::base(),
            'Folder lama (storage/app/public)'    => MediaStorage::legacyBase(),
        ] as $nama => $path) {
            $lokasi[] = [
                'nama'     => $nama,
                'path'     => $path,
                'ada'      => $path !== '' && is_dir($path),
                'bisa_tulis' => $path !== '' && is_dir($path) && is_writable($path),
            ];
        }

        return view('perawatan.index', [
            'lokasi' => $lokasi,
            'hasil'  => session('hasil'),
            'judul'  => self::JUDUL,
        ]);
    }

    public function jalankan(Request $request)
    {
        $aksi = (string) $request->input('aksi');

        abort_unless(isset(self::AKSI[$aksi]), 422, 'Aksi tidak dikenal.');

        @set_time_limit(600);
        @ignore_user_abort(true);

        [$perintah, $opsi] = self::AKSI[$aksi];

        try {
            $kode   = Artisan::call($perintah, $opsi);
            $output = trim(Artisan::output());
        } catch (\Throwable $e) {
            $kode   = 1;
            $output = 'Error: ' . $e->getMessage();
        }

        Log::info('Perawatan media dijalankan', [
            'aksi' => $aksi,
            'user' => auth()->id(),
            'kode' => $kode,
        ]);

        if (mb_strlen($output) > 20000) {
            $output = mb_substr($output, 0, 20000) . "\n... (output dipotong)";
        }

        return redirect()->route('perawatan.index')->with('hasil', [
            'aksi'   => $aksi,
            'kode'   => $kode,
            'output' => $output === '' ? '(tidak ada output)' : $output,
        ]);
    }
}
