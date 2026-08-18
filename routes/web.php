<?php

use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/ping', fn () => response('pong', 200));

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'ok',
            'database' => 'connected',
        ]);
    } catch (\Throwable $exception) {
        return response()->json([
            'status' => 'error',
            'database' => 'failed',
            'message' => $exception->getMessage(),
        ], 500);
    }
});

Route::prefix('tienda')->name('tienda.')->group(function () {
    Route::get('/', [TiendaController::class, 'catalogo'])->name('catalogo');
    Route::get('/carrito', [TiendaController::class, 'carrito'])->name('carrito');
    Route::post('/producto/{producto}/agregar', [TiendaController::class, 'agregar'])->name('agregar');
    Route::post('/producto/{producto}/quitar', [TiendaController::class, 'quitar'])->name('quitar');
    Route::get('/checkout', [TiendaController::class, 'checkout'])->name('checkout');
    Route::post('/confirmar', [TiendaController::class, 'confirmar'])->name('confirmar');
});
