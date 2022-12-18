<?php

namespace Tests\Feature\Controller\API\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DownloadLossesTest extends TestCase
{
	/* ******************************************** *\
		Happy flow
	\* ******************************************** */

	public function test_download_losses(): void
	{
		$response = $this->get('/api/v1/products/download-losses');

		// Enables deleteFileAfterSend()
		ob_start();
		$response->sendContent();
		ob_end_clean();

		$this->assertMatch($response, [
			'status' => 200,
			'headers' => [
				'content-type' => 'text/csv; charset=UTF-8'
			]
		]);

		$response->assertDownload();

		$filenameTemplate = '#^losses\-\d{4}\-\d{2}\-\d{2}UTC\d{2}:\d{2}:\d{2}\.csv$#';
		$this->assertMatchesRegularExpression($filenameTemplate, $response->getFile()->getFilename());
	}

	/* ******************************************** *\
		Extended happy flow
	\* ******************************************** */

	// Add tests with filters

	/* ******************************************** *\
		Unhappy flow
	\* ******************************************** */

	// Add tests with invalid filters
}
