<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RemoveFromBasketTest extends TestCase
{
	/**
	 * @return void
	 */
	public function test_remove_from_basket_with_valid_input()
	{
		$response = $this->delete('/api/v1/baskets/1/products/1');

		$response->assertStatus(200);
	}
}
