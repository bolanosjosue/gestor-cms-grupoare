<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesaje extends Model
{
    use Auditable;

    protected $table = 'pesajes';

    protected $fillable = [
        'animal_id',
        'peso',
        'fecha',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'peso'  => 'decimal:2',
            'fecha' => 'date',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}
