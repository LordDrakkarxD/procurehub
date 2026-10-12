<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BiddingController;
use App\Http\Controllers\Api\BiddingItemController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\SupplyOrderController;

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Rotas protegidas da SPA (API)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('dashboard/stats', [UserController::class, 'stats']);

    Route::get('attachments', [AttachmentController::class, 'index']);
    Route::post('attachments', [AttachmentController::class, 'store']);
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);

    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('biddings', BiddingController::class);
    Route::apiResource('contracts', ContractController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('genres', GenreController::class);
    Route::apiResource('supply-orders', SupplyOrderController::class)
    ->only(['index', 'store', 'show', 'destroy']);

    Route::get('biddings/{bidding}/items', [BiddingItemController::class, 'index']);
    Route::post('biddings/{bidding}/items', [BiddingItemController::class, 'store']);
    Route::put('biddings/{bidding}/items/{item}', [BiddingItemController::class, 'update']);
    Route::delete('biddings/{bidding}/items/{item}', [BiddingItemController::class, 'destroy']);

});

/*
|--------------------------------------------------------------------------
| Catch-all da SPA
|--------------------------------------------------------------------------
*/

Route::view('/{any?}', 'app')->where('any', '.*');

