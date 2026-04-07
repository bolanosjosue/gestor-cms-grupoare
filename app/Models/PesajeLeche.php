<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesajeLeche extends Model
{
    use Auditable;

    protected $table = 'pesajes_leche';

    protected $fillable = [
        'animal_id',
        'fecha',
        'peso_am',
        'peso_pm',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'peso_am' => 'decimal:2',
            'peso_pm' => 'decimal:2',
            'fecha'   => 'date',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }

    public function getTotalAttribute(): float
    {
        return round(($this->peso_am ?? 0) + ($this->peso_pm ?? 0), 2);
    }
}
