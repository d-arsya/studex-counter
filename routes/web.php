<?php

use App\Http\Controllers\MessageController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json('OK');
});
Route::withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    Route::post('webhook', function (Request $request) {
        $data = $request->data;
        try {
            DB::table('datas')->insert(['webhookdata' => json_encode($data)]);
            Artisan::call('decode:data');
            return response()->json(["success" => true]);
        } catch (\Throwable $th) {
            return response()->json(["success" => false, "message" => $th->getMessage()]);
        }
    });
    Route::get('today', [MessageController::class, 'today']);
    Route::get('weekly', [MessageController::class, 'weekly']);
});
