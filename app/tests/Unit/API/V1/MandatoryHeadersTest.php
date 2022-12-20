<?php

declare(strict_types=1);

namespace Tests\Unit\API\V1;

use stdClass;
use Exception;
use Tests\Unit\API\V1\AbstractEndpointUnitTest;

class MandatoryHeadersTest extends AbstractEndpointUnitTest
{
	/**
	 * Checks that all endpoints fail if not provided their mandatory headers.
	 * 
	 * @return void
	 */
	public function test_endpoints_without_headers(): void
	{
		$this->endpoints['/api/auth/sign-up'] = ['post'];
		$this->endpoints['/api/auth/sign-in'] = ['post'];

		foreach ($this->endpoints as $endpoint => $methods)
		{
			foreach ($methods as $method)
			{
				$response = $this->{$method}($endpoint);

				$response->assertStatus(400);

				$content = $this->extractResponseContent($response);

				$expectedErrors = ['MISSING_HEADER_ACCEPT'];
				if (in_array($method, ['post', 'put', 'patch']))
				{
					$expectedErrors[] = 'MISSING_HEADER_CONTENT_TYPE';
				}

				$this->assertSame($expectedErrors, array_keys(get_object_vars($content->errors)));
			}
		}
	}
}
