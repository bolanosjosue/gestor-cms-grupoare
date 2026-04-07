<?php

use App\Http\Controllers\Admin\AnimalController;
use App\Http\Controllers\Admin\AnimalImportController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BuffaloBreedController;
use App\Http\Controllers\Admin\BuffaloSaleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\Admin\StaffController;
use App\Models\Article;
use App\Models\BuffaloSale;
use App\Models\Staff;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// LANDING PÚBLICA (URLs limpias)
// ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    $articles = Article::query()
        ->where('status', 'published')
        ->orderByDesc('published_at')
        ->take(3)
        ->get();

    $staff = Staff::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    $sales = BuffaloSale::query()
        ->with(['breed', 'fatherBreed', 'motherBreed'])
        ->where('is_active', true)
        ->whereIn('status', ['available', 'reserved'])
        ->orderByDesc('created_at')
        ->take(3)
        ->get();

    return view('landing.index', compact('articles', 'staff', 'sales'));
})->name('landing.home');

Route::get('/about', function () {
    $staff = Staff::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return view('landing.about', compact('staff'));
})->name('landing.about');

Route::get('/personal', function () {
    $staff = Staff::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return view('landing.personal', compact('staff'));
})->name('landing.personal');

Route::get('/ventas', function () {
    $sales = BuffaloSale::query()
        ->with(['breed', 'fatherBreed', 'motherBreed'])
        ->where('is_active', true)
        ->whereIn('status', ['available', 'reserved'])
        ->orderByDesc('created_at')
        ->get();

    return view('landing.ventas', compact('sales'));
})->name('landing.ventas');

Route::view('/services', 'landing.services')->name('landing.services');
Route::view('/gallery', 'landing.gallery')->name('landing.gallery');
Route::view('/contact', 'landing.contact')->name('landing.contact');

// Si querés una página "blog" estática de la plantilla:
Route::get('/recursos', function () {
    $articles = Article::query()
        ->where('status', 'published')
        ->orderByDesc('published_at')
        ->paginate(6);

    return view('landing.recursos', compact('articles'));
})->name('landing.recursos');

Route::get('/blog/{slug}', function ($slug) {

    $path = storage_path("app/public/blog/$slug/index.html");

    if (!File::exists($path)) {
        abort(404);
    }

    return response()->file($path);

})->name('landing.blog.show');

// ─────────────────────────────────────────────────────────────
// AUTH
// ─────────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ─────────────────────────────────────────────────────────────
// ADMIN (protegido)
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'check_activo'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // /admin -> dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // ─── Módulos con permisos granulares ───
        Route::resource('staff', StaffController::class)->except(['show'])->middleware('permiso:personal,puede_ver');
        Route::resource('articles', ArticleController::class)->except(['show'])->middleware('permiso:articulos,puede_ver');
        Route::resource('breeds', BuffaloBreedController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('permiso:razas,puede_ver');
        Route::resource('sales', BuffaloSaleController::class)->except(['show'])->middleware('permiso:ventas,puede_ver');

        Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])
            ->name('articles.publish')->middleware('permiso:articulos,puede_editar');

        Route::post('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])
            ->name('articles.unpublish')->middleware('permiso:articulos,puede_editar');

        // Animales (ganado/búfalos) — rutas especiales antes del resource
        Route::middleware('permiso:animales,puede_ver')->group(function () {
            Route::get('animales/filtros/opciones', [AnimalController::class, 'filterOptions'])->name('animales.filtros');
            Route::get('animales/exportacion', [AnimalController::class, 'exportacion'])->name('animales.exportacion');
            Route::get('animales/exportar', [AnimalController::class, 'exportar'])->name('animales.exportar');
            Route::get('animales/importacion', [AnimalImportController::class, 'index'])->name('animales.importacion');
            Route::get('animales/importacion/historial', [AnimalImportController::class, 'historial'])->name('animales.importacion.historial');
            Route::post('animales/importar', [AnimalImportController::class, 'importar'])->name('animales.importar');
            Route::resource('animales', AnimalController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        });

        // ─── Gestión de usuarios — solo Super Admin ───
        Route::middleware('super_admin')->group(function () {
            Route::get('usuarios', [PersonalController::class, 'index'])->name('usuarios.index');
            Route::get('usuarios/{usuario}', [PersonalController::class, 'show'])->name('usuarios.show');
            Route::post('usuarios', [PersonalController::class, 'store'])->name('usuarios.store');
            Route::put('usuarios/{usuario}', [PersonalController::class, 'update'])->name('usuarios.update');
            Route::put('usuarios/{usuario}/desactivar', [PersonalController::class, 'toggleActivo'])->name('usuarios.toggleActivo');
        });

        // ─── Permisos del usuario autenticado ───
        Route::get('usuario/permisos', [PermisoController::class, 'misPermisos'])->name('permisos.mis');
        Route::get('usuario/permisos/campos-animales', [PermisoController::class, 'camposAnimales'])->name('permisos.campos');
    });
