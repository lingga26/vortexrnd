<?php

namespace App\Support;

use App\Models\Client;

/**
 * Satu pintu untuk lokasi file media iklan.
 *
 * Struktur folder di MEDIA_PATH:
 *   iklan/xxx.mp4              -> client 1 / domain utama (data lama)
 *   {subdomain}/iklan/xxx.mp4  -> client lain, sesuai nama subdomain (mis. kalibrasi/iklan)
 *
 * Nilai kolom file_iklan.file_path selalu relatif terhadap MEDIA_PATH.
 * File lama hasil `storage:link` (storage/app/public/iklan/...) tetap dikenali
 * agar ikut terhapus dan tetap bisa diputar.
 */
class MediaStorage
{
    /** Folder dasar media (MEDIA_PATH di .env). */
    public static function base(): string
    {
        return rtrim((string) config('media.path'), '/');
    }

    /** Lokasi lama: storage/app/public (dilayani lewat public/storage). */
    public static function legacyBase(): string
    {
        return rtrim(storage_path('app/public'), '/');
    }

    /** Ubah nama subdomain menjadi nama folder yang aman. */
    public static function slug(string $subdomain): string
    {
        return preg_replace('/[^a-z0-9-]/', '', strtolower($subdomain));
    }

    /** Sub-folder (relatif ke base) untuk satu client. */
    public static function subFor(?Client $client): string
    {
        if (!$client || (int) $client->id === 1) {
            return 'iklan';
        }

        $slug = filled($client->subdomain) ? self::slug((string) $client->subdomain) : '';

        // Client tanpa subdomain memakai id (sama seperti struktur lama).
        return ($slug !== '' ? $slug : (string) $client->id) . '/iklan';
    }

    /** Sub-folder untuk client yang sedang aktif (subdomain / user yang login). */
    public static function subAktif(): string
    {
        return self::subFor(Tenant::current());
    }

    /** Folder fisik tujuan upload untuk client yang sedang aktif. */
    public static function dirAktif(): string
    {
        return self::base() . '/' . self::subAktif();
    }

    /** Path relatif yang aman, atau null bila mencurigakan (mengandung segmen "..") */
    private static function bersih(?string $rel): ?string
    {
        $rel = ltrim(str_replace('\\', '/', (string) $rel), '/');

        if ($rel === '') {
            return null;
        }

        foreach (explode('/', $rel) as $segmen) {
            if ($segmen === '..') {
                return null;
            }
        }

        return $rel;
    }

    /** Semua lokasi fisik yang mungkin untuk file_path ini. */
    public static function kandidat(?string $rel): array
    {
        $rel = self::bersih($rel);

        if ($rel === null) {
            return [];
        }

        return array_values(array_filter([
            self::base() !== '' ? self::base() . '/' . $rel : null,
            self::legacyBase() . '/' . $rel,
        ]));
    }

    /** Path fisik pertama yang benar-benar ada, atau null. */
    public static function resolve(?string $rel): ?string
    {
        foreach (self::kandidat($rel) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /** URL publik file (mengikuti host yang sedang dibuka), atau null bila file tidak ada. */
    public static function url(?string $rel): ?string
    {
        $rel = self::bersih($rel);

        if ($rel === null) {
            return null;
        }

        if (self::base() !== '' && is_file(self::base() . '/' . $rel)) {
            return url('media/' . $rel);
        }

        if (is_file(self::legacyBase() . '/' . $rel)) {
            return url('storage/' . $rel);
        }

        return null;
    }

    /**
     * Hapus file fisik di semua lokasi yang mungkin.
     * Mengembalikan ['ditemukan' => int, 'terhapus' => int, 'gagal' => int].
     */
    public static function hapus(?string $rel): array
    {
        $hasil = ['ditemukan' => 0, 'terhapus' => 0, 'gagal' => 0];

        foreach (self::kandidat($rel) as $path) {
            if (!is_file($path)) {
                continue;
            }

            $hasil['ditemukan']++;

            if (@unlink($path)) {
                $hasil['terhapus']++;
            } else {
                $hasil['gagal']++;
            }
        }

        self::rapikanFolder($rel);

        return $hasil;
    }

    /** Hapus folder subdomain yang sudah kosong (rmdir hanya berhasil bila kosong). */
    public static function rapikanFolder(?string $rel): void
    {
        $rel = self::bersih($rel);

        if ($rel === null || self::base() === '') {
            return;
        }

        $dir = dirname($rel);

        while ($dir !== '.' && $dir !== '' && $dir !== 'iklan') {
            @rmdir(self::base() . '/' . $dir);
            $dir = dirname($dir);
        }
    }
}
