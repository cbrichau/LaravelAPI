<?php

namespace App\Http\Controllers\API\V1;

use DateTime;
use App\Models\Basket;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBasketRequest;
use App\Http\Requests\UpdateBasketRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class BasketController extends Controller
{
	use RefreshDatabase;

	/**
	 * Add the given product to the given basket.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addItem(string $basketId, string $productId): JsonResponse
	{
		if (($basket = Basket::find($basketId)) === null)
			return response()->json(['error' => 'Invalid basketId'], 400);

		if (($product = Product::find($productId)) === null)
			return response()->json(['error' => 'Invalid productId'], 400);

		$basket->products()->sync([
			$productId => ['date_removed' => null]
		], false);

		return response()->json([
			'response' => 'success'
		], 201);
	}

	/**
	 * Removes the given product from the given basket.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function removeItem(string $basketId, string $productId): JsonResponse
	{
		if (($basket = Basket::find($basketId)) === null)
			return response()->json(['error' => 'Invalid basketId'], 400);

		if (($product = Product::find($productId)) === null)
			return response()->json(['error' => 'Invalid productId'], 400);

		$basket->products()->sync([
			$productId => ['date_removed' => new DateTime()]
		], false);

		return response()->json([
			'response' => 'success'
		]);
	}

	// /**
	//  * Display a listing of the resource.
	//  *
	//  * @return \Illuminate\Http\Response
	//  */
	// public function index()
	// {
	// 	//
	// }

	// /**
	//  * Show the form for creating a new resource.
	//  *
	//  * @return \Illuminate\Http\Response
	//  */
	// public function create()
	// {
	// 	//
	// }

	// /**
	//  * Store a newly created resource in storage.
	//  *
	//  * @param  \App\Http\Requests\StoreBasketRequest  $request
	//  * @return \Illuminate\Http\Response
	//  */
	// public function store(StoreBasketRequest $request)
	// {
	// 	//
	// }

	// /**
	//  * Display the specified resource.
	//  *
	//  * @param  \App\Models\Basket  $basket
	//  * @return \Illuminate\Http\Response
	//  */
	// public function show(Basket $basket)
	// {
	// 	//
	// }

	// /**
	//  * Show the form for editing the specified resource.
	//  *
	//  * @param  \App\Models\Basket  $basket
	//  * @return \Illuminate\Http\Response
	//  */
	// public function edit(Basket $basket)
	// {
	// 	//
	// }

	// /**
	//  * Update the specified resource in storage.
	//  *
	//  * @param  \App\Http\Requests\UpdateBasketRequest  $request
	//  * @param  \App\Models\Basket  $basket
	//  * @return \Illuminate\Http\Response
	//  */
	// public function update(UpdateBasketRequest $request, Basket $basket)
	// {
	// 	//
	// }

	// /**
	//  * Remove the specified resource from storage.
	//  *
	//  * @param  \App\Models\Basket  $basket
	//  * @return \Illuminate\Http\Response
	//  */
	// public function destroy(Basket $basket)
	// {
	// 	//
	// }
}
