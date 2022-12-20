<?php

declare(strict_types=1);

namespace Tests;

use stdClass;
use Exception;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	use CreatesApplication;

	public function setUp(): void
	{
		parent::setUp();
		Artisan::call('migrate:refresh', ['--seed' => true]);
	}

	/**
	 * Helper method to extract the test's response's content.
	 * 
	 * @param TestResponse $response
	 * @return stdClass
	 */
	protected function extractResponseContent(TestResponse $response): stdClass
	{
		if ($response->getContent() !== false)
		{
			$cleanResponse = str_replace('\u0000*\u0000', '', $response->getContent());
			if (($content = json_decode($cleanResponse)) !== false)
			{
				/** @var stdClass $content */
				return $content;
			}
		}

		throw new Exception('json_decode() failed');
	}
}
