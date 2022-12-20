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
				$response = $this->{$method . 'Json'}($endpoint);
				$content = json_decode($response->getContent());

				$response->assertStatus(401);
				$this->assertSame('Unauthenticated.', $content->message);
			}
		}
	}
}
