<?php

namespace App\Console\Commands;

use App\Support\MediaStorage;
use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Cari (dan hapus) file media yang tidak lagi dipakai record file_iklan mana pun,
 * misalnya iklan yang sudah dihapus tetapi file fisiknya tertinggal di server.
 *
 *   php artisan media:bersihkan-yatim            (hanya daftar, aman)
 *   php artisan media:bersihkan-yatim --hapus    (hapus sungguhan)
 */
class MediaBersihkanYatim extends Command
{
    protected $signature = 'media:bersihkan-yatim
                            {--hapus : Hapus file yatim (tanpa opsi ini hanya ditampilkan)}
                            {--umur=60 : Abaikan file yang diubah kurang dari N menit lalu (upload yang sedang berjalan)}';

    protected $description = 'Daftar/hapus file media di server yang sudah tidak ada di database';

    private const EKSTENSI = ['mp4', 'avi', 'mov', 'mkv', 'webm', 'jpg', 'jpeg', 'png'];

    public function handle(): int
    {
        $hapus = (bool) $this->option('hapus');
        $umur  = max(0, (int) $this->option('umur')) * 60;

        // Semua file_path yang masih dipakai (tanpa scope client).
        $dipakai = DB::table('file_iklan')
            ->whereNotNull('file_path')
            ->pluck('file_path')
            ->map(fn ($p) => ltrim(str_replace('\\', '/', (string) $p), '/'))
            ->flip()
            ->all();

        $lokasi = [
            'media'   => MediaStorage::base(),
            'storage' => MediaStorage::legacyBase(),
        ];

        $total = 0;
        $ukuran = 0;
        $gagal = 0;

        foreach ($lokasi as $label => $root) {
            if ($root === '' || !is_dir($root)) {
                continue;
            }

            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($it as $f) {
                if (!$f->isFile()) {
                    continue;
                }

                $rel = ltrim(str_replace('\\', '/', substr($f->getPathname(), strlen($root))), '/');

                // Hanya folder iklan ({..}/iklan/...), file lain di storage tidak disentuh.
                if (!(str_starts_with($rel, 'iklan/') || str_contains($rel, '/iklan/'))) {
                    continue;
                }

                if (!in_array(strtolower($f->getExtension()), self::EKSTENSI, true)) {
                    continue;
                }

                if (isset($dipakai[$rel])) {
                    continue;
                }

                if ($umur > 0 && (time() - $f->getMTime()) < $umur) {
                    continue;
                }

                $total++;
                $ukuran += $f->getSize();
                $this->line(sprintf('[%s] %s  (%s)', $label, $rel, $this->ukuran($f->getSize())));

                if ($hapus) {
                    if (@unlink($f->getPathname())) {
                        if ($label === 'media') {
                            MediaStorage::rapikanFolder($rel);
                        }
                    } else {
                        $gagal++;
                        $this->error('  gagal dihapus (cek izin)');
                    }
                }
            }
        }

        $this->newLine();

        if ($total === 0) {
            $this->info('Tidak ada file yatim.');
            return self::SUCCESS;
        }

        if ($hapus) {
            $this->info(sprintf('Dihapus: %d file (%s), gagal: %d', $total - $gagal, $this->ukuran($ukuran), $gagal));
        } else {
            $this->info(sprintf('Ditemukan %d file yatim (%s). Jalankan dengan --hapus untuk menghapusnya.', $total, $this->ukuran($ukuran)));
        }

        return self::SUCCESS;
    }

    private function ukuran(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
