<?php

declare(strict_types=1);

namespace Tests\Feature\Controller\API\V1;

use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Controller\API\V1\AbstractEndpointFeatureTest;

class AddToAndRemoveFromBasketTest extends AbstractEndpointFeatureTest
{
	private function callEndpoint(string $method, int $basketId, int $productId): TestResponse
	{
		return $this->request($method, '/api/v1/baskets/' . $basketId . '/products/' . $productId);
	}

	/* ******************************************** *\
		Happy flow
	\* ******************************************** */

	public function test_add_to_basket_with_valid_input(): void
	{
		$validBasketId = (int) $_ENV['INTERNAL_USER_OPEN_BASKET_ID'];
		$validProductId['post'] = (int) $_ENV['IN_BASKET_BUT_REMOVED_PRODUCT_ID'];
		$validProductId['delete'] = (int) $_ENV['IN_BASKET_PRODUCT_ID'];

		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, $validBasketId, $validProductId[$method]);

			$this->assertValidJSONResponse($response, [
				'status' => ($method === 'post' ? 201 : 200)
			]);

			/** @var array<stdClass> $matchingItems */
			$matchingItems = DB::table('basket_product')->where([
				['basket_id', '=', $validBasketId],
				['product_id', '=', $validProductId[$method]]
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
		$invalidBasketIds = [0, (int) $_ENV['EXTERNAL_USER_OPEN_BASKET_ID']];
		$validProductId = 1;

		foreach (['post', 'delete'] as $method)
		{
			foreach ($invalidBasketIds as $invalidBasketId)
			{
				$response = $this->callEndpoint($method, $invalidBasketId, $validProductId);

				$this->assertValidJSONResponse($response, [
					'status' => 400,
					'errors' => ['NO_BASKET'],
				]);
			}
		}
	}

	public function test_add_and_remove_item_with_checked_out_basket(): void
	{
		$invalidBasketId = (int) $_ENV['INTERNAL_USER_CLOSED_BASKET_ID'];
		$validProductId = 1;

		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, $invalidBasketId, $validProductId);

			$this->assertValidJSONResponse($response, [
				'status' => 400,
				'errors' => ['CLOSED_BASKET'],
			]);
		}
	}

	public function test_add_and_remove_item_with_invalid_product_id(): void
	{
		$validBasketId = (int) $_ENV['INTERNAL_USER_OPEN_BASKET_ID'];
		$invalidProductId = 0;

		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, $validBasketId, $invalidProductId);

			$this->assertValidJSONResponse($response, [
				'status' => 400,
				'errors' => ['BAD_PRODUCT'],
			]);
		}
	}

	public function test_duplicate_add_and_remove_item(): void
	{
		$validBasketId = (int) $_ENV['INTERNAL_USER_OPEN_BASKET_ID'];
		$validProductId['post'] = (int) $_ENV['NOT_IN_BASKET_PRODUCT_ID'];
		$validProductId['delete'] = (int) $_ENV['IN_BASKET_PRODUCT_ID'];

		foreach (['post', 'delete'] as $method)
		{
			$response = $this->callEndpoint($method, $validBasketId, $validProductId[$method]);

			$this->assertValidJSONResponse($response, [
				'status' => ($method === 'post' ? 201 : 200)
			]);

			$duplicate = $this->callEndpoint($method, $validBasketId, $validProductId[$method]);

			$this->assertValidJSONResponse($duplicate, [
				'status' => 400,
				'errors' => [($method === 'post' ? 'PRODUCT_IS_ALREADY_ADDED' : 'PRODUCT_IS_NOT_AVAILABLE')],
			]);
		}
	}
}
