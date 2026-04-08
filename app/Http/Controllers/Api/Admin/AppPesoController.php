<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Pesaje;
use Illuminate\Http\Request;

class AppPesoController extends Controller
{
    public function agropecuarias()
    {
        $agropecuarias = Animal::whereNotNull('agropecuaria')
            ->where('agropecuaria', '!=', '')
            ->distinct()
            ->orderBy('agropecuaria')
            ->pluck('agropecuaria');

        return response()->json($agropecuarias);
    }

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
        $pesoHoy = Pesaje::where('animal_id', $animal->id)
            ->whereDate('fecha', $hoy)
            ->orderByDesc('created_at')
            ->first();

        return response()->json([
            'encontrado' => true,
            'animal' => [
                'id'                         => $animal->id,
                'codigo'                     => $animal->codigo ?? $animal->codigo_practico,
                'identificacion_electronica' => $animal->identificacion_electronica,
                'nombre'                     => $animal->nombre ?: 'Sin nombre',
                'agropecuaria'               => $animal->agropecuaria ?: '—',
                'sexo'                       => $animal->sexo ?: '—',
                'ultimo_peso'                => $animal->ultimo_peso ? number_format((float) $animal->ultimo_peso, 2) . ' kg' : '—',
            ],
            'peso_hoy' => $pesoHoy ? [
                'id'   => $pesoHoy->id,
                'peso' => (float) $pesoHoy->peso,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animales,id',
            'peso'      => 'required|numeric|min:0.01|max:99999.99',
        ]);

        $animalId = $request->input('animal_id');
        $peso     = $request->input('peso');
        $hoy      = now()->toDateString();

        $pesaje = Pesaje::where('animal_id', $animalId)
            ->whereDate('fecha', $hoy)
            ->orderByDesc('created_at')
            ->first();

        if ($pesaje) {
            $pesaje->update(['peso' => $peso]);
        } else {
            $pesaje = Pesaje::create([
                'animal_id' => $animalId,
                'peso'      => $peso,
                'fecha'     => $hoy,
            ]);
        }

        Animal::where('id', $animalId)->update(['ultimo_peso' => $peso]);

        return response()->json(['ok' => true, 'peso' => (float) $pesaje->peso]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animales,id',
        ]);

        $animalId = $request->input('animal_id');
        $hoy      = now()->toDateString();

        $pesaje = Pesaje::where('animal_id', $animalId)
            ->whereDate('fecha', $hoy)
            ->orderByDesc('created_at')
            ->first();

        if ($pesaje) {
            $pesaje->delete();

            $ultimo = Pesaje::where('animal_id', $animalId)
                ->orderByDesc('fecha')
                ->orderByDesc('created_at')
                ->first();

            Animal::where('id', $animalId)->update([
                'ultimo_peso' => $ultimo ? $ultimo->peso : null,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
