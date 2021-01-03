<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
<<<<<<< Updated upstream
=======
Route::post('sale/information', [SaleController::class, "getPurchaseInformationByFlightIds"])->name('sale.information');

Route::post('sale/purchase', [SaleController::class, "purchase"])
    ->name('sale.purchase');
>>>>>>> Stashed changes
