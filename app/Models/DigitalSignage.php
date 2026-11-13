<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use App\Support\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalSignage extends Model
{
    use HasFactory, BelongsToClient;

    protected $table = 'digital_signage';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_aktivasi' => 'date',
        'unduh_terakhir'   => 'datetime',
        'last_ping'        => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Otomatis ubah device_id menjadi HURUF BESAR dan hapus spasi.
     */
    public function setDeviceIdAttribute($value): void
    {
        $this->attributes['device_id'] = $value !== null
            ? strtoupper(trim((string) $value))
            : null;
    }

    /**
     * Pastikan saat dibaca dari model juga tampil HURUF BESAR.
     */
    public function getDeviceIdAttribute($value): ?string
    {
        return $value !== null ? strtoupper((string) $value) : null;
    }

    public function getTanggalAktivasiAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse($value);
    }

    public function getUnduhTerakhirAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse($value);
    }

    public function getLastPingAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse($value);
    }

    /**
     * Relasi ke Toko.
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    /**
     * Relasi ke Model Signage (TV).
     * Menggunakan withoutGlobalScopes() agar data master model_signage selalu terbaca,
     * serta withDefault() untuk fallback ke kolom device_model jika model_signage_id belum dipilih.
     */
    public function modelSignage()
    {
        return $this->belongsTo(ModelSignage::class, 'model_signage_id')
            ->withoutGlobalScopes()
            ->withDefault(function ($model, $signage) {
                if (!empty($signage->device_model)) {
                    $model->nama_model_tv = $signage->device_model;
                }
            });
    }

    /**
     * Relasi ke Distribusi Jadwal.
     */
    public function distribusiJadwal()
    {
        return $this->hasMany(DistribusiJadwal::class, 'signage_id');
    }

    public function distribusi()
    {
        return $this->hasMany(DistribusiJadwal::class, 'signage_id');
    }

    /**
     * Klaim perangkat yang sudah terdaftar tanpa client_id, atau buat/perbarui perangkat.
     */
    public static function claimOrCreate(array $attributes, ?int $clientId = null): self
    {
        $clientId = $clientId ?: Tenant::id();

        $deviceId = isset($attributes['device_id'])
            ? strtoupper(trim((string) $attributes['device_id']))
            : '';

        $attributes['device_id'] = $deviceId;

        if ($deviceId !== '') {
            $existing = static::withoutGlobalScopes()
                ->whereRaw('UPPER(device_id) = ?', [$deviceId])
                ->first();

            if ($existing) {
                if (empty($existing->client_id) && $clientId) {
                    $existing->client_id = $clientId;
                }

                // Jangan timpa token yang sudah ada dengan nilai kosong. Jika token memang
                // kosong, biarkan kosong: DeviceAuth akan mengadopsi token dari aplikasi TV
                // pada ping pertama sehingga TV tidak perlu didaftarkan ulang.
                if (empty($attributes['device_token'])) {
                    unset($attributes['device_token']);
                }

                $existing->fill($attributes);
                $existing->save();

                return $existing;
            }
        }

        if ($clientId) {
            $attributes['client_id'] = $clientId;
        }

        // Token sengaja tidak dibuat acak di sini (lihat catatan di atas).
        if (empty($attributes['device_token'])) {
            $attributes['device_token'] = null;
        }

        return static::create($attributes);
    }

    /**
     * Batas waktu (menit) sejak ping terakhir agar perangkat dianggap online.
     * Interval ping terpanjang 10 menit (webOS) + toleransi 5 menit.
     */
    public const ONLINE_MENIT = 15;

    public function isOnline(): bool
    {
        return $this->last_ping
            && $this->last_ping->gt(now()->subMinutes(self::ONLINE_MENIT));
    }
}