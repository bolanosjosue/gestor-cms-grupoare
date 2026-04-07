<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacionAnimal extends Model
{
    protected $table = 'importaciones_animales';

    protected $fillable = [
        'user_id',
        'session_uuid',
        'nombre_archivo',
        'total_registros',
        'insertados',
        'actualizados',
        'con_error',
        'lotes_total',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
