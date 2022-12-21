<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\V1\BasketController;
use App\Http\Controllers\API\V1\ProductController;

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

Route::group(
	[
		'prefix' => 'auth',
	],
	function ()
	{
		Route::post('sign-up', [AuthController::class, 'signUp']);
		Route::post('sign-in', [AuthController::class, 'signIn']);
	}
);

// Public (customer) routes
Route::group(
	[
		'prefix' => 'v1',
		'middleware' => ['auth:sanctum']
	],
	function ()
	{
		Route::post('baskets/{paramBasketId}/products/{paramProductId}', [BasketController::class, 'addItem']);
		Route::delete('baskets/{paramBasketId}/products/{paramProductId}', [BasketController::class, 'removeItem']);
	}
);

// Internal (employee) routes
Route::group(
	[
		'prefix' => 'v1',
		'middleware' => ['auth:sanctum', 'can:access_internal_features']
	],
	function ()
	{
		Route::get('products/download-losses', [ProductController::class, 'downloadLosses']);
	}
);
