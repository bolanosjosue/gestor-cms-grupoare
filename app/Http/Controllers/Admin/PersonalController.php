<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermisoCampoAnimal;
use App\Models\PermisoUsuario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PersonalController extends Controller
{
    private const MODULOS = ['dashboard', 'articulos', 'personal', 'razas', 'ventas', 'animales', 'auditoria'];

    private const CAMPOS_ANIMALES = [
        'codigo_practico', 'identificacion_electronica', 'nombre', 'sexo',
        'agropecuaria', 'estado', 'composicion_racial', 'ultimo_peso',
        'estandarizacion_produccion', 'ultima_locacion', 'clasificacion_asociacion',
        'estado_reproductivo', 'numero_revisiones', 'fecha_ultimo_servicio',
        'fecha_ultimo_parto', 'fecha_secado', 'codigo_reproductor', 'codigo',
        'codigo_nombre', 'fecha_nacimiento', 'padre_nombre', 'codigo_madre',
    ];

    public function index(Request $request)
    {
        $query = User::query()->with('permisos');

        if ($request->filled('estado')) {
            $query->where('activo', $request->input('estado') === 'activo');
        }

        $usuarios = $query->orderByDesc('is_super_admin')->orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'usuarios' => $usuarios->map(function ($u) {
                    $rolLabel = match ($u->rol) {
                        'encargado'   => 'Encargado',
                        'veterinario' => 'Veterinario',
                        'supervisor'  => 'Supervisor',
                        default       => null,
                    };
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'username' => $u->username,
                        'is_super_admin' => $u->is_super_admin,
                        'activo' => $u->activo,
                        'rol' => $u->rol,
                        'rol_label' => $rolLabel,
                        'modulos_acceso' => $u->is_super_admin
                            ? self::MODULOS
                            : ($u->rol ? [] : $u->permisos->where('puede_ver', true)->pluck('modulo')->values()),
                    ];
                }),
            ]);
        }

        return view('admin.personal.index');
    }

    public function show(User $usuario)
    {
        $usuario->load('permisos', 'permisosCamposAnimales');

        $permisos = [];
        foreach (self::MODULOS as $modulo) {
            $p = $usuario->permisos->firstWhere('modulo', $modulo);
            $permisos[$modulo] = [
                'puede_ver' => $p ? $p->puede_ver : false,
                'puede_agregar' => $p ? $p->puede_agregar : false,
                'puede_editar' => $p ? $p->puede_editar : false,
                'puede_eliminar' => $p ? $p->puede_eliminar : false,
            ];
        }

        $camposAnimales = [];
        foreach (self::CAMPOS_ANIMALES as $campo) {
            $pc = $usuario->permisosCamposAnimales->firstWhere('campo', $campo);
            $camposAnimales[$campo] = $pc ? $pc->puede_editar : false;
        }

        return response()->json([
            'id' => $usuario->id,
            'name' => $usuario->name,
            'username' => $usuario->username,
            'is_super_admin' => $usuario->is_super_admin,
            'activo' => $usuario->activo,
            'rol' => $usuario->rol,
            'permisos' => $permisos,
            'campos_animales' => $camposAnimales,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_super_admin' => ['boolean'],
            'activo' => ['boolean'],
            'rol' => ['nullable', 'in:encargado,veterinario,supervisor'],
            'permisos' => ['nullable', 'array'],
            'permisos.*.puede_ver' => ['boolean'],
            'permisos.*.puede_agregar' => ['boolean'],
            'permisos.*.puede_editar' => ['boolean'],
            'permisos.*.puede_eliminar' => ['boolean'],
            'campos_animales' => ['nullable', 'array'],
            'campos_animales.*' => ['boolean'],
        ], [
            'username.regex' => 'Solo letras, números, puntos, guiones y guiones bajos.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => $data['password'],
            'is_super_admin' => $data['is_super_admin'] ?? false,
            'activo' => $data['activo'] ?? true,
            'rol' => $data['rol'] ?? null,
        ]);

        if (empty($data['is_super_admin']) && empty($data['rol'])) {
            $this->syncPermisos($user, $data['permisos'] ?? []);
            $this->syncCamposAnimales($user, $data['campos_animales'] ?? []);
        }

        return response()->json(['message' => 'Usuario creado correctamente.', 'id' => $user->id], 201);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($usuario->id), 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_super_admin' => ['boolean'],
            'activo' => ['boolean'],
            'rol' => ['nullable', 'in:encargado,veterinario,supervisor'],
            'permisos' => ['nullable', 'array'],
            'permisos.*.puede_ver' => ['boolean'],
            'permisos.*.puede_agregar' => ['boolean'],
            'permisos.*.puede_editar' => ['boolean'],
            'permisos.*.puede_eliminar' => ['boolean'],
            'campos_animales' => ['nullable', 'array'],
            'campos_animales.*' => ['boolean'],
        ], [
            'username.regex' => 'Solo letras, números, puntos, guiones y guiones bajos.',
        ]);

        if (!empty($data['name'])) {
            $usuario->name = $data['name'];
        }
        if (!empty($data['username'])) {
            $usuario->username = $data['username'];
        }
        if (array_key_exists('is_super_admin', $data)) {
            $usuario->is_super_admin = $data['is_super_admin'] ?? false;
        }
        if (array_key_exists('activo', $data)) {
            $usuario->activo = $data['activo'] ?? true;
        }
        if (array_key_exists('rol', $data)) {
            $usuario->rol = $data['rol'];
        }
        if (!empty($data['password'])) {
            $usuario->password = $data['password'];
        }

        $usuario->save();

        if ($usuario->is_super_admin || $usuario->rol) {
            // Super admin or role-based: clear granular permissions
            $usuario->permisos()->delete();
            $usuario->permisosCamposAnimales()->delete();
        } else {
            if (array_key_exists('permisos', $data)) {
                $this->syncPermisos($usuario, $data['permisos'] ?? []);
            }
            if (array_key_exists('campos_animales', $data)) {
                $this->syncCamposAnimales($usuario, $data['campos_animales'] ?? []);
            }
        }

        return response()->json(['message' => 'Usuario actualizado correctamente.']);
    }

    public function toggleActivo(Request $request, User $usuario)
    {
        // No puede desactivarse a sí mismo
        if ($usuario->id === $request->user()->id) {
            return response()->json(['message' => 'No puedes desactivarte a ti mismo.'], 422);
        }

        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $estado = $usuario->activo ? 'activado' : 'desactivado';

        return response()->json(['message' => "Usuario {$estado} correctamente.", 'activo' => $usuario->activo]);
    }

    private function syncPermisos(User $user, array $permisos): void
    {
        // Eliminar existentes y recrear
        $user->permisos()->delete();

        foreach (self::MODULOS as $modulo) {
            $p = $permisos[$modulo] ?? [];
            PermisoUsuario::create([
                'user_id' => $user->id,
                'modulo' => $modulo,
                'puede_ver' => !empty($p['puede_ver']),
                'puede_agregar' => !empty($p['puede_agregar']),
                'puede_editar' => !empty($p['puede_editar']),
                'puede_eliminar' => !empty($p['puede_eliminar']),
            ]);
        }
    }

    private function syncCamposAnimales(User $user, array $campos): void
    {
        $user->permisosCamposAnimales()->delete();

        foreach (self::CAMPOS_ANIMALES as $campo) {
            if (!empty($campos[$campo])) {
                PermisoCampoAnimal::create([
                    'user_id' => $user->id,
                    'campo' => $campo,
                    'puede_editar' => true,
                ]);
            }
        }
    }
}
