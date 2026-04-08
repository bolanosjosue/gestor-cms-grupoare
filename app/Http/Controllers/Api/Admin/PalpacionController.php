<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Palpacion;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PalpacionController extends Controller
{
    private const OD_OI_VALUES = ['AT','CL','F6','F8','F9','F10','F12','F14','F15','FM','QF','QL','QO','Q20','Q25'];
    private const UT_VALUES    = ['UNI','IUC','UE','UT','PIA','CALC','MTRI','SALP'];
    private const DIAG_VALUES  = ['E','L','N','P','V','PR'];

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

    public function agropecuariaData(Request $request, string $nombre)
    {
        $nombre = urldecode($nombre);
        $fecha  = $request->input('fecha', now()->toDateString());

        $animales = Animal::where('agropecuaria', $nombre)
            ->where('sexo', '!=', 'Macho')
            ->orderBy('codigo_practico')
            ->get(['id', 'codigo_practico', 'identificacion_electronica', 'nombre']);

        $palpaciones = Palpacion::whereIn('animal_id', $animales->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get();

        $palPorAnimal = $palpaciones->groupBy('animal_id')
            ->map(fn ($group) => $group->sortByDesc('created_at')->first());

        $rows = $animales->map(function ($a) use ($palPorAnimal) {
            $p = $palPorAnimal->get($a->id);
            if (! $p) return null;
            return [
                'animal_id'                  => $a->id,
                'codigo_practico'            => $a->codigo_practico,
                'identificacion_electronica' => $a->identificacion_electronica,
                'nombre'                     => $a->nombre,
                'cc'                         => (float) $p->cc,
                'od'                         => $p->od,
                'oi'                         => $p->oi,
                'ut'                         => $p->ut,
                'diagnostico'                => $p->diagnostico,
            ];
        })->filter()->values();

        return response()->json([
            'fecha'        => $fecha,
            'agropecuaria' => $nombre,
            'registros'    => $rows,
            'resumen'      => [
                'animales'     => $animales->count(),
                'con_registro' => $rows->count(),
            ],
        ]);
    }

    public function exportarAgropecuaria(Request $request, string $nombre)
    {
        $nombre = urldecode($nombre);
        $fecha  = $request->input('fecha', now()->toDateString());

        $animales = Animal::where('agropecuaria', $nombre)
            ->where('sexo', '!=', 'Macho')
            ->orderBy('codigo_practico')
            ->get(['id', 'codigo_practico', 'identificacion_electronica', 'nombre']);

        $palpaciones = Palpacion::whereIn('animal_id', $animales->pluck('id'))
            ->whereDate('fecha', $fecha)
            ->get();

        $palPorAnimal = $palpaciones->groupBy('animal_id')
            ->map(fn ($group) => $group->sortByDesc('created_at')->first());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Palpaciones');

        $sheet->setCellValue('A1', 'Código Práctico');
        $sheet->setCellValue('B1', 'ID Electrónica');
        $sheet->setCellValue('C1', 'CC');
        $sheet->setCellValue('D1', 'O.D');
        $sheet->setCellValue('E1', 'O.I');
        $sheet->setCellValue('F1', 'UT');
        $sheet->setCellValue('G1', 'Diagnóstico');

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1d6b4e'],
            ],
        ]);

        $row = 2;
        foreach ($animales as $a) {
            $p = $palPorAnimal->get($a->id);
            if (! $p) continue;
            $sheet->setCellValue("A{$row}", $a->codigo_practico);
            $sheet->setCellValueExplicit("B{$row}", $a->identificacion_electronica, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (float) $p->cc);
            $sheet->setCellValue("D{$row}", $p->od);
            $sheet->setCellValue("E{$row}", $p->oi);
            $sheet->setCellValue("F{$row}", $p->ut);
            $sheet->setCellValue("G{$row}", $p->diagnostico);
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'palpaciones_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $nombre) . '_' . $fecha . '.xlsx';
        $temp     = tempnam(sys_get_temp_dir(), 'palp');
        (new Xlsx($spreadsheet))->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function animales(Request $request)
    {
        $query = Animal::query()
            ->select('id', 'codigo_practico', 'identificacion_electronica', 'nombre')
            ->where('sexo', '!=', 'Macho');

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
        $palpaciones = Palpacion::with('animal:id,codigo_practico,identificacion_electronica,nombre,agropecuaria')
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(fn ($p) => [
                'id'                         => $p->id,
                'fecha'                      => $p->fecha->format('Y-m-d'),
                'cc'                         => (float) $p->cc,
                'od'                         => $p->od,
                'oi'                         => $p->oi,
                'ut'                         => $p->ut,
                'diagnostico'                => $p->diagnostico,
                'animal_id'                  => $p->animal_id,
                'codigo_practico'            => $p->animal->codigo_practico ?? null,
                'identificacion_electronica' => $p->animal->identificacion_electronica ?? null,
                'nombre'                     => $p->animal->nombre ?? null,
                'agropecuaria'               => $p->animal->agropecuaria ?? null,
            ]);

        return response()->json($palpaciones);
    }

    public function lista(Animal $animale)
    {
        $palpaciones = $animale->palpaciones()
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'cc'          => (float) $p->cc,
                'od'          => $p->od,
                'oi'          => $p->oi,
                'ut'          => $p->ut,
                'diagnostico' => $p->diagnostico,
                'observacion' => $p->observacion,
                'fecha'       => $p->fecha->format('Y-m-d'),
            ]);

        return response()->json([
            'animal'      => [
                'id'              => $animale->id,
                'nombre'          => $animale->nombre,
                'codigo_practico' => $animale->codigo_practico,
            ],
            'palpaciones' => $palpaciones,
        ]);
    }

    public function storeAnimal(Request $request, Animal $animale)
    {
        $data = $request->validate([
            'fecha'       => ['required', 'date', 'before_or_equal:today'],
            'cc'          => ['required', 'numeric', 'min:1', 'max:5'],
            'od'          => ['nullable', 'string', 'in:' . implode(',', self::OD_OI_VALUES)],
            'oi'          => ['nullable', 'string', 'in:' . implode(',', self::OD_OI_VALUES)],
            'ut'          => ['nullable', 'string', 'in:' . implode(',', self::UT_VALUES)],
            'diagnostico' => ['nullable', 'string', 'in:' . implode(',', self::DIAG_VALUES)],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $palpacion = Palpacion::where('animal_id', $animale->id)
            ->whereDate('fecha', $data['fecha'])
            ->first();

        if ($palpacion) {
            $merge = array_filter($data, fn ($v) => $v !== null && $v !== '');
            $palpacion->update($merge);
        } else {
            $palpacion = Palpacion::create($data + ['animal_id' => $animale->id]);
        }

        return response()->json([
            'message'   => 'Palpación registrada correctamente.',
            'palpacion' => [
                'id'          => $palpacion->id,
                'cc'          => (float) $palpacion->cc,
                'od'          => $palpacion->od,
                'oi'          => $palpacion->oi,
                'ut'          => $palpacion->ut,
                'diagnostico' => $palpacion->diagnostico,
                'fecha'       => $palpacion->fecha->format('Y-m-d'),
            ],
        ], 201);
    }

    public function destroyAnimal(Animal $animale, Palpacion $palpacion)
    {
        if ($palpacion->animal_id !== $animale->id) {
            return response()->json(['message' => 'La palpación no pertenece a este animal.'], 403);
        }

        $palpacion->delete();

        return response()->json(['message' => 'Palpación eliminada correctamente.']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'animal_id'   => ['required', 'exists:animales,id'],
            'fecha'       => ['required', 'date', 'before_or_equal:today'],
            'cc'          => ['required', 'numeric', 'min:1', 'max:5'],
            'od'          => ['nullable', 'string', 'in:' . implode(',', self::OD_OI_VALUES)],
            'oi'          => ['nullable', 'string', 'in:' . implode(',', self::OD_OI_VALUES)],
            'ut'          => ['nullable', 'string', 'in:' . implode(',', self::UT_VALUES)],
            'diagnostico' => ['nullable', 'string', 'in:' . implode(',', self::DIAG_VALUES)],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $palpacion = Palpacion::where('animal_id', $data['animal_id'])
            ->whereDate('fecha', $data['fecha'])
            ->first();

        if ($palpacion) {
            $merge = array_filter($data, fn ($v) => $v !== null && $v !== '');
            $palpacion->update($merge);
        } else {
            $palpacion = Palpacion::create($data);
        }

        return response()->json([
            'message'   => 'Palpación registrada correctamente.',
            'palpacion' => [
                'id'          => $palpacion->id,
                'cc'          => (float) $palpacion->cc,
                'od'          => $palpacion->od,
                'oi'          => $palpacion->oi,
                'ut'          => $palpacion->ut,
                'diagnostico' => $palpacion->diagnostico,
                'fecha'       => $palpacion->fecha->format('Y-m-d'),
            ],
        ], 201);
    }

    public function destroy(Palpacion $palpacion)
    {
        $palpacion->delete();

        return response()->json(['message' => 'Palpación eliminada correctamente.']);
    }
}
