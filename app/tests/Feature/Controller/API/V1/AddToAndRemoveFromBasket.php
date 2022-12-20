<?php

namespace Tests\Feature\Controller\API\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Controller\API\V1\AbstractEndpointFeatureTest;

class AddToAndRemoveFromBasket extends AbstractEndpointFeatureTest
{
	const BASKET_INVALID_ID = 0;
	const CHECKED_OUT_BASKET_ID = 1;
	const BASKET_VALID_ID = 2;
	const OTHER_USER_BASKET_ID = 3;

	const PRODUCT_INVALID_ID = 0;
	const PRODUCT_VALID_ID = 2;

	private function callEndpoint(string $method, int $basketId, int $productId): TestResponse
	{
		return $this->request($method, '/api/v1/baskets/' . $basketId . '/products/' . $productId);
	}

	/* ******************************************** *\
		Happy flow
	\* ******************************************** */

	public function test_add_to_basket_with_valid_input(): void
	{
		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, self::BASKET_VALID_ID,  self::PRODUCT_VALID_ID);

			$this->assertValidJSONResponse($response, [
				'status' => ($method === 'post' ? 201 : 200)
			]);

			$matchingItems = DB::table('basket_product')->where([
				['basket_id', '=', self::BASKET_VALID_ID],
				['product_id', '=', self::PRODUCT_VALID_ID]
			])->get();

			$this->assertCount(1, $matchingItems);

			if ($method === 'post')
			{
				$this->assertNull($matchingItems['0']->removal_date);
			}
			else
			{
				$this->assertNotNull($matchingItems['0']->removal_date);
			}
		}
	}

	/* ******************************************** *\
		Unhappy flow
	\* ******************************************** */

	public function test_add_and_remove_item_with_invalid_basket_id(): void
	{
		foreach (['post', 'delete'] as $method)
		{
			foreach ([self::BASKET_INVALID_ID, self::OTHER_USER_BASKET_ID] as $basketId)
			{
				$response = $this->callEndpoint($method, $basketId,  self::PRODUCT_VALID_ID);

				$this->assertValidJSONResponse($response, [
					'status' => 400,
					'errors' => ['NO_BASKET'],
				]);
			}
		}
	}

	public function test_add_and_remove_item_with_checked_out_basket(): void
	{
		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, self::CHECKED_OUT_BASKET_ID,  self::PRODUCT_VALID_ID);

			$this->assertValidJSONResponse($response, [
				'status' => 400,
				'errors' => ['CLOSED_BASKET'],
			]);
		}
	}

	public function test_add_and_remove_item_with_invalid_product_id(): void
	{
		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, self::BASKET_VALID_ID,  self::PRODUCT_INVALID_ID);

			$this->assertValidJSONResponse($response, [
				'status' => 400,
				'errors' => ['BAD_PRODUCT'],
			]);
		}
	}

	public function test_duplicate_add_and_remove_item(): void
	{
		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, self::BASKET_VALID_ID,  self::PRODUCT_VALID_ID);

			$this->assertValidJSONResponse($response, [
				'status' => ($method === 'post' ? 201 : 200)
			]);

			$duplicate = $this->callEndpoint($method, self::BASKET_VALID_ID,  self::PRODUCT_VALID_ID);

			$this->assertValidJSONResponse($duplicate, [
				'status' => 400,
				'errors' => [($method === 'post' ? 'PRODUCT_IS_ALREADY_ADDED' : 'PRODUCT_IS_NOT_AVAILABLE')],
			]);
		}
	}
}
