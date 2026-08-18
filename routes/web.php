<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
