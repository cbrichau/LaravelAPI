<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddToBasketTest extends TestCase
{
	/**
	 * @return void
	 */
	public function test_add_to_basket_with_valid_input()
	{
		$response = $this->post('/api/v1/baskets/1/products/1');

		$response->assertStatus(201);
	}
}
