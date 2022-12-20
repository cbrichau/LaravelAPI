<?php

declare(strict_types=1);

namespace Tests\Feature\Controller\API\V1;

use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\Feature\Controller\API\V1\AbstractEndpointFeatureTest;

class DownloadLossesTest extends AbstractEndpointFeatureTest
{
	private function callEndpoint(): TestResponse
	{
		return $this->request('get', '/api/v1/products/download-losses');
	}

	/* ******************************************** *\
		Happy flow
	\* ******************************************** */

	public function test_download_losses(): void
	{
		$response = $this->callEndpoint();

		// Enables deleteFileAfterSend().
		ob_start();
		$response->sendContent();
		ob_end_clean();

		$response->assertStatus(200);
		$response->assertDownload();
		$response->assertHeader('content-type', 'text/csv; charset=UTF-8');

		/** @var BinaryFileResponse $baseResponse */
		$baseResponse = $response->baseResponse;

		$filenameTemplate = '#^losses\-\d{4}\-\d{2}\-\d{2}UTC\d{2}:\d{2}:\d{2}\.csv$#';
		$this->assertMatchesRegularExpression($filenameTemplate, $baseResponse->getFile()->getFilename());
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
