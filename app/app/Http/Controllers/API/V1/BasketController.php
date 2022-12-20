<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use DateTime;
use stdClass;
use App\Models\Basket;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\APIController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasketController extends APIController
{
	use RefreshDatabase;

	/**
	 * Adds the given product to the given basket.
	 * 
	 * @param int $basketId
	 * @param int $productId
	 * @return JsonResponse
	 */
	public function addItem(int $basketId, int $productId): JsonResponse
	{
		if (($errors = $this->findErrorsInRequest($basketId, $productId, 'add')) !== [])
		{
			return $this->returnErrorResponse(400, $errors);
		}

		/** @var Basket $basket */
		$basket = Basket::find($basketId);

		$basket->products()->sync([
			$productId => ['removal_date' => null]
		], false);

		return $this->returnSuccessResponse(201);
	}

	/**
	 * Removes the given product from the given basket.
	 *
	 * @param int $basketId
	 * @param int $productId
	 * @return JsonResponse
	 */
	public function removeItem(int $basketId, int $productId): JsonResponse
	{
		if (($errors = $this->findErrorsInRequest($basketId, $productId, 'remove')) !== [])
		{
			return $this->returnErrorResponse(400, $errors);
		}

		/** @var Basket $basket */
		$basket = Basket::find($basketId);

		$basket->products()->sync([
			$productId => ['removal_date' => new DateTime()]
		], false);

		return $this->returnSuccessResponse(200);
	}

	/* *************************************** *\
		Support methods
	\* *************************************** */

	/**
	 * Helper method that checks the request is valid.
	 *
	 * @param int $basketId
	 * @param int $productId
	 * @param string $action
	 * @return array<string, string>
	 */
	private function findErrorsInRequest(int $basketId, int $productId, string $action): array
	{
		if (
			($basket = Basket::find($basketId)) === null ||
			$basket->user === null ||
			$basket->user->id !== Auth::id()
		)
		{
			return ['NO_BASKET' => "The basket doesn't exist or doesn't belong to the authenticated user."];
		}

		if ($basket->checkout_date !== null)
		{
			return ['CLOSED_BASKET' => 'The basket is checked out, it can no longer be modified.'];
		}

		if (Product::find($productId) === null)
		{
			$errors['BAD_PRODUCT'] = 'The product does not exist.';
		}
		else
		{
			/** @var ?stdClass $productInBasket */
			$productInBasket = $basket->products->find($productId);

			if ($action === 'add' && $productInBasket !== null && $productInBasket->pivot->removal_date === null)
			{
				$errors['PRODUCT_IS_ALREADY_ADDED'] = "The basket already contains that product.";
			}
			elseif ($action === 'remove' && ($productInBasket === null || $productInBasket->pivot->removal_date !== null))
			{
				$errors['PRODUCT_IS_NOT_AVAILABLE'] = "The basket doesn't contain that product.";
			}
		}

		return $errors ?? [];
	}


	// /**
	//  * Display a listing of the resource.
	//  *
	//  * @return \Illuminate\Http\Response
	//  */
	// public function index()
	// {
	// 	return Basket::all();
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
