<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Palpacion;
use App\Models\Pesaje;
use App\Models\PesajeLeche;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Return all animals with basic fields for offline cache.
     */
    public function animales(): JsonResponse
    {
        $animales = Animal::select([
            'id',
            'identificacion_electronica',
            'codigo_practico',
            'codigo',
            'nombre',
            'agropecuaria',
            'sexo',
            'ultimo_peso',
            'estado',
        ])
        ->whereNotNull('identificacion_electronica')
        ->where('identificacion_electronica', '!=', '')
        ->get()
        ->map(fn ($a) => [
            'id'                         => $a->id,
            'identificacion_electronica' => $a->identificacion_electronica,
            'codigo'                     => $a->codigo ?? $a->codigo_practico,
            'nombre'                     => $a->nombre ?: 'Sin nombre',
            'agropecuaria'               => $a->agropecuaria ?: '—',
            'sexo'                       => $a->sexo ?: '—',
            'ultimo_peso'                => $a->ultimo_peso ? number_format((float) $a->ultimo_peso, 2) . ' kg' : '—',
        ]);

        return response()->json([
            'animales' => $animales,
            'total'    => $animales->count(),
        ]);
    }

    /**
     * Initial bootstrap for offline local database.
     */
    public function bootstrap(Request $request): JsonResponse
    {
        $request->validate([
            'updated_since' => 'nullable|date',
        ]);

        $updatedSince = $request->input('updated_since');

        $animalesQuery = Animal::query();
        $pesosQuery = Pesaje::query();
        $pesajesQuery = PesajeLeche::query();
        $palpacionesQuery = Palpacion::query();

        if ($updatedSince) {
            $animalesQuery->where('updated_at', '>', $updatedSince);
            $pesosQuery->where('updated_at', '>', $updatedSince);
            $pesajesQuery->where('updated_at', '>', $updatedSince);
            $palpacionesQuery->where('updated_at', '>', $updatedSince);
        }

        $animales = $animalesQuery
            ->orderBy('id')
            ->get([
                'id',
                'identificacion_electronica',
                'codigo_practico',
                'codigo',
                'nombre',
                'agropecuaria',
                'sexo',
                'estado',
                'ultimo_peso',
                'fecha_nacimiento',
                'padre_nombre',
                'codigo_madre',
                'estado_reproductivo',
                'fecha_ultimo_servicio',
                'fecha_ultimo_parto',
                'fecha_secado',
                'numero_revisiones',
                'updated_at',
            ]);

        $pesos = $pesosQuery
            ->orderBy('id')
            ->get([
                'id',
                'animal_id',
                'peso',
                'fecha',
                'observacion',
                'updated_at',
            ]);

        $pesajes = $pesajesQuery
            ->orderBy('id')
            ->get([
                'id',
                'animal_id',
                'fecha',
                'peso_am',
                'peso_pm',
                'observacion',
                'updated_at',
            ]);

        $palpaciones = $palpacionesQuery
            ->orderBy('id')
            ->get([
                'id',
                'animal_id',
                'fecha',
                'cc',
                'od',
                'oi',
                'ut',
                'diagnostico',
                'observacion',
                'updated_at',
            ]);

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'updated_since' => $updatedSince,
            'data' => [
                'animales' => $animales,
                'pesos' => $pesos,
                'pesajes' => $pesajes,
                'palpaciones' => $palpaciones,
            ],
            'counts' => [
                'animales' => $animales->count(),
                'pesos' => $pesos->count(),
                'pesajes' => $pesajes->count(),
                'palpaciones' => $palpaciones->count(),
            ],
        ]);
    }

    /**
     * Incremental pull for remote changes.
     */
    public function pull(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'since' => 'required|date',
        ]);

        $since = $validated['since'];

        $animales = Animal::where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->get([
                'id',
                'identificacion_electronica',
                'codigo_practico',
                'codigo',
                'nombre',
                'agropecuaria',
                'sexo',
                'estado',
                'ultimo_peso',
                'fecha_nacimiento',
                'padre_nombre',
                'codigo_madre',
                'estado_reproductivo',
                'fecha_ultimo_servicio',
                'fecha_ultimo_parto',
                'fecha_secado',
                'numero_revisiones',
                'updated_at',
            ]);

        $pesos = Pesaje::where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->get([
                'id',
                'animal_id',
                'peso',
                'fecha',
                'observacion',
                'updated_at',
            ]);

        $pesajes = PesajeLeche::where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->get([
                'id',
                'animal_id',
                'fecha',
                'peso_am',
                'peso_pm',
                'observacion',
                'updated_at',
            ]);

        $palpaciones = Palpacion::where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->get([
                'id',
                'animal_id',
                'fecha',
                'cc',
                'od',
                'oi',
                'ut',
                'diagnostico',
                'observacion',
                'updated_at',
            ]);

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'since' => $since,
            'data' => [
                'animales' => $animales,
                'pesos' => $pesos,
                'pesajes' => $pesajes,
                'palpaciones' => $palpaciones,
            ],
            'counts' => [
                'animales' => $animales->count(),
                'pesos' => $pesos->count(),
                'pesajes' => $pesajes->count(),
                'palpaciones' => $palpaciones->count(),
            ],
        ]);
    }

    /**
     * Batch push for local changes queued offline.
     */
    public function push(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'operations' => 'required|array|min:1',
            'operations.*.entity' => 'required|string|in:pesos,pesajes,palpaciones',
            'operations.*.action' => 'required|string|in:upsert,delete',
            'operations.*.data' => 'nullable|array',
            'operations.*.server_id' => 'nullable|integer|min:1',
            'operations.*.client_ref' => 'nullable|string|max:120',
        ]);

        $results = [];

        DB::beginTransaction();
        try {
            foreach ($validated['operations'] as $index => $operation) {
                try {
                    $entity = $operation['entity'];
                    $action = $operation['action'];
                    $data = $operation['data'] ?? [];
                    $serverId = $operation['server_id'] ?? null;
                    $clientRef = $operation['client_ref'] ?? null;

                    $recordId = match ($entity) {
                        'pesos' => $this->applyPesoOperation($action, $data, $serverId),
                        'pesajes' => $this->applyPesajeOperation($action, $data, $serverId),
                        'palpaciones' => $this->applyPalpacionOperation($action, $data, $serverId),
                    };

                    $results[] = [
                        'index' => $index,
                        'client_ref' => $clientRef,
                        'entity' => $entity,
                        'action' => $action,
                        'status' => 'ok',
                        'server_id' => $recordId,
                    ];
                } catch (\Throwable $e) {
                    $results[] = [
                        'index' => $index,
                        'client_ref' => $operation['client_ref'] ?? null,
                        'entity' => $operation['entity'] ?? null,
                        'action' => $operation['action'] ?? null,
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo procesar el lote.',
                'error' => $e->getMessage(),
            ], 500);
        }

        $okCount = collect($results)->where('status', 'ok')->count();
        $errorCount = collect($results)->where('status', 'error')->count();

        return response()->json([
            'ok' => $errorCount === 0,
            'server_time' => now()->toIso8601String(),
            'summary' => [
                'total' => count($results),
                'ok' => $okCount,
                'errors' => $errorCount,
            ],
            'results' => $results,
        ]);
    }

    private function applyPesoOperation(string $action, array $data, ?int $serverId): ?int
    {
        if ($action === 'delete') {
            if (! $serverId) {
                throw new \InvalidArgumentException('server_id es requerido para eliminar peso.');
            }

            $peso = Pesaje::find($serverId);
            if (! $peso) {
                return null;
            }

            $animalId = $peso->animal_id;
            $peso->delete();
            $this->refreshUltimoPeso($animalId);

            return null;
        }

        $payload = validator($data, [
            'animal_id' => 'required|exists:animales,id',
            'peso' => 'required|numeric|min:0.01|max:99999.99',
            'fecha' => 'required|date',
            'observacion' => 'nullable|string|max:1000',
        ])->validate();

        if ($serverId) {
            $peso = Pesaje::find($serverId);
            if (! $peso) {
                throw new \InvalidArgumentException('No existe el peso a actualizar.');
            }
            $peso->update($payload);
        } else {
            $peso = Pesaje::create($payload);
        }

        $this->refreshUltimoPeso((int) $payload['animal_id']);

        return $peso->id;
    }

    private function applyPesajeOperation(string $action, array $data, ?int $serverId): ?int
    {
        if ($action === 'delete') {
            if (! $serverId) {
                throw new \InvalidArgumentException('server_id es requerido para eliminar pesaje.');
            }

            $pesaje = PesajeLeche::find($serverId);
            if (! $pesaje) {
                return null;
            }

            $pesaje->delete();

            return null;
        }

        $payload = validator($data, [
            'animal_id' => 'required|exists:animales,id',
            'fecha' => 'required|date',
            'peso_am' => 'nullable|numeric|min:0|max:99999.99',
            'peso_pm' => 'nullable|numeric|min:0|max:99999.99',
            'observacion' => 'nullable|string|max:1000',
        ])->validate();

        if ($serverId) {
            $pesaje = PesajeLeche::find($serverId);
            if (! $pesaje) {
                throw new \InvalidArgumentException('No existe el pesaje a actualizar.');
            }
            $pesaje->update($payload);
        } else {
            $pesaje = PesajeLeche::create($payload);
        }

        return $pesaje->id;
    }

    private function applyPalpacionOperation(string $action, array $data, ?int $serverId): ?int
    {
        if ($action === 'delete') {
            if (! $serverId) {
                throw new \InvalidArgumentException('server_id es requerido para eliminar palpación.');
            }

            $palpacion = Palpacion::find($serverId);
            if (! $palpacion) {
                return null;
            }

            $palpacion->delete();

            return null;
        }

        $payload = validator($data, [
            'animal_id' => 'required|exists:animales,id',
            'fecha' => 'required|date',
            'cc' => 'required|numeric|min:1|max:5',
            'od' => 'nullable|string|max:10',
            'oi' => 'nullable|string|max:10',
            'ut' => 'nullable|string|max:10',
            'diagnostico' => 'nullable|string|max:10',
            'observacion' => 'nullable|string|max:1000',
        ])->validate();

        if ($serverId) {
            $palpacion = Palpacion::find($serverId);
            if (! $palpacion) {
                throw new \InvalidArgumentException('No existe la palpación a actualizar.');
            }
            $palpacion->update($payload);
        } else {
            $palpacion = Palpacion::create($payload);
        }

        return $palpacion->id;
    }

    private function refreshUltimoPeso(int $animalId): void
    {
        $ultimo = Pesaje::where('animal_id', $animalId)
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->first();

        Animal::where('id', $animalId)->update([
            'ultimo_peso' => $ultimo?->peso,
        ]);
    }
}
