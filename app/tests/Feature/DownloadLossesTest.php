<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DownloadLossesTest extends TestCase
{
	/**
	 * @return void
	 */
	public function test_download_losses()
	{
		$response = $this->get('/api/v1/baskets/download-losses');

		$response->assertStatus(200);
	}
}
