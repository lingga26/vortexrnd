<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\JadwalSnapshot;
use App\Support\MediaStorage;
use App\Support\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Pindahkan media client dari struktur lama {client_id}/iklan ke {subdomain}/iklan
 * dan perbarui file_iklan.file_path.
 *
 *   php artisan media:pindah-subdomain --dry-run   (hanya tampilkan rencana)
 *   php artisan media:pindah-subdomain             (jalankan)
 */
class MediaPindahSubdomain extends Command
{
    protected $signature = 'media:pindah-subdomain
                            {--dry-run : Hanya tampilkan rencana, tidak mengubah apa pun}
                            {--tanpa-distribusi : Jangan distribusi ulang jadwal setelah pemindahan}';

    protected $description = 'Pindahkan folder media client dari {client_id}/iklan ke {subdomain}/iklan';

    public function handle(): int
    {
        $dry  = (bool) $this->option('dry-run');
        $base = MediaStorage::base();

        if ($base === '' || !is_dir($base)) {
            $this->error("Folder media tidak ditemukan: {$base} (cek MEDIA_PATH di .env)");
            return self::FAILURE;
        }

        $dipindah = 0;
        $dilewati = 0;
        $dbSaja   = 0;
        $klienDiubah = [];

        foreach (Client::where('id', '!=', 1)->whereNotNull('subdomain')->get() as $client) {
            $lama = $client->id . '/iklan';
            $baru = MediaStorage::subFor($client);

            if ($lama === $baru) {
                continue;
            }

            $rows = DB::table('file_iklan')
                ->where('client_id', $client->id)
                ->where('file_path', 'like', $lama . '/%')
                ->get(['id', 'file_path']);

            if ($rows->isEmpty()) {
                continue;
            }

            $this->info("Client {$client->id} ({$client->subdomain}): {$lama}  ->  {$baru}  [{$rows->count()} file]");

            foreach ($rows as $row) {
                $namaFile = basename($row->file_path);
                $relBaru  = $baru . '/' . $namaFile;
                $src      = $base . '/' . $row->file_path;
                $dst      = $base . '/' . $relBaru;

                if (!is_file($src)) {
                    // File fisik sudah tidak ada: cukup selaraskan path di database.
                    $this->warn("  - file fisik tidak ada, path database saja: {$row->file_path}");
                    if (!$dry) {
                        DB::table('file_iklan')->where('id', $row->id)->update(['file_path' => $relBaru]);
                    }
                    $dbSaja++;
                    continue;
                }

                if (is_file($dst)) {
                    $this->warn("  - dilewati, tujuan sudah ada: {$relBaru}");
                    $dilewati++;
                    continue;
                }

                $this->line("  - {$row->file_path}  ->  {$relBaru}");

                if ($dry) {
                    $dipindah++;
                    continue;
                }

                if (!is_dir(dirname($dst)) && !@mkdir(dirname($dst), 0775, true)) {
                    $this->error('    gagal membuat folder tujuan');
                    $dilewati++;
                    continue;
                }

                if (!@rename($src, $dst)) {
                    $this->error('    gagal memindahkan file (cek izin folder)');
                    $dilewati++;
                    continue;
                }

                DB::table('file_iklan')->where('id', $row->id)->update(['file_path' => $relBaru]);
                $dipindah++;
            }

            if (!$dry) {
                MediaStorage::rapikanFolder($lama . '/x');
                $klienDiubah[] = $client;
            }
        }

        $this->newLine();
        $this->info(($dry ? '[DRY-RUN] ' : '') . "Dipindah: {$dipindah}, hanya database: {$dbSaja}, dilewati: {$dilewati}");

        if (!$dry && ($dipindah + $dbSaja) > 0 && !$this->option('tanpa-distribusi')) {
            $total = 0;

            // Per client: Tenant::set() membuat global scope client memakai client yang tepat,
            // baik dijalankan dari terminal maupun dari halaman perawatan (superadmin).
            foreach ($klienDiubah as $client) {
                try {
                    Tenant::set($client);
                    $total += JadwalSnapshot::distributeAll();
                } catch (\Throwable $e) {
                    $this->warn("Distribusi ulang client {$client->subdomain} gagal, lakukan manual lewat tombol Distribusi Ulang Semua: " . $e->getMessage());
                } finally {
                    Tenant::set(null);
                }
            }

            $this->info("Distribusi ulang jadwal: {$total} distribusi diperbarui (URL video baru dikirim ke TV).");
        }

        return self::SUCCESS;
    }
}
