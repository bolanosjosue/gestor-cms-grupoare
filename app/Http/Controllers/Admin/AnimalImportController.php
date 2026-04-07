<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\ImportacionAnimal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnimalImportController extends Controller
{
    public function index()
    {
        $historial = ImportacionAnimal::query()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.animales.importar', compact('historial'));
    }

    /**
     * Historial JSON para auto-refresh vía AJAX.
     */
    public function historial(Request $request)
    {
        $historial = ImportacionAnimal::query()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->paginate(15);

        $items = $historial->map(function ($imp) {
            return [
                'fecha'           => $imp->created_at->timezone(config('app.timezone'))->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
                'hora'            => $imp->created_at->format('H:i'),
                'usuario'         => $imp->user?->name ?? '—',
                'nombre_archivo'  => $imp->nombre_archivo ?? '—',
                'total_registros' => number_format($imp->total_registros),
                'insertados'      => number_format($imp->insertados),
                'actualizados'    => number_format($imp->actualizados),
                'con_error'       => number_format($imp->con_error),
                'con_error_raw'   => $imp->con_error,
                'completada'      => (bool) $imp->finalized_at,
            ];
        });

        return response()->json([
            'data'  => $items,
            'links' => $historial->links('pagination::default')->toHtml(),
        ]);
    }

    /**
     * Importar batch de registros (upsert por identificacion_electronica).
     * Opcional: session_uuid + metadatos de lote para historial unificado.
     */
    public function importar(Request $request)
    {
        $request->validate([
            'registros' => ['required', 'array', 'min:1'],
            'registros.*.codigo_practico' => ['required', 'string'],
            'registros.*.identificacion_electronica' => ['required', 'string'],
            'registros.*.agropecuaria' => ['nullable', 'string'],
            'registros.*.estado' => ['nullable', 'string'],
            'registros.*.fecha_nacimiento' => ['nullable', 'date'],
            'registros.*.padre_nombre' => ['nullable', 'string'],
            'registros.*.codigo_madre' => ['nullable', 'string'],
            'registros.*.ultima_locacion' => ['nullable', 'string'],
            'registros.*.composicion_racial' => ['nullable', 'string'],
            'registros.*.clasificacion_asociacion' => ['nullable', 'string'],
            'registros.*.ultimo_peso' => ['nullable', 'numeric'],
            'registros.*.estandarizacion_produccion' => ['nullable', 'string'],
            'registros.*.fecha_ultimo_servicio' => ['nullable', 'date'],
            'registros.*.estado_reproductivo' => ['nullable', 'string'],
            'registros.*.numero_revisiones' => ['nullable', 'integer'],
            'registros.*.fecha_ultimo_parto' => ['nullable', 'date'],
            'registros.*.fecha_secado' => ['nullable', 'date'],
            'registros.*.nombre' => ['nullable', 'string'],
            'registros.*.sexo' => ['nullable', 'string'],
            'registros.*.codigo_reproductor' => ['nullable', 'string'],
            'registros.*.codigo' => ['nullable', 'string'],
            'registros.*.codigo_nombre' => ['nullable', 'string'],
            'session_uuid' => ['nullable', 'uuid'],
            'nombre_archivo' => ['nullable', 'string', 'max:255'],
            'lote_index' => ['nullable', 'integer', 'min:0'],
            'total_lotes' => ['nullable', 'integer', 'min:1'],
            'total_registros' => ['nullable', 'integer', 'min:0'],
        ]);

        $registros = $request->input('registros');
        $insertados = 0;
        $actualizados = 0;
        $errores = [];

        $campos = [
            'agropecuaria', 'codigo_practico', 'estado', 'identificacion_electronica',
            'fecha_nacimiento', 'padre_nombre', 'codigo_madre', 'ultima_locacion',
            'composicion_racial', 'clasificacion_asociacion', 'ultimo_peso',
            'estandarizacion_produccion', 'fecha_ultimo_servicio', 'estado_reproductivo',
            'numero_revisiones', 'fecha_ultimo_parto', 'fecha_secado', 'nombre',
            'sexo', 'codigo_reproductor', 'codigo', 'codigo_nombre',
        ];

        foreach ($registros as $index => $registro) {
            try {
                $fila = [];
                foreach ($campos as $campo) {
                    $fila[$campo] = $registro[$campo] ?? null;
                }

                $existing = null;

                if (! empty($fila['identificacion_electronica'])) {
                    $existing = Animal::where('identificacion_electronica', $fila['identificacion_electronica'])->first();
                }

                if (! $existing && ! empty($fila['codigo_practico']) && ! empty($fila['agropecuaria'])) {
                    $existing = Animal::where('codigo_practico', $fila['codigo_practico'])
                        ->where('agropecuaria', $fila['agropecuaria'])
                        ->first();
                }

                if ($existing) {
                    $existing->fill($fila);
                    $existing->save();
                    $actualizados++;
                } else {
                    Animal::create($fila);
                    $insertados++;
                }
            } catch (\Throwable $e) {
                Log::warning("Error importando animal fila {$index}: ".$e->getMessage());
                $errores[] = [
                    'fila' => $index + 1,
                    'identificacion' => $registro['identificacion_electronica'] ?? $registro['codigo_practico'] ?? 'N/A',
                    'error' => $e->getMessage(),
                ];
            }
        }

        if ($request->filled('session_uuid')) {
            $this->registrarHistorialLote(
                $request,
                $insertados,
                $actualizados,
                count($errores)
            );
        }

        return response()->json([
            'insertados' => $insertados,
            'actualizados' => $actualizados,
            'errores' => $errores,
        ]);
    }

    protected function registrarHistorialLote(Request $request, int $insertados, int $actualizados, int $conError): void
    {
        $sessionUuid = $request->input('session_uuid');
        $loteIndex = (int) $request->input('lote_index', 0);
        $totalLotes = max(1, (int) $request->input('total_lotes', 1));
        $totalRegistros = (int) $request->input('total_registros', 0);
        $nombreArchivo = $request->input('nombre_archivo');

        $historial = ImportacionAnimal::firstOrNew(['session_uuid' => $sessionUuid]);

        if (! $historial->exists) {
            $historial->user_id = $request->user()?->id;
            $historial->nombre_archivo = $nombreArchivo ? mb_substr($nombreArchivo, 0, 255) : null;
            $historial->total_registros = $totalRegistros;
            $historial->lotes_total = $totalLotes;
            $historial->insertados = 0;
            $historial->actualizados = 0;
            $historial->con_error = 0;
        }

        $historial->insertados += $insertados;
        $historial->actualizados += $actualizados;
        $historial->con_error += $conError;

        if ($loteIndex + 1 >= $totalLotes) {
            $historial->finalized_at = now();
        }

        $historial->save();
    }
}
