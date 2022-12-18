<?php

namespace Tests;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	use CreatesApplication;

	public function setUp(): void
	{
		parent::setUp();
		// Artisan::call('migrate:refresh', ['--seed' => true]);
		// Artisan::call('migrate:refresh');
		Artisan::call('db:seed');
	}

	protected function assertMatch(TestResponse $response, array $expectedResponse): void
	{
		$response->assertStatus($expectedResponse['status']);

		if (isset($expectedResponse['content']))
		{
			$response->assertContent($expectedResponse['content']);
		}

		if (isset($expectedResponse['headers']))
		{
			foreach ($expectedResponse['headers'] as $name => $value)
			{
				$response->assertHeader($name, $value);
			}
		}

		if (!isset($expectedResponse['headers']['content-type']))
		{
			$response->assertHeader('content-type', 'application/json');
		}
	}
}
