<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoUsuario extends Model
{
    protected $table = 'permisos_usuario';

    protected $fillable = [
        'user_id',
        'modulo',
        'puede_ver',
        'puede_agregar',
        'puede_editar',
        'puede_eliminar',
    ];

    protected function casts(): array
    {
        return [
            'puede_ver' => 'boolean',
            'puede_agregar' => 'boolean',
            'puede_editar' => 'boolean',
            'puede_eliminar' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
