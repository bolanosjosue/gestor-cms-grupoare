<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\PesajeLeche;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PesajeLecheController extends Controller
{
    public function seccionData()
    {
        $agropecuarias = Animal::query()
            ->whereNotNull('agropecuaria')
            ->where('agropecuaria', '!=', '')
            ->where('sexo', '!=', 'Macho')
            ->selectRaw('agropecuaria, COUNT(*) as count')
            ->groupBy('agropecuaria')
            ->orderBy('agropecuaria')
            ->pluck('count', 'agropecuaria');

        return response()->json([
            'agropecuarias' => $agropecuarias->keys(),
            'agro_counts'   => $agropecuarias,
        ]);
    }

    public function dashboardData()
    {
        $agropecuarias = Animal::query()
            ->whereNotNull('agropecuaria')
            ->where('agropecuaria', '!=', '')
            ->distinct()
            ->orderBy('agropecuaria')
            ->pluck('agropecuaria');

        $promedios = [];
        foreach ($agropecuarias as $agro) {
            $animalIds = Animal::where('agropecuaria', $agro)
                ->where('sexo', '!=', 'Macho')
                ->pluck('id');

            $pesajes = PesajeLeche::whereIn('animal_id', $animalIds)->get();
            if ($pesajes->isEmpty()) {
                $promedios[] = ['agropecuaria' => $agro, 'promedio' => 0];
                continue;
            }

            $totales = $pesajes->map(fn ($p) => round(($p->peso_am ?? 0) + ($p->peso_pm ?? 0), 2));
            $promedios[] = [
                'agropecuaria' => $agro,
                'promedio'     => round($totales->avg(), 2),
            ];
        }

        $allAnimalIds = Animal::where('sexo', '!=', 'Macho')
            ->whereNotNull('agropecuaria')
            ->where('agropecuaria', '!=', '')
            ->pluck('id');

        $pesajesPorAnimal = PesajeLeche::whereIn('animal_id', $allAnimalIds)
            ->get()
            ->groupBy('animal_id')
            ->map(fn ($group) => round($group->avg(fn ($p) => ($p->peso_am ?? 0) + ($p->peso_pm ?? 0)), 2));

        $rangos = [
            '0 - 1 L'  => 0, '1 - 3 L'  => 0, '3 - 5 L'  => 0,
            '5 - 8 L'  => 0, '8 - 12 L' => 0, '12+ L'    => 0,
        ];

        foreach ($pesajesPorAnimal as $avg) {
            if ($avg <= 1)      $rangos['0 - 1 L']++;
            elseif ($avg <= 3)  $rangos['1 - 3 L']++;
            elseif ($avg <= 5)  $rangos['3 - 5 L']++;
            elseif ($avg <= 8)  $rangos['5 - 8 L']++;
            elseif ($avg <= 12) $rangos['8 - 12 L']++;
            else                $rangos['12+ L']++;
        }

        $rangos = array_filter($rangos, fn ($v) => $v > 0);

        return response()->json([
            'promedios'    => $promedios,
            'distribucion' => $rangos,
        ]);
    }

    public function agropecuariaData(Request $request, string $nombre)
    {
        $nombre = urldecode($nombre);
        $fecha  = $request->input('fecha', now()->toDateString());

        $animales = Animal::where('agropecuaria', $nombre)
            ->where('sexo', '!=', 'Macho')
            ->orderBy('codigo_practico')
            ->get(['id', 'codigo_practico', 'identificacion_electronica', 'nombre']);

        $pesajes = PesajeLeche::whereIn('animal_id', $animales->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('animal_id');

        $rows = $animales->map(function ($a) use ($pesajes) {
            $p = $pesajes->get($a->id);
            if (! $p) return null;
            return [
                'animal_id'                  => $a->id,
                'codigo_practico'            => $a->codigo_practico,
                'identificacion_electronica' => $a->identificacion_electronica,
                'nombre'                     => $a->nombre,
                'peso_am'                    => (float) $p->peso_am,
                'peso_pm'                    => (float) $p->peso_pm,
                'total'                      => round((float) ($p->peso_am ?? 0) + (float) ($p->peso_pm ?? 0), 2),
            ];
        })->filter()->values();

        $totalAM = $rows->sum(fn ($r) => $r['peso_am'] ?? 0);
        $totalPM = $rows->sum(fn ($r) => $r['peso_pm'] ?? 0);

        return response()->json([
            'fecha'        => $fecha,
            'agropecuaria' => $nombre,
            'registros'    => $rows->values(),
            'resumen'      => [
                'total_am'      => round($totalAM, 2),
                'total_pm'      => round($totalPM, 2),
                'total_general' => round($totalAM + $totalPM, 2),
                'animales'      => $animales->count(),
                'con_registro'  => $rows->count(),
            ],
        ]);
    }

    public function exportar(Request $request, string $nombre)
    {
        $nombre = urldecode($nombre);
        $fecha  = $request->input('fecha', now()->toDateString());

        $animales = Animal::where('agropecuaria', $nombre)
            ->where('sexo', '!=', 'Macho')
            ->orderBy('codigo_practico')
            ->get(['id', 'codigo_practico', 'identificacion_electronica']);

        $pesajes = PesajeLeche::whereIn('animal_id', $animales->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('animal_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pesajes Leche');

        $sheet->setCellValue('A1', 'Código Práctico');
        $sheet->setCellValue('B1', 'ID Electrónica');
        $sheet->setCellValue('C1', 'Peso AM (L)');
        $sheet->setCellValue('D1', 'Peso PM (L)');

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1d6b4e'],
            ],
        ]);

        $row = 2;
        foreach ($animales as $a) {
            $p = $pesajes->get($a->id);
            if (! $p) continue;
            $sheet->setCellValue("A{$row}", $a->codigo_practico);
            $sheet->setCellValueExplicit("B{$row}", $a->identificacion_electronica, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (float) $p->peso_am);
            $sheet->setCellValue("D{$row}", (float) $p->peso_pm);
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'pesajes_leche_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $nombre) . '_' . $fecha . '.xlsx';
        $temp     = tempnam(sys_get_temp_dir(), 'pesaje');
        (new Xlsx($spreadsheet))->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function animales(Request $request)
    {
        $query = Animal::query()->select('id', 'codigo_practico', 'identificacion_electronica', 'nombre');

        if ($request->filled('agropecuaria')) {
            $query->where('agropecuaria', $request->input('agropecuaria'));
        }

        if ($request->filled('solo_hembras')) {
            $query->where('sexo', '!=', 'Macho');
        }

        if ($request->filled('busqueda')) {
            $s = $request->input('busqueda');
            $query->where(function ($q) use ($s) {
                $q->where('codigo_practico', 'like', "%{$s}%")
                  ->orWhere('identificacion_electronica', 'like', "%{$s}%")
                  ->orWhere('nombre', 'like', "%{$s}%");
            });
        }

        return response()->json($query->orderBy('codigo_practico')->get());
    }

    public function historialGlobal()
    {
        $pesajes = PesajeLeche::with('animal:id,codigo_practico,identificacion_electronica,nombre')
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($p) {
                $total = ($p->peso_am ?? 0) + ($p->peso_pm ?? 0);

                $anterior = PesajeLeche::where('animal_id', $p->animal_id)
                    ->where('fecha', '<', $p->fecha)
                    ->orderBy('fecha', 'desc')
                    ->first();

                $totalAnterior = $anterior ? (($anterior->peso_am ?? 0) + ($anterior->peso_pm ?? 0)) : null;
                $porcentaje = null;
                $diferencia = null;
                if ($totalAnterior !== null && $totalAnterior > 0) {
                    $diferencia = round($total - $totalAnterior, 2);
                    $porcentaje = round((($total - $totalAnterior) / $totalAnterior) * 100, 1);
                }

                return [
                    'id'                         => $p->id,
                    'peso_am'                    => $p->peso_am,
                    'peso_pm'                    => $p->peso_pm,
                    'total'                      => round($total, 2),
                    'fecha'                      => $p->fecha->format('Y-m-d'),
                    'observacion'                => $p->observacion,
                    'animal_id'                  => $p->animal_id,
                    'codigo_practico'            => $p->animal->codigo_practico ?? null,
                    'identificacion_electronica' => $p->animal->identificacion_electronica ?? null,
                    'nombre'                     => $p->animal->nombre ?? null,
                    'diferencia'                 => $diferencia,
                    'porcentaje'                 => $porcentaje,
                ];
            });

        return response()->json($pesajes);
    }

    public function lista(Animal $animale)
    {
        $pesajes = $animale->pesajesLeche()
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'peso_am'     => $p->peso_am,
                'peso_pm'     => $p->peso_pm,
                'total'       => round(($p->peso_am ?? 0) + ($p->peso_pm ?? 0), 2),
                'fecha'       => $p->fecha->format('Y-m-d'),
                'observacion' => $p->observacion,
            ]);

        return response()->json([
            'animal'  => [
                'id'              => $animale->id,
                'nombre'          => $animale->nombre,
                'codigo_practico' => $animale->codigo_practico,
            ],
            'pesajes' => $pesajes,
        ]);
    }

    public function store(Request $request, Animal $animale)
    {
        $data = $request->validate([
            'fecha'       => ['required', 'date'],
            'peso_am'     => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'peso_pm'     => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $fecha = $data['fecha'];

        $pesaje = PesajeLeche::where('animal_id', $animale->id)
            ->whereDate('fecha', $fecha)
            ->first();

        if ($pesaje) {
            if (array_key_exists('peso_am', $data) && $data['peso_am'] !== null) {
                $pesaje->peso_am = $data['peso_am'];
            }
            if (array_key_exists('peso_pm', $data) && $data['peso_pm'] !== null) {
                $pesaje->peso_pm = $data['peso_pm'];
            }
            if (array_key_exists('observacion', $data) && $data['observacion'] !== null) {
                $pesaje->observacion = $data['observacion'];
            }
            $pesaje->save();
        } else {
            $pesaje = PesajeLeche::create([
                'animal_id'   => $animale->id,
                'fecha'       => $fecha,
                'peso_am'     => $data['peso_am'] ?? null,
                'peso_pm'     => $data['peso_pm'] ?? null,
                'observacion' => $data['observacion'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Pesaje de leche registrado correctamente.',
            'pesaje'  => [
                'id'      => $pesaje->id,
                'peso_am' => $pesaje->peso_am,
                'peso_pm' => $pesaje->peso_pm,
                'total'   => round(($pesaje->peso_am ?? 0) + ($pesaje->peso_pm ?? 0), 2),
                'fecha'   => $pesaje->fecha->format('Y-m-d'),
            ],
        ], 201);
    }

    public function destroy(Animal $animale, PesajeLeche $pesaje)
    {
        if ($pesaje->animal_id !== $animale->id) {
            return response()->json(['message' => 'El pesaje no pertenece a este animal.'], 403);
        }

        $pesaje->delete();

        return response()->json(['message' => 'Pesaje de leche eliminado correctamente.']);
    }

    public function comparacion(Animal $animale)
    {
        $ultimos = PesajeLeche::where('animal_id', $animale->id)
            ->orderBy('fecha', 'desc')
            ->limit(2)
            ->get();

        $reciente = $ultimos->first();
        $anterior = $ultimos->count() > 1 ? $ultimos->last() : null;

        $totalReciente = $reciente ? round((float) ($reciente->peso_am ?? 0) + (float) ($reciente->peso_pm ?? 0), 2) : 0;
        $totalAnterior = $anterior ? round((float) ($anterior->peso_am ?? 0) + (float) ($anterior->peso_pm ?? 0), 2) : 0;

        return response()->json([
            'hoy'  => [
                'am'    => (float) ($reciente->peso_am ?? 0),
                'pm'    => (float) ($reciente->peso_pm ?? 0),
                'total' => $totalReciente,
                'fecha' => $reciente?->fecha->format('Y-m-d'),
            ],
            'ayer' => [
                'am'    => (float) ($anterior->peso_am ?? 0),
                'pm'    => (float) ($anterior->peso_pm ?? 0),
                'total' => $totalAnterior,
                'fecha' => $anterior?->fecha->format('Y-m-d'),
            ],
            'diferencia' => round($totalReciente - $totalAnterior, 2),
            'porcentaje' => $totalAnterior > 0 ? round((($totalReciente - $totalAnterior) / $totalAnterior) * 100, 1) : null,
        ]);
    }
}
