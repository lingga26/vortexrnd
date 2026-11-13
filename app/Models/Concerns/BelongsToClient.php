<?php

namespace App\Models\Concerns;

use App\Models\Client;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToClient
{
    public static function bootBelongsToClient(): void
    {
        static::addGlobalScope('client', function (Builder $builder) {
            // Gunakan Tenant::id() agar mengikuti subdomain yang sedang dibuka
            if (auth()->check()) {
                $clientId = Tenant::id();
                $table = $builder->getModel()->getTable();

                if ($clientId !== null) {
                    $builder->where($table . '.client_id', $clientId);
                } else {
                    $builder->whereRaw('1 = 0');
                }
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->client_id)) {
                $clientId = Tenant::id();
                if ($clientId !== null) {
                    $model->client_id = $clientId;
                }
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}