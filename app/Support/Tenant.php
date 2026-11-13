<?php

namespace App\Support;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

class Tenant
{
    protected static ?Client $currentClient = null;
    protected static bool $resolvedFromHost = false;

    /**
     * Set client aktif berdasarkan subdomain host yang sedang diakses.
     */
    public static function set(?Client $client): void
    {
        static::$currentClient = $client;
        static::$resolvedFromHost = ($client !== null);
    }

    /**
     * Ambil model Client yang sedang aktif.
     * Prioritas 1: Client dari subdomain yang sedang dibuka di browser (jika ada).
     * Prioritas 2: Client dari user yang sedang login (fallback di domain utama).
     */
    public static function current(): ?Client
    {
        if (static::$currentClient !== null) {
            return static::$currentClient;
        }

        // Coba deteksi langsung dari host request jika middleware belum menyetel
        $hostClient = static::resolveFromRequest();
        if ($hostClient !== null) {
            static::$currentClient = $hostClient;
            static::$resolvedFromHost = true;
            return static::$currentClient;
        }

        $user = Auth::user();
        if ($user && $user->client_id) {
            static::$currentClient = Client::find($user->client_id);
            return static::$currentClient;
        }

        return null;
    }

    /**
     * Ambil ID client aktif untuk dipakai oleh trait BelongsToClient.
     */
    public static function id(): ?int
    {
        // 1. Jika sedang berada di subdomain client (misal: lawson.vortexsign.web.id),
        //    selalu gunakan ID client milik subdomain tersebut!
        if (static::$currentClient !== null) {
            return (int) static::$currentClient->id;
        }

        $hostClient = static::resolveFromRequest();
        if ($hostClient !== null) {
            static::$currentClient = $hostClient;
            static::$resolvedFromHost = true;
            return (int) $hostClient->id;
        }

        // 2. Jika berada di domain utama (vortexsign.web.id), gunakan client_id milik user
        $user = Auth::user();
        if ($user && !empty($user->client_id)) {
            return (int) $user->client_id;
        }

        return null;
    }

    /**
     * Deteksi Client berdasarkan subdomain dari HTTP Request saat ini.
     */
    protected static function resolveFromRequest(): ?Client
    {
        if (!app()->bound('request')) {
            return null;
        }

        $request = request();
        $host = strtolower((string) $request->getHost());
        $baseHost = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));

        if ($baseHost === '' || $host === $baseHost || $host === 'www.' . $baseHost || $host === 'admin.' . $baseHost) {
            return null;
        }

        $suffix = '.' . $baseHost;
        if (str_ends_with($host, $suffix)) {
            $sub = substr($host, 0, -strlen($suffix));
            if ($sub !== '' && !str_contains($sub, '.')) {
                return Client::whereRaw('LOWER(subdomain) = ?', [$sub])->first();
            }
        }

        return null;
    }

    /**
     * Helper validasi exists terbatas pada client aktif.
     */
    public static function exists(string $table, string $column = 'id'): Exists
    {
        return Rule::exists($table, $column)->where(function ($query) {
            $clientId = static::id();
            if ($clientId !== null) {
                $query->where('client_id', $clientId);
            } else {
                $query->whereRaw('1 = 0');
            }
        });
    }

    /**
     * Helper validasi unique terbatas pada client aktif.
     */
    public static function unique(string $table, string $column = 'NULL', mixed $ignoreId = null): Unique
    {
        $rule = Rule::unique($table, $column)->where(function ($query) {
            $clientId = static::id();
            if ($clientId !== null) {
                $query->where('client_id', $clientId);
            }
        });

        if ($ignoreId !== null) {
            $rule->ignore($ignoreId);
        }

        return $rule;
    }
}