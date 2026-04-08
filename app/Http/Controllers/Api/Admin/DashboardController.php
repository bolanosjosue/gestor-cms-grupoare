<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Article;
use App\Models\BuffaloBreed;
use App\Models\BuffaloSale;
use App\Models\Pesaje;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agropecuarias = Animal::distinct()
            ->whereNotNull('agropecuaria')
            ->pluck('agropecuaria')
            ->sort()
            ->values();

        $filtro = $request->input('agropecuaria');

        $animalQuery = Animal::query();
        if ($filtro) {
            $animalQuery->where('agropecuaria', $filtro);
        }

        $totalAnimalesGlobal = Animal::count();
        $totalAnimales = (clone $animalQuery)->count();
        $hembras       = (clone $animalQuery)->where('sexo', 'Hembra')->count();
        $machos        = (clone $animalQuery)->where('sexo', 'Macho')->count();

        $estadosRepro = (clone $animalQuery)
            ->select('estado_reproductivo', DB::raw('count(*) as total'))
            ->whereNotNull('estado_reproductivo')
            ->where('estado_reproductivo', '!=', '')
            ->groupBy('estado_reproductivo')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['estado' => $r->estado_reproductivo, 'total' => $r->total]);

        $razas = (clone $animalQuery)
            ->select('composicion_racial', DB::raw('count(*) as total'))
            ->whereNotNull('composicion_racial')
            ->where('composicion_racial', '!=', '')
            ->groupBy('composicion_racial')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['raza' => $r->composicion_racial, 'total' => $r->total]);

        $pesoStats = (clone $animalQuery)
            ->whereNotNull('ultimo_peso')
            ->where('ultimo_peso', '>', 0)
            ->selectRaw('AVG(ultimo_peso) as promedio, MIN(ultimo_peso) as minimo, MAX(ultimo_peso) as maximo, COUNT(*) as con_peso')
            ->first();

        $pesajeQuery = Pesaje::query();
        if ($filtro) {
            $pesajeQuery->whereHas('animal', fn ($q) => $q->where('agropecuaria', $filtro));
        }
        $totalPesajes = (clone $pesajeQuery)->count();
        $pesajesMes   = (clone $pesajeQuery)->where('fecha', '>=', now()->subDays(30))->count();

        $driver = DB::getDriverName();
        $mesExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', fecha)"
            : "TO_CHAR(fecha, 'YYYY-MM')";

        $curvaQuery = Pesaje::query()
            ->select(
                DB::raw("{$mesExpr} as mes"),
                DB::raw('AVG(peso) as promedio'),
                DB::raw('COUNT(*) as total')
            );
        if ($filtro) {
            $curvaQuery->whereHas('animal', fn ($q) => $q->where('agropecuaria', $filtro));
        }
        $curvaPeso = $curvaQuery
            ->where('fecha', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($r) => ['mes' => $r->mes, 'promedio' => round($r->promedio, 2), 'total' => $r->total]);

        $topPesados = (clone $animalQuery)
            ->whereNotNull('ultimo_peso')
            ->where('ultimo_peso', '>', 0)
            ->orderByDesc('ultimo_peso')
            ->limit(10)
            ->get(['id', 'codigo_practico', 'nombre', 'ultimo_peso', 'composicion_racial', 'sexo']);

        $ultimosPesajes = Pesaje::with('animal:id,codigo_practico,nombre,agropecuaria')
            ->when($filtro, fn ($q) => $q->whereHas('animal', fn ($q2) => $q2->where('agropecuaria', $filtro)))
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $distribucionAgro = Animal::select('agropecuaria', DB::raw('count(*) as total'))
            ->whereNotNull('agropecuaria')
            ->groupBy('agropecuaria')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['agropecuaria' => $r->agropecuaria, 'total' => $r->total]);

        $totalArticles     = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles     = Article::where('status', 'draft')->count();
        $staffCount        = Staff::count();
        $breedsCount       = BuffaloBreed::count();

        $salesTotal     = BuffaloSale::where('is_active', true)->count();
        $salesAvailable = BuffaloSale::where('is_active', true)->where('status', 'available')->count();
        $salesReserved  = BuffaloSale::where('is_active', true)->where('status', 'reserved')->count();
        $salesSold      = BuffaloSale::where('is_active', true)->where('status', 'sold')->count();

        return response()->json([
            'agropecuarias'       => $agropecuarias,
            'filtro'              => $filtro,
            'totalAnimalesGlobal' => $totalAnimalesGlobal,
            'totalAnimales'       => $totalAnimales,
            'hembras'             => $hembras,
            'machos'              => $machos,
            'estadosRepro'        => $estadosRepro,
            'razas'               => $razas,
            'pesoStats'           => $pesoStats,
            'totalPesajes'        => $totalPesajes,
            'pesajesMes'          => $pesajesMes,
            'curvaPeso'           => $curvaPeso,
            'topPesados'          => $topPesados,
            'ultimosPesajes'      => $ultimosPesajes,
            'distribucionAgro'    => $distribucionAgro,
            'totalArticles'       => $totalArticles,
            'publishedArticles'   => $publishedArticles,
            'draftArticles'       => $draftArticles,
            'staffCount'          => $staffCount,
            'breedsCount'         => $breedsCount,
            'salesTotal'          => $salesTotal,
            'salesAvailable'      => $salesAvailable,
            'salesReserved'       => $salesReserved,
            'salesSold'           => $salesSold,
        ]);
    }
}
