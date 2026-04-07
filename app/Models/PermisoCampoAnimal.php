<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoCampoAnimal extends Model
{
    protected $table = 'permisos_campos_animales';

    protected $fillable = [
        'user_id',
        'campo',
        'puede_editar',
    ];

    protected function casts(): array
    {
        return [
            'puede_editar' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
