<?php

use App\Http\Controllers\Api\Admin\AnimalController;
use App\Http\Controllers\Api\Admin\AnimalImportController;
use App\Http\Controllers\Api\Admin\AppPesajeController;
use App\Http\Controllers\Api\Admin\AppPesoController;
use App\Http\Controllers\Api\Admin\AppVeterinarioController;
use App\Http\Controllers\Api\Admin\ArticleController;
use App\Http\Controllers\Api\Admin\AuditoriaController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\BuffaloBreedController;
use App\Http\Controllers\Api\Admin\BuffaloSaleController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\PalpacionController;
use App\Http\Controllers\Api\Admin\PersonalController;
use App\Http\Controllers\Api\Admin\PermisoController;
use App\Http\Controllers\Api\Admin\PesajeController;
use App\Http\Controllers\Api\Admin\PesajeLecheController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\PublicArticleController;
use App\Http\Controllers\Api\PublicStaffController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// RUTAS PÚBLICAS (sin autenticación)
// ─────────────────────────────────────────────────────────────
Route::prefix('public')->group(function () {
    Route::get('staff', [PublicStaffController::class, 'index']);
    Route::get('articles', [PublicArticleController::class, 'index']);
    Route::get('articles/{slug}', [PublicArticleController::class, 'show']);
});

// ─────────────────────────────────────────────────────────────
// AUTH (login no requiere token)
// ─────────────────────────────────────────────────────────────
Route::post('auth/login', [AuthController::class, 'login']);

