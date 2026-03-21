<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\BuffaloBreed;
use App\Models\BuffaloSale;
use App\Models\Staff;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Artículos ──
        $totalArticles    = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles     = Article::where('status', 'draft')->count();

        // ── Personal & Razas ──
        $staffCount  = Staff::count();
        $breedsCount = BuffaloBreed::count();

        // ── Ventas: conteos por estado ──
        $salesTotal     = BuffaloSale::where('is_active', true)->count();
        $salesAvailable = BuffaloSale::where('is_active', true)->where('status', 'available')->count();
        $salesReserved  = BuffaloSale::where('is_active', true)->where('status', 'reserved')->count();
        $salesSold      = BuffaloSale::where('is_active', true)->where('status', 'sold')->count();

        // ── Top 5 destacadas (más caras disponibles/reservadas) ──
        $topSales = BuffaloSale::with('breed')
            ->where('is_active', true)
            ->whereIn('status', ['available', 'reserved'])
            ->orderByDesc('price_crc')
            ->take(5)
            ->get();

        // ── Últimas 5 ventas registradas ──
        $latestSales = BuffaloSale::with('breed')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // ── Últimos artículos publicados ──
        $latestPublished = Article::where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalArticles',
            'publishedArticles',
            'draftArticles',
            'staffCount',
            'breedsCount',
            'salesTotal',
            'salesAvailable',
            'salesReserved',
            'salesSold',
            'topSales',
            'latestSales',
            'latestPublished'
        ));
    }
}

