<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = 'animales';

    protected $fillable = [
        'agropecuaria',
        'codigo_practico',
        'estado',
        'identificacion_electronica',
        'fecha_nacimiento',
        'padre_nombre',
        'codigo_madre',
        'ultima_locacion',
        'composicion_racial',
        'clasificacion_asociacion',
        'ultimo_peso',
        'estandarizacion_produccion',
        'fecha_ultimo_servicio',
        'estado_reproductivo',
        'numero_revisiones',
        'fecha_ultimo_parto',
        'fecha_secado',
        'nombre',
        'sexo',
        'codigo_reproductor',
        'codigo',
        'codigo_nombre',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_ultimo_servicio' => 'date',
            'fecha_ultimo_parto' => 'date',
            'fecha_secado' => 'date',
            'ultimo_peso' => 'decimal:2',
            'numero_revisiones' => 'integer',
        ];
    }

    public function pesajes()
    {
        return $this->hasMany(Pesaje::class);
    }

    public function pesajesLeche()
    {
        return $this->hasMany(PesajeLeche::class);
    }

    public function palpaciones()
    {
        return $this->hasMany(Palpacion::class);
    }
}
