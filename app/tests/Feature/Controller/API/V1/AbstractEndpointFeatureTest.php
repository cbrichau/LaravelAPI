<?php

declare(strict_types=1);

namespace Tests\Feature\Controller\API\V1;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\TestResponse;

abstract class AbstractEndpointFeatureTest extends TestCase
{
	/**
	 * Helper method to make a request with the required JSON headers and authentication.
	 * 
	 * @param string $method
	 * @param string $url
	 * @return TestResponse
	 */
	protected function request(string $method, string $url): TestResponse
	{
		/** @var User $authenticatedUser */
		$authenticatedUser = User::find(1);

		return $this->actingAs($authenticatedUser)->{$method . 'Json'}($url);
	}

	/**
	 * Helper method to check the test's response broadly matches the expected response
	 * (= same status, content, and headers).
	 * 
	 * @param TestResponse $response
	 * @param array<string, array<int, string>|int> $expected
	 * @return void
	 */
	protected function assertValidJSONResponse(TestResponse $response, array $expected): void
	{
		// Status
		$response->assertStatus((int) $expected['status']);

		// Content
		$content = $this->extractResponseContent($response);

		if (in_array($expected['status'], [200, 201]))
		{
			$this->assertTrue($content->success);
			$this->assertArrayHasKey('data', (array) $content);
		}
		else
		{
			$this->assertFalse($content->success);
			$this->assertSame($expected['errors'], array_keys(get_object_vars($content->errors)));
		}

		// Headers
		if (!isset($expected['headers']['content-type']))
		{
			$response->assertHeader('content-type', 'application/json');
		}

		if (isset($expected['headers']))
		{
			/** @var array<string, string> $headers */
			$headers = $expected['headers'];
			foreach ($headers as $name => $value)
			{
				$response->assertHeader($name, $value);
			}
		}
	}
}
