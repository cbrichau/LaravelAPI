<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		$fakeCreationDate = $this->faker->dateTimeBetween('-5 years', '-1 week');

		return [
			'name' => fake()->name(),
			'email' => fake()->unique()->safeEmail(),
			'created_at' => $fakeCreationDate,
			'updated_at' => $fakeCreationDate,
			'email_verified_at' => $fakeCreationDate,
			'password' => Hash::make(fake()->password()),
			'remember_token' => Str::random(10),
		];
	}

	/**
	 * Indicate that the model's email address should be unverified.
	 *
	 * @return static
	 */
	public function unverified()
	{
		return $this->state(fn (array $attributes) => [
			'email_verified_at' => null,
		]);
	}
}