// ─────────────────────────────────────────────────────────────
// RUTAS PROTEGIDAS (Sanctum + check_activo)
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'check_activo'])->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user', [AuthController::class, 'user']);

    Route::prefix('admin')->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->middleware('permiso:dashboard,puede_ver');

        // ─── Módulos con permisos granulares ───

        // Staff (personal público)
        Route::middleware('permiso:personal,puede_ver')->group(function () {
            Route::get('staff', [StaffController::class, 'index']);
            Route::post('staff', [StaffController::class, 'store']);
            Route::put('staff/{staff}', [StaffController::class, 'update']);
            Route::delete('staff/{staff}', [StaffController::class, 'destroy']);
        });

        // Artículos
        Route::middleware('permiso:articulos,puede_ver')->group(function () {
            Route::get('articles', [ArticleController::class, 'index']);
            Route::get('articles/all', [ArticleController::class, 'all']);
            Route::post('articles', [ArticleController::class, 'store']);
            Route::get('articles/{article}', [ArticleController::class, 'show']);
            Route::put('articles/{article}', [ArticleController::class, 'update']);
            Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
            Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])
                ->middleware('permiso:articulos,puede_editar');
            Route::post('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])
                ->middleware('permiso:articulos,puede_editar');
        });

        // Razas
        Route::middleware('permiso:razas,puede_ver')->group(function () {
            Route::get('breeds', [BuffaloBreedController::class, 'index']);
            Route::post('breeds', [BuffaloBreedController::class, 'store']);
            Route::put('breeds/{breed}', [BuffaloBreedController::class, 'update']);
            Route::delete('breeds/{breed}', [BuffaloBreedController::class, 'destroy']);
        });

        // Ventas
        Route::middleware('permiso:ventas,puede_ver')->group(function () {
            Route::get('sales', [BuffaloSaleController::class, 'index']);
            Route::get('sales/breeds', [BuffaloSaleController::class, 'breeds']);
            Route::post('sales', [BuffaloSaleController::class, 'store']);
            Route::get('sales/{sale}', [BuffaloSaleController::class, 'show']);
            Route::put('sales/{sale}', [BuffaloSaleController::class, 'update']);
            Route::delete('sales/{sale}', [BuffaloSaleController::class, 'destroy']);
        });

        // ─── Animales ───
        Route::middleware('permiso:animales,puede_ver')->group(function () {
            // CRUD animales
            Route::get('animales', [AnimalController::class, 'index']);
            Route::get('animales/filtros/opciones', [AnimalController::class, 'filterOptions']);
            Route::get('animales/exportar', [AnimalController::class, 'exportar']);
            Route::post('animales', [AnimalController::class, 'store']);
            Route::get('animales/{animale}', [AnimalController::class, 'show']);
            Route::put('animales/{animale}', [AnimalController::class, 'update']);
            Route::delete('animales/{animale}', [AnimalController::class, 'destroy']);

            // Importación
            Route::get('animales-importacion/historial', [AnimalImportController::class, 'historial']);
            Route::post('animales-importacion/importar', [AnimalImportController::class, 'importar']);

            // ─── Pesos (sección) ───
            Route::get('pesos/seccion', [PesajeController::class, 'seccionData']);
            Route::get('pesos/animales', [PesajeController::class, 'animales']);
            Route::get('pesos/historial', [PesajeController::class, 'historialGlobal']);
            Route::get('pesos/agropecuaria/{nombre}/data', [PesajeController::class, 'agropecuariaData']);
            Route::get('pesos/agropecuaria/{nombre}/exportar', [PesajeController::class, 'exportarAgropecuaria']);

            // Pesos por animal
            Route::get('animales/{animale}/pesos', [PesajeController::class, 'lista']);
            Route::post('animales/{animale}/pesos', [PesajeController::class, 'store']);
            Route::delete('animales/{animale}/pesos/{pesaje}', [PesajeController::class, 'destroy']);

            // ─── Palpaciones (sección) ───
            Route::get('palpaciones/seccion', [PalpacionController::class, 'seccionData']);
            Route::get('palpaciones/animales', [PalpacionController::class, 'animales']);
            Route::get('palpaciones/historial', [PalpacionController::class, 'historialGlobal']);
            Route::get('palpaciones/agropecuaria/{nombre}/data', [PalpacionController::class, 'agropecuariaData']);
            Route::get('palpaciones/agropecuaria/{nombre}/exportar', [PalpacionController::class, 'exportarAgropecuaria']);
            Route::post('palpaciones', [PalpacionController::class, 'store']);
            Route::delete('palpaciones/{palpacion}', [PalpacionController::class, 'destroy']);

            // Palpaciones por animal
            Route::get('animales/{animale}/palpaciones', [PalpacionController::class, 'lista']);
            Route::post('animales/{animale}/palpaciones', [PalpacionController::class, 'storeAnimal']);
            Route::delete('animales/{animale}/palpaciones/{palpacion}', [PalpacionController::class, 'destroyAnimal']);

            // ─── Pesajes de leche (sección) ───
            Route::get('pesajes/seccion', [PesajeLecheController::class, 'seccionData']);
            Route::get('pesajes/dashboard-data', [PesajeLecheController::class, 'dashboardData']);
            Route::get('pesajes/animales', [PesajeLecheController::class, 'animales']);
            Route::get('pesajes/historial', [PesajeLecheController::class, 'historialGlobal']);
            Route::get('pesajes/agropecuaria/{nombre}/data', [PesajeLecheController::class, 'agropecuariaData']);
            Route::get('pesajes/agropecuaria/{nombre}/exportar', [PesajeLecheController::class, 'exportar']);

            // Pesajes de leche por animal
            Route::get('animales/{animale}/pesajes', [PesajeLecheController::class, 'lista']);
            Route::get('animales/{animale}/pesajes/comparacion', [PesajeLecheController::class, 'comparacion']);
            Route::post('animales/{animale}/pesajes', [PesajeLecheController::class, 'store']);
            Route::delete('animales/{animale}/pesajes/{pesaje}', [PesajeLecheController::class, 'destroy']);
        });

        // ─── Gestión de usuarios — solo Super Admin ───
        Route::middleware('super_admin')->group(function () {
            Route::get('usuarios', [PersonalController::class, 'index']);
            Route::get('usuarios/{usuario}', [PersonalController::class, 'show']);
            Route::post('usuarios', [PersonalController::class, 'store']);
            Route::put('usuarios/{usuario}', [PersonalController::class, 'update']);
            Route::put('usuarios/{usuario}/desactivar', [PersonalController::class, 'toggleActivo']);
        });

        // ─── Auditoría ───
        Route::middleware('permiso:auditoria,puede_ver')->group(function () {
            Route::get('auditoria/filtros', [AuditoriaController::class, 'filtros']);
            Route::get('auditoria/datos', [AuditoriaController::class, 'datos']);
            Route::get('auditoria/grupo', [AuditoriaController::class, 'grupo']);
        });

        // ─── Permisos del usuario autenticado ───
        Route::get('usuario/permisos', [PermisoController::class, 'misPermisos']);
        Route::get('usuario/permisos/campos-animales', [PermisoController::class, 'camposAnimales']);

        // ─── App (módulo empleados) ───
        Route::prefix('app')->group(function () {
            Route::middleware('permiso:app_pesos,puede_ver')->group(function () {
                Route::get('pesos/agropecuarias', [AppPesoController::class, 'agropecuarias']);
                Route::get('pesos/buscar', [AppPesoController::class, 'buscar']);
                Route::post('pesos', [AppPesoController::class, 'store']);
                Route::delete('pesos', [AppPesoController::class, 'destroy']);
            });

            Route::middleware('permiso:app_pesajes,puede_ver')->group(function () {
                Route::get('pesajes/agropecuarias', [AppPesajeController::class, 'agropecuarias']);
                Route::get('pesajes/buscar', [AppPesajeController::class, 'buscar']);
                Route::post('pesajes', [AppPesajeController::class, 'store']);
                Route::delete('pesajes', [AppPesajeController::class, 'destroy']);
                Route::post('pesajes/nacimiento', [AppPesajeController::class, 'nacimiento']);
            });

            Route::middleware('permiso:app_veterinario,puede_ver')->group(function () {
                Route::get('veterinario/buscar', [AppVeterinarioController::class, 'buscar']);
                Route::post('veterinario', [AppVeterinarioController::class, 'store']);
                Route::delete('veterinario', [AppVeterinarioController::class, 'destroy']);
            });
        });
    });
});

