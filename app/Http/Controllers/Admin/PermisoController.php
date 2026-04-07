<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    /**
     * GET /admin/usuario/permisos
     * Retorna los permisos del usuario autenticado.
     */
    public function misPermisos(Request $request)
    {
        $user = $request->user();
        $user->load('permisos');

        if ($user->is_super_admin) {
            return response()->json([
                'is_super_admin' => true,
                'permisos' => collect([
                    'dashboard', 'articulos', 'personal', 'razas', 'ventas', 'animales',
                ])->mapWithKeys(fn ($m) => [$m => [
                    'puede_ver' => true,
                    'puede_agregar' => true,
                    'puede_editar' => true,
                    'puede_eliminar' => true,
                ]]),
            ]);
        }

        $permisos = [];
        foreach ($user->permisos as $p) {
            $permisos[$p->modulo] = [
                'puede_ver' => $p->puede_ver,
                'puede_agregar' => $p->puede_agregar,
                'puede_editar' => $p->puede_editar,
                'puede_eliminar' => $p->puede_eliminar,
            ];
        }

        return response()->json([
            'is_super_admin' => false,
            'permisos' => $permisos,
        ]);
    }

    /**
     * GET /admin/usuario/permisos/campos-animales
     * Retorna los campos de animales que el usuario puede editar.
     */
    public function camposAnimales(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'campos_editables' => $user->getCamposEditablesAnimales(),
        ]);
    }
}
