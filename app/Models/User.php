<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Auditable, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_super_admin',
        'activo',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function permisos()
    {
        return $this->hasMany(PermisoUsuario::class);
    }

    public function permisosCamposAnimales()
    {
        return $this->hasMany(PermisoCampoAnimal::class);
    }

    public function importacionesAnimales()
    {
        return $this->hasMany(ImportacionAnimal::class);
    }

    public function tienePermiso(string $modulo, string $accion = 'puede_ver'): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $permiso = $this->permisos->firstWhere('modulo', $modulo);

        return $permiso && $permiso->{$accion};
    }

    /**
     * Check if user has a preset role.
     */
    public function tieneRol(?string $rol = null): bool
    {
        if ($rol) {
            return $this->rol === $rol;
        }
        return !empty($this->rol);
    }

    /**
     * Check if user with a role can access the given app section.
     * supervisor  → pesos
     * encargado   → pesajes
     * veterinario → veterinario
     */
    public function puedeAccederApp(string $seccion): bool
    {
        if ($this->is_super_admin) return true;

        return match ($this->rol) {
            'supervisor'  => $seccion === 'pesos',
            'encargado'   => $seccion === 'pesajes',
            'veterinario' => $seccion === 'veterinario',
            default       => $this->tienePermiso('animales'),
        };
    }

    public function getCamposEditablesAnimales(): array
    {
        if ($this->is_super_admin) {
            return [
                'codigo_practico', 'identificacion_electronica', 'nombre', 'sexo',
                'agropecuaria', 'estado', 'composicion_racial', 'ultimo_peso',
                'estandarizacion_produccion', 'ultima_locacion', 'clasificacion_asociacion',
                'estado_reproductivo', 'numero_revisiones', 'fecha_ultimo_servicio',
                'fecha_ultimo_parto', 'fecha_secado', 'codigo_reproductor', 'codigo',
                'codigo_nombre', 'fecha_nacimiento', 'padre_nombre', 'codigo_madre',
            ];
        }

        return $this->permisosCamposAnimales()
            ->where('puede_editar', true)
            ->pluck('campo')
            ->toArray();
    }
}
