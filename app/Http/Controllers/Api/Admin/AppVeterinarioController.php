<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Palpacion;
use App\Models\PesajeLeche;
use Illuminate\Http\Request;

class AppVeterinarioController extends Controller
{
    private const OD_OI_VALUES = ['AT','CL','F6','F8','F9','F10','F12','F14','F15','FM','QF','QL','QO','Q20','Q25'];
    private const UT_VALUES    = ['UNI','IUC','UE','UT','PIA','CALC','MTRI','SALP'];
    private const DIAG_VALUES  = ['E','L','N','P','V','PR'];

    public function buscar(Request $request)
    {
        $request->validate([
            'electronico' => 'required|string|max:100',
        ]);

        $electronico = trim($request->input('electronico'));

        $animal = Animal::where('identificacion_electronica', $electronico)->first();

        if (! $animal) {
            return response()->json(['encontrado' => false]);
        }

        $hoy = now()->toDateString();
        $palpacionHoy = Palpacion::where('animal_id', $animal->id)
            ->whereDate('fecha', $hoy)
            ->orderByDesc('created_at')
            ->first();

        return response()->json([
            'encontrado' => true,
            'animal' => [
                'id'                         => $animal->id,
                'codigo'                     => $animal->codigo ?? $animal->codigo_practico,
                'identificacion_electronica' => $animal->identificacion_electronica,
                'nombre'                     => $animal->nombre ?: null,
                'agropecuaria'               => $animal->agropecuaria ?: null,
                'sexo'                       => $animal->sexo ?: null,
                'estado'                     => $animal->estado ?: null,
                'estado_reproductivo'        => $animal->estado_reproductivo ?: null,
                'composicion_racial'         => $animal->composicion_racial ?: null,
                'fecha_nacimiento'           => $animal->fecha_nacimiento?->format('Y-m-d'),
                'padre_nombre'               => $animal->padre_nombre ?: null,
                'codigo_madre'               => $animal->codigo_madre ?: null,
                'ultimo_peso'                => $animal->ultimo_peso ? number_format((float) $animal->ultimo_peso, 2) : null,
                'fecha_ultimo_servicio'      => $animal->fecha_ultimo_servicio?->format('Y-m-d'),
                'fecha_ultimo_parto'         => $animal->fecha_ultimo_parto?->format('Y-m-d'),
                'fecha_secado'               => $animal->fecha_secado?->format('Y-m-d'),
                'numero_revisiones'          => $animal->numero_revisiones,
                'ultima_locacion'            => $animal->ultima_locacion ?: null,
                'estandarizacion_produccion' => $animal->estandarizacion_produccion ?: null,
            ],
            'palpacion_hoy' => $palpacionHoy ? [
                'id'          => $palpacionHoy->id,
                'cc'          => (float) $palpacionHoy->cc,
                'od'          => $palpacionHoy->od,
                'oi'          => $palpacionHoy->oi,
                'ut'          => $palpacionHoy->ut,
                'diagnostico' => $palpacionHoy->diagnostico,
            ] : null,
            'historial_palpaciones' => Palpacion::where('animal_id', $animal->id)
                ->orderBy('fecha', 'asc')
                ->limit(30)
                ->get()
                ->map(fn ($p) => [
                    'fecha'       => $p->fecha->format('Y-m-d'),
                    'cc'          => (float) $p->cc,
                    'diagnostico' => $p->diagnostico,
                ]),
            'historial_leche' => PesajeLeche::where('animal_id', $animal->id)
                ->orderBy('fecha', 'desc')
                ->limit(15)
                ->get()
                ->reverse()
                ->values()
                ->map(fn ($p) => [
                    'fecha' => $p->fecha->format('Y-m-d'),
                    'total' => round(($p->peso_am ?? 0) + ($p->peso_pm ?? 0), 2),
                ]),
            'opciones' => [
                'od_oi' => self::OD_OI_VALUES,
                'ut'    => self::UT_VALUES,
                'diag'  => self::DIAG_VALUES,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'animal_id'   => 'required|exists:animales,id',
            'cc'          => 'required|numeric|min:1|max:5',
            'od'          => 'nullable|string|in:' . implode(',', self::OD_OI_VALUES),
            'oi'          => 'nullable|string|in:' . implode(',', self::OD_OI_VALUES),
            'ut'          => 'nullable|string|in:' . implode(',', self::UT_VALUES),
            'diagnostico' => 'nullable|string|in:' . implode(',', self::DIAG_VALUES),
        ]);

        $animalId = $data['animal_id'];
        $hoy      = now()->toDateString();

        $palpacion = Palpacion::where('animal_id', $animalId)
            ->whereDate('fecha', $hoy)
            ->first();

        $campos = [
            'fecha'       => $hoy,
            'cc'          => $data['cc'],
            'od'          => $data['od'] ?? null,
            'oi'          => $data['oi'] ?? null,
            'ut'          => $data['ut'] ?? null,
            'diagnostico' => $data['diagnostico'] ?? null,
        ];

        if ($palpacion) {
            $palpacion->update($campos);
        } else {
            $palpacion = Palpacion::create($campos + ['animal_id' => $animalId]);
        }

        return response()->json([
            'ok'        => true,
            'palpacion' => [
                'id'          => $palpacion->id,
                'cc'          => (float) $palpacion->cc,
                'od'          => $palpacion->od,
                'oi'          => $palpacion->oi,
                'ut'          => $palpacion->ut,
                'diagnostico' => $palpacion->diagnostico,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animales,id',
        ]);

        Palpacion::where('animal_id', $request->input('animal_id'))
            ->whereDate('fecha', now()->toDateString())
            ->delete();

        return response()->json(['ok' => true]);
    }
}
