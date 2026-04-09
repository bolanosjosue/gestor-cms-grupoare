<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        if (! $user->activo) {
            return response()->json(['message' => 'Tu cuenta está desactivada.'], 403);
        }

        $deviceName = $data['device_name'] ?? 'desktop';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'             => $user->id,
                'name'           => $user->name,
                'username'       => $user->username,
                'is_super_admin' => $user->is_super_admin,
                'rol'            => $user->rol,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('permisos');

        return response()->json([
            'id'             => $user->id,
            'name'           => $user->name,
            'username'       => $user->username,
            'is_super_admin' => $user->is_super_admin,
            'activo'         => $user->activo,
            'rol'            => $user->rol,
            'permisos'       => $user->permisos->map(fn ($p) => [
                'modulo'          => $p->modulo,
                'puede_ver'       => $p->puede_ver,
                'puede_agregar'   => $p->puede_agregar,
                'puede_editar'    => $p->puede_editar,
                'puede_eliminar'  => $p->puede_eliminar,
            ])->values(),
        ]);
    }
}
