<?php

namespace Tests\Feature\Controller\API\V1;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class RemoveFromBasketTest extends TestCase
{
	const VALID_BASKET_ID = 1;
	const INVALID_BASKET_ID = 0;
	const VALID_PRODUCT_ID = 1;
	const INVALID_PRODUCT_ID = 0;

	/* ******************************************** *\
		Happy flow
	\* ******************************************** */

	public function test_remove_from_basket_with_valid_input(): void
	{
		$response = $this->delete('/api/v1/baskets/' . self::VALID_BASKET_ID . '/products/' . self::VALID_PRODUCT_ID);

		$this->assertMatch($response, [
			'status' => 200,
			'content' => json_encode(['response' => 'success']),
		]);

		$matchingItems = DB::table('basket_product')->where([
			['basket_id', '=', self::VALID_BASKET_ID],
			['product_id', '=', self::VALID_PRODUCT_ID]
		])->get();

		$this->assertCount(1, $matchingItems);
		$this->assertNotNull($matchingItems['0']->date_removed);
	}

	/* ******************************************** *\
		Unhappy flow
	\* ******************************************** */

	public function test_remove_from_basket_with_invalid_basket_id(): void
	{
		$response = $this->delete('/api/v1/baskets/' . self::INVALID_BASKET_ID . '/products/' . self::VALID_PRODUCT_ID);

		$this->assertMatch($response, [
			'status' => 400,
			'content' => json_encode(['error' => 'Invalid basketId']),
		]);
	}

	public function test_remove_from_basket_with_invalid_product_id(): void
	{
		$response = $this->delete('/api/v1/baskets/' . self::VALID_BASKET_ID . '/products/' . self::INVALID_PRODUCT_ID);

		$this->assertMatch($response, [
			'status' => 400,
			'content' => json_encode(['error' => 'Invalid productId']),
		]);
	}
}
