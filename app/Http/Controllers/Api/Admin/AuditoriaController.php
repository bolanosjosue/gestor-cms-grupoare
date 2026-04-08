<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function filtros()
    {
        return response()->json([
            'usuarios' => User::orderBy('name')->get(['id', 'name']),
            'tablas'   => Auditoria::distinct()->pluck('tabla')->sort()->values(),
            'campos'   => Auditoria::distinct()->whereNotNull('campo')->pluck('campo')->sort()->values(),
        ]);
    }

    public function datos(Request $request)
    {
        $query = Auditoria::with('user:id,name')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->input('accion'));
        }
        if ($request->filled('tabla')) {
            $query->where('tabla', $request->input('tabla'));
        }
        if ($request->filled('campo')) {
            $query->where('campo', $request->input('campo'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->input('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->input('hasta'));
        }

        $registros = $query->paginate(25);

        return response()->json([
            'data'         => $registros->items(),
            'current_page' => $registros->currentPage(),
            'last_page'    => $registros->lastPage(),
            'total'        => $registros->total(),
        ]);
    }

    public function grupo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $record = Auditoria::with('user:id,name')->find($request->input('id'));

        if (! $record) {
            return response()->json([]);
        }

        if ($record->accion === 'update') {
            $logs = Auditoria::with('user:id,name')
                ->where('tabla', $record->tabla)
                ->where('registro_id', $record->registro_id)
                ->where('accion', 'update')
                ->where('created_at', $record->getRawOriginal('created_at'))
                ->get();
            return response()->json($logs);
        }

        return response()->json([$record]);
    }
}
