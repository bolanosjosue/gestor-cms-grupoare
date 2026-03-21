<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BuffaloBreedController;
use App\Http\Controllers\Admin\BuffaloSaleController;
use App\Http\Controllers\Admin\DashboardController;
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
Route::middleware('auth')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // /admin -> dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('staff', StaffController::class)->except(['show']);
        Route::resource('articles', ArticleController::class)->except(['show']);
        Route::resource('breeds', BuffaloBreedController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('sales', BuffaloSaleController::class)->except(['show']);

        Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])
            ->name('articles.publish');

        Route::post('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])
            ->name('articles.unpublish');
    });
