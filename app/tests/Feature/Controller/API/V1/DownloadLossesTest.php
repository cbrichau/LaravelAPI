<?php

declare(strict_types=1);

namespace Tests\Feature\Controller\API\V1;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\Feature\Controller\API\V1\AbstractEndpointFeatureTest;

class DownloadLossesTest extends AbstractEndpointFeatureTest
{
	private function callEndpoint(string $queryParameters = ''): TestResponse
	{
		/** @var User $authenticatedUser */
		$authenticatedUser = User::find($_ENV['INTERNAL_USER_ID']);

		return $this->actingAs($authenticatedUser)
			->withHeaders([['accept' => 'text/csv']])
			->get('/api/v1/products/download-losses' . $queryParameters);
	}

	private function assertSuccessfulDownload(TestResponse $response): void
	{
		ob_start();
		$response->sendContent(); //This enables deleteFileAfterSend(), c.f. \app\app\Http\Controllers\API\V1\ProductController.php
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
		Happy flow
	\* ******************************************** */

	public function test_download_losses(): void
	{
		$response = $this->callEndpoint();

		$this->assertSuccessfulDownload($response);
	}

	/* ******************************************** *\
		Extended happy flow
	\* ******************************************** */

	public function test_download_losses_with_valid_filters(): void
	{
		$allValidFilters = [
			'basket_id[is]=1',

			'product_id[is]=1',

			'add_to_basket_date[greaterThan]=1990-01-01',
			'add_to_basket_date[lowerThan]=2090-01-01',

			'removed_from_basket_date[greaterThan]=1990-01-01',
			'removed_from_basket_date[lowerThan]=2090-01-01',

			'product_price[greaterThan]=0',
			'product_price[lowerThan]=1000000',
		];

		$response = $this->callEndpoint('?' . implode('&', $allValidFilters));

		$this->assertSuccessfulDownload($response);
	}

	/* ******************************************** *\
		Unhappy flow
	\* ******************************************** */

	public function test_download_losses_with_external_user(): void
	{
		/** @var User $authenticatedUser */
		$authenticatedUser = User::find($_ENV['EXTERNAL_USER_ID']);

		$response = $this->actingAs($authenticatedUser)->getJson('/api/v1/products/download-losses');

		$response->assertStatus(403);
	}

	public function test_download_losses_with_invalid_filters(): void
	{
		$invalidQueryFilters = '?imNotAFilter=badValue&meToo=0';

		$response = $this->callEndpoint($invalidQueryFilters);

		// Invalid filters are simply ignored, they do not trigger an error, so the file should be downloaded anyway.
		$this->assertSuccessfulDownload($response);
	}
}
