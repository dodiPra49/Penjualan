<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PelangganApiController;
use App\Http\Controllers\Api\KategoriApiController;

/*
|--------------------------------------------------------------------------
| API Routes - RESTful CRUD Pelanggan
|--------------------------------------------------------------------------
*/

Route::middleware(['pelanggan.api'])->prefix('pelanggan')->group(function () {
    Route::get('/stats/summary', [PelangganApiController::class, 'stats']);
    Route::get('/', [PelangganApiController::class, 'index']);
    Route::post('/', [PelangganApiController::class, 'store']);
    Route::get('/{pelanggan}', [PelangganApiController::class, 'show']);
    Route::put('/{pelanggan}', [PelangganApiController::class, 'update']);
    Route::patch('/{pelanggan}', [PelangganApiController::class, 'update']);
    Route::delete('/{pelanggan}', [PelangganApiController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| API Routes - RESTful CRUD Kategori
|--------------------------------------------------------------------------
*/

Route::middleware(['pelanggan.api'])->prefix('kategori')->group(function () {
    Route::get('/', [KategoriApiController::class, 'index']);
    Route::post('/', [KategoriApiController::class, 'store']);
    Route::get('/{id_kategori}', [KategoriApiController::class, 'show']);
    Route::put('/{id_kategori}', [KategoriApiController::class, 'update']);
    Route::patch('/{id_kategori}', [KategoriApiController::class, 'update']);
    Route::delete('/{id_kategori}', [KategoriApiController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

