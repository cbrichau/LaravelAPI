<?php

namespace Tests\Unit\API\V1;

use Tests\TestCase;

abstract class AbstractEndpointUnitTest extends TestCase
{
	protected array $endpoints = [];

	public function setUp(): void
	{
		parent::setUp();

		$this->endpoints = [
			'/api/v1/baskets/1/products/1' => ['post', 'delete'],
			'/api/v1/products/download-losses' => ['get']
		];
	}
}
