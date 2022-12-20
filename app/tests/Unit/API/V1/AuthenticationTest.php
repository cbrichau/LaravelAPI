<?php

namespace Tests\Unit\API\V1;

use Tests\Unit\API\V1\AbstractEndpointUnitTest;

class AuthenticationTest extends AbstractEndpointUnitTest
{
	/**
	 * Checks that all endpoints fail without authentication.
	 * 
	 * @return void
	 */
	public function test_endpoints_without_authentication(): void
	{
		foreach ($this->endpoints as $endpoint => $methods)
		{
			foreach ($methods as $method)
			{
				$headers['accept'] = 'application/json';
				if ($method === 'post')
				{
					$headers['content-type'] = 'application/json';
				}

				$response = $this->withHeaders($headers)->{$method}($endpoint);
				$content = json_decode($response->getContent());

				$response->assertStatus(401);
				$this->assertSame('Unauthenticated.', $content->message);
			}
		}
	}
}
