<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Pesaje;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PesajeController extends Controller
{
    public function seccionData()
    {
        $agropecuarias = Animal::query()
            ->whereNotNull('agropecuaria')
            ->where('agropecuaria', '!=', '')
            ->selectRaw('agropecuaria, COUNT(*) as count')
            ->groupBy('agropecuaria')
            ->orderBy('agropecuaria')
            ->pluck('count', 'agropecuaria');

        $promedios = Animal::query()
            ->whereNotNull('agropecuaria')
            ->where('agropecuaria', '!=', '')
            ->whereNotNull('ultimo_peso')
            ->where('ultimo_peso', '>', 0)
            ->selectRaw('agropecuaria, ROUND(AVG(ultimo_peso), 2) as promedio, COUNT(*) as total')
            ->groupBy('agropecuaria')
            ->orderBy('agropecuaria')
            ->get();

        $animalesConPeso = Animal::whereNotNull('ultimo_peso')->where('ultimo_peso', '>', 0)->pluck('ultimo_peso');
        $rangos = [
            '0–100' => 0, '100–200' => 0, '200–300' => 0, '300–400' => 0,
            '400–500' => 0, '500–600' => 0, '600–700' => 0, '700+' => 0,
        ];
        foreach ($animalesConPeso as $peso) {
            $p = (float) $peso;
            if ($p < 100)      $rangos['0–100']++;
            elseif ($p < 200)  $rangos['100–200']++;
            elseif ($p < 300)  $rangos['200–300']++;
            elseif ($p < 400)  $rangos['300–400']++;
            elseif ($p < 500)  $rangos['400–500']++;
            elseif ($p < 600)  $rangos['500–600']++;
            elseif ($p < 700)  $rangos['600–700']++;
            else               $rangos['700+']++;
        }
        $distribucion = array_filter($rangos, fn ($v) => $v > 0);

        return response()->json([
            'agropecuarias' => $agropecuarias->keys(),
            'agro_counts'   => $agropecuarias,
            'promedios'     => $promedios,
            'distribucion'  => $distribucion,
        ]);
    }

    public function agropecuariaData(Request $request, string $nombre)
    {
        $nombre = urldecode($nombre);
        $fecha  = $request->input('fecha', now()->toDateString());

        $animales = Animal::where('agropecuaria', $nombre)
            ->orderBy('codigo_practico')
            ->get(['id', 'codigo_practico', 'identificacion_electronica', 'nombre']);

        $pesajes = Pesaje::whereIn('animal_id', $animales->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get();

        $pesajesPorAnimal = $pesajes->groupBy('animal_id')->map(fn ($group) => $group->sortByDesc('created_at')->first());

        $rows = $animales->map(function ($a) use ($pesajesPorAnimal) {
            $p = $pesajesPorAnimal->get($a->id);
            if (! $p) return null;
            return [
                'animal_id'                  => $a->id,
                'codigo_practico'            => $a->codigo_practico,
                'identificacion_electronica' => $a->identificacion_electronica,
                'nombre'                     => $a->nombre,
                'peso'                       => (float) $p->peso,
            ];
        })->filter()->values();

        return response()->json([
            'fecha'        => $fecha,
            'agropecuaria' => $nombre,
            'registros'    => $rows->values(),
            'resumen'      => [
                'animales'     => $animales->count(),
                'con_registro' => $rows->count(),
                'total_peso'   => round($rows->sum(fn ($r) => $r['peso'] ?? 0), 2),
            ],
        ]);
    }

    public function exportarAgropecuaria(Request $request, string $nombre)
    {
        $nombre = urldecode($nombre);
        $fecha  = $request->input('fecha', now()->toDateString());

        $animales = Animal::where('agropecuaria', $nombre)
            ->orderBy('codigo_practico')
            ->get(['id', 'codigo_practico', 'identificacion_electronica', 'nombre']);

        $pesajes = Pesaje::whereIn('animal_id', $animales->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get();

        $pesajesPorAnimal = $pesajes->groupBy('animal_id')->map(fn ($group) => $group->sortByDesc('created_at')->first());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pesos');

        $sheet->setCellValue('A1', 'Código Práctico');
        $sheet->setCellValue('B1', 'ID Electrónica');
        $sheet->setCellValue('C1', 'Peso (kg)');

        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1d6b4e'],
            ],
        ]);

        $row = 2;
        foreach ($animales as $a) {
            $p = $pesajesPorAnimal->get($a->id);
            if (! $p) continue;
            $sheet->setCellValue("A{$row}", $a->codigo_practico);
            $sheet->setCellValueExplicit("B{$row}", $a->identificacion_electronica, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (float) $p->peso);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'pesos_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $nombre) . '_' . $fecha . '.xlsx';
        $temp     = tempnam(sys_get_temp_dir(), 'peso');
        (new Xlsx($spreadsheet))->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function animales(Request $request)
    {
        $query = Animal::query()->select('id', 'codigo_practico', 'identificacion_electronica', 'nombre', 'ultimo_peso');

        if ($request->filled('agropecuaria')) {
            $query->where('agropecuaria', $request->input('agropecuaria'));
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
        $pesajes = Pesaje::with('animal:id,codigo_practico,identificacion_electronica,nombre')
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($p) {
                $anterior = Pesaje::where('animal_id', $p->animal_id)
                    ->where(function ($q) use ($p) {
                        $q->where('fecha', '<', $p->fecha)
                          ->orWhere(function ($q2) use ($p) {
                              $q2->where('fecha', $p->fecha)
                                 ->where('created_at', '<', $p->created_at);
                          });
                    })
                    ->orderBy('fecha', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $porcentaje = null;
                $diferencia = null;
                if ($anterior) {
                    $diferencia = round($p->peso - $anterior->peso, 2);
                    $porcentaje = $anterior->peso > 0
                        ? round((($p->peso - $anterior->peso) / $anterior->peso) * 100, 1)
                        : null;
                }

                return [
                    'id'                         => $p->id,
                    'peso'                       => $p->peso,
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
        $pesajes = $animale->pesajes()
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'peso'        => $p->peso,
                'fecha'       => $p->fecha->format('Y-m-d'),
                'observacion' => $p->observacion,
            ]);

        return response()->json([
            'animal'  => [
                'id'              => $animale->id,
                'nombre'          => $animale->nombre,
                'codigo_practico' => $animale->codigo_practico,
                'ultimo_peso'     => $animale->ultimo_peso,
            ],
            'pesajes' => $pesajes,
        ]);
    }

    public function store(Request $request, Animal $animale)
    {
        $data = $request->validate([
            'peso'        => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'fecha'       => ['required', 'date', 'before_or_equal:today'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $pesaje = Pesaje::where('animal_id', $animale->id)
            ->whereDate('fecha', $data['fecha'])
            ->first();

        if ($pesaje) {
            $pesaje->update($data);
        } else {
            $pesaje = $animale->pesajes()->create($data);
        }

        $ultimoPesaje = $animale->pesajes()->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();
        if ($ultimoPesaje) {
            $animale->update(['ultimo_peso' => $ultimoPesaje->peso]);
        }

        return response()->json([
            'message'     => 'Pesaje registrado correctamente.',
            'pesaje'      => [
                'id'          => $pesaje->id,
                'peso'        => $pesaje->peso,
                'fecha'       => $pesaje->fecha->format('Y-m-d'),
                'observacion' => $pesaje->observacion,
            ],
            'ultimo_peso' => $animale->fresh()->ultimo_peso,
        ], 201);
    }

    public function destroy(Animal $animale, Pesaje $pesaje)
    {
        if ($pesaje->animal_id !== $animale->id) {
            return response()->json(['message' => 'El pesaje no pertenece a este animal.'], 403);
        }

        $pesaje->delete();

        $ultimoPesaje = $animale->pesajes()->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();
        $animale->update(['ultimo_peso' => $ultimoPesaje ? $ultimoPesaje->peso : null]);

        return response()->json([
            'message'     => 'Pesaje eliminado correctamente.',
            'ultimo_peso' => $animale->fresh()->ultimo_peso,
        ]);
    }
}
