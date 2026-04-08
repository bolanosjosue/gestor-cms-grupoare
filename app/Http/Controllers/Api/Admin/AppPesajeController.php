<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\PesajeLeche;
use Illuminate\Http\Request;

class AppPesajeController extends Controller
{
    public function agropecuarias()
    {
        return response()->json([
            'agropecuarias' => Animal::distinct()
                ->whereNotNull('agropecuaria')
                ->pluck('agropecuaria')
                ->sort()
                ->values(),
        ]);
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
        $pesajeHoy = PesajeLeche::where('animal_id', $animal->id)
            ->whereDate('fecha', $hoy)
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
            'pesaje_hoy' => $pesajeHoy ? [
                'id'      => $pesajeHoy->id,
                'peso_am' => $pesajeHoy->peso_am !== null ? (float) $pesajeHoy->peso_am : null,
                'peso_pm' => $pesajeHoy->peso_pm !== null ? (float) $pesajeHoy->peso_pm : null,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animales,id',
            'peso_am'   => 'nullable|numeric|min:0|max:99999.99',
            'peso_pm'   => 'nullable|numeric|min:0|max:99999.99',
        ]);

        $animalId = $request->input('animal_id');
        $hoy      = now()->toDateString();

        $pesaje = PesajeLeche::where('animal_id', $animalId)
            ->whereDate('fecha', $hoy)
            ->first();

        $data = ['fecha' => $hoy];
        if ($request->input('peso_am') !== null) {
            $data['peso_am'] = $request->input('peso_am');
        }
        if ($request->input('peso_pm') !== null) {
            $data['peso_pm'] = $request->input('peso_pm');
        }

        if ($pesaje) {
            $pesaje->update($data);
        } else {
            $pesaje = PesajeLeche::create($data + ['animal_id' => $animalId]);
        }

        return response()->json([
            'ok'      => true,
            'peso_am' => $pesaje->peso_am !== null ? (float) $pesaje->peso_am : null,
            'peso_pm' => $pesaje->peso_pm !== null ? (float) $pesaje->peso_pm : null,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animales,id',
        ]);

        PesajeLeche::where('animal_id', $request->input('animal_id'))
            ->whereDate('fecha', now()->toDateString())
            ->delete();

        return response()->json(['ok' => true]);
    }

    public function nacimiento(Request $request)
    {
        $request->validate([
            'identificacion_electronica' => 'required|string|regex:/^\d{15}$/|unique:animales,identificacion_electronica',
            'codigo_practico'            => 'required|string|max:255|unique:animales,codigo_practico',
            'sexo'                       => 'required|in:Macho,Hembra',
            'fecha_nacimiento'           => 'required|date',
            'agropecuaria'               => 'required|string|max:255',
            'codigo_madre'               => 'nullable|string|max:255',
            'padre_nombre'               => 'nullable|string|max:255',
            'nombre'                     => 'nullable|string|max:255',
            'peso'                       => 'nullable|numeric|min:0.01|max:99999.99',
        ], [
            'identificacion_electronica.regex'  => 'El ID electrónico debe tener exactamente 15 dígitos numéricos.',
            'identificacion_electronica.unique' => 'Este ID electrónico ya está registrado.',
            'codigo_practico.unique'            => 'Este código práctico ya está registrado.',
        ]);

        $animal = Animal::create([
            'identificacion_electronica' => $request->input('identificacion_electronica'),
            'codigo_practico'            => $request->input('codigo_practico'),
            'sexo'                       => $request->input('sexo'),
            'fecha_nacimiento'           => $request->input('fecha_nacimiento'),
            'agropecuaria'               => $request->input('agropecuaria'),
            'codigo_madre'               => $request->input('codigo_madre'),
            'padre_nombre'               => $request->input('padre_nombre'),
            'nombre'                     => $request->input('nombre'),
            'estado'                     => 'Activo',
            'ultimo_peso'                => $request->input('peso'),
        ]);

        return response()->json([
            'ok'     => true,
            'animal' => [
                'id'                         => $animal->id,
                'identificacion_electronica' => $animal->identificacion_electronica,
                'codigo_practico'            => $animal->codigo_practico,
                'nombre'                     => $animal->nombre,
            ],
        ]);
    }
}
