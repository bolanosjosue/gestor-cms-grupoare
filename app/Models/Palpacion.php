<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Palpacion extends Model
{
    use Auditable;

    protected $table = 'palpaciones';

    protected $fillable = [
        'animal_id',
        'fecha',
        'cc',
        'od',
        'oi',
        'ut',
        'diagnostico',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'cc'    => 'decimal:2',
            'fecha' => 'date',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}
