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
	 * @OA\Post(
	 *   security={{"sanctum": {}}},
	 *   operationId="addItem",
	 *   tags={"Basket"},
	 *   path="/api/v1/baskets/{basketId}/products/{productId}",
	 *   summary="Add a product to a basket.",
	 *   @OA\Parameter(name="basketId", in="path", required=true, description="The basket's id", example="2"),
	 *   @OA\Parameter(name="productId", in="path", required=true, description="The product's id", example="1"),
	 *   @OA\RequestBody(
	 *     @OA\JsonContent(example=""),
	 *   ),
	 *   @OA\Response(
	 *     response=200,
	 *     description="Item added to basket.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="true"),
	 *       @OA\Property(property="data", type="array", example={},
	 *         @OA\Items(type="string"),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=400,
	 *     description="Bad request.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="false"),
	 *       @OA\Property(property="errors", type="array", example={
	 *           {"NO_BASKET": "The basket doesn't exist or doesn't belong to the authenticated user."},
	 *           {"CLOSED_BASKET": "The basket is checked out, it can no longer be modified."},
	 *           {"BAD_PRODUCT": "The product does not exist."},
	 *           {"PRODUCT_IS_ALREADY_ADDED": "The basket already contains that product."},
	 *         },
	 *         @OA\Items(type="string"),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=401,
	 *     description="Access denied.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="message", type="string", example="Unauthenticated."),
	 *     ),
	 *   ),
	 * )
	 *
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
	 * @OA\Delete(
	 *   security={{"sanctum": {}}},
	 *   operationId="removeItem",
	 *   tags={"Basket"},
	 *   path="/api/v1/baskets/{basketId}/products/{productId}",
	 *   summary="Remove a product from a basket.",
	 *   @OA\Parameter(name="basketId", in="path", required=true, description="The basket's id", example="2"),
	 *   @OA\Parameter(name="productId", in="path", required=true, description="The product's id", example="1"),
	 *   @OA\RequestBody(
	 *     @OA\JsonContent(example=""),
	 *   ),
	 *   @OA\Response(
	 *     response=200,
	 *     description="Item removed from basket.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="true"),
	 *       @OA\Property(property="data", type="array", example={},
	 *         @OA\Items(type="string"),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=400,
	 *     description="Bad request.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="false"),
	 *       @OA\Property(property="errors", type="array", example={
	 *           {"NO_BASKET": "The basket doesn't exist or doesn't belong to the authenticated user."},
	 *           {"CLOSED_BASKET": "The basket is checked out, it can no longer be modified."},
	 *           {"BAD_PRODUCT": "The product does not exist."},
	 *           {"PRODUCT_IS_NOT_AVAILABLE": "The basket doesn't contain that product."},
	 *         },
	 *         @OA\Items(type="string"),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=401,
	 *     description="Access denied.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="message", type="string", example="Unauthenticated."),
	 *     ),
	 *   ),
	 * )
	 *
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
		) {
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
}
