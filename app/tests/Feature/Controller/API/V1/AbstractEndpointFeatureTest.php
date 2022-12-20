<?php

namespace Tests\Feature\Controller\API\V1;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\TestResponse;

abstract class AbstractEndpointFeatureTest extends TestCase
{
	/**
	 * Helper method to make a request with all the required headers and authentication.
	 * 
	 * @param string $method
	 * @param string $url
	 * @return TestResponse
	 */
	protected function request(string $method, string $url): TestResponse
	{
		$headers['accept'] = 'application/json';
		if ($method === 'post')
		{
			$headers['content-type'] = 'application/json';
		}

		$this->actingAs(User::find(1));

		return $this->withHeaders($headers)->{$method}($url);
	}

	/**
	 * Helper method to check the test's response broadly matches the expected response
	 * (= same status, content, and headers).
	 * 
	 * @param TestReponse $response
	 * @param array $expected
	 * @return void
	 */
	protected function assertValidJSONResponse(TestResponse $response, array $expected): void
	{
		// Status
		$response->assertStatus($expected['status']);

		// Content
		$content = json_decode($response->getContent());

		if (in_array($expected['status'], [200, 201]))
		{
			$this->assertTrue($content->success);
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

		foreach (($expected['headers'] ?? []) as $name => $value)
		{
			$response->assertHeader($name, $value);
		}
	}
}
