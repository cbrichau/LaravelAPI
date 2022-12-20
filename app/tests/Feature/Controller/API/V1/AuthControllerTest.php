<?php

declare(strict_types=1);

namespace Tests\Feature\Controller\API\V1;

use Tests\Feature\Controller\API\V1\AbstractEndpointFeatureTest;

class AuthControllerTest extends AbstractEndpointFeatureTest
{
	const VALID_EMAIL = 'james.bond@example.com';
	const VALID_PASSWORD = '007';

	/**
	 * @var array<string, array<string, string>> $payload
	 */
	private array $payload = [];

	public function setUp(): void
	{
		parent::setUp();

		$this->payload['sign-up'] = [
			'name' => fake()->name(),
			'email' => fake()->unique()->safeEmail(),
			'password' => '123456',
			'password_confirmation' => '123456'
		];

		$this->payload['sign-in'] = [
			'email' => self::VALID_EMAIL,
			'password' => self::VALID_PASSWORD
		];
	}

	private function assertValidAuth(string $endpoint, int $expectedStatus): void
	{
		$response = $this->postJson('/api/auth/' . $endpoint, $this->payload[$endpoint]);

		$response->assertStatus($expectedStatus);

		$content = $this->extractResponseContent($response);

		$this->assertTrue($content->success);
		$this->assertTrue(isset($content->data->token));

		$response->assertHeader('content-type', 'application/json');
	}

	/* ******************************************** *\
		Happy flow
	\* ******************************************** */

	public function test_sign_up_with_valid_input(): void
	{
		$this->assertValidAuth('sign-up', 201);
	}

	public function test_sign_in_with_valid_input(): void
	{
		$this->assertValidAuth('sign-in', 200);
	}

	/* ******************************************** *\
		Unhappy flow
	\* ******************************************** */

	public function test_sign_up_with_invalid_input(): void
	{
		$this->payload['sign-up']['email'] = '';

		$response = $this->postJson('/api/auth/sign-up', $this->payload['sign-up']);

		$response->assertStatus(400);

		$content = $this->extractResponseContent($response);

		$this->assertFalse($content->success);
		$this->assertSame(["The email field is required."], $content->errors->messages->email);

		$response->assertHeader('content-type', 'application/json');
	}

	public function test_sign_in_with_invalid_input(): void
	{
		$this->payload['sign-in']['password'] = 'wrong';

		$response = $this->postJson('/api/auth/sign-in', $this->payload['sign-in']);

		$response->assertStatus(401);

		$content = $this->extractResponseContent($response);

		$this->assertFalse($content->success);
		$this->assertSame(["Wrong email and/or password"], $content->errors);

		$response->assertHeader('content-type', 'application/json');
	}
}
