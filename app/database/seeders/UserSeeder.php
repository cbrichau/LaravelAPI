<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Basket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	/**
	 * @return void
	 */
	public function run()
	{
		$this->createTestUsers();
		$this->createSampleUsers();
		$this->assignBasketsToAllUsers();
	}

	/**
	 * Creates the principal test users James Bond (employee) and Dracula (customer).
	 *
	 * @return void
	 */
	private function createTestUsers(): void
	{
		$testUsers = [
			// name, email, password, is_internal
			['James Bond', 'james.bond@example.com', '007', true],
			['Dracula', 'dracula@example.com', '666', false]
		];

		foreach ($testUsers as $user)
		{
			User::factory()->create([
				'name' => $user[0],
				'email' => $user[1],
				'password' => Hash::make($user[2]),
				'is_internal' => $user[3],
			]);
		}
	}

	/**
	 * Creates extra sample users.
	 *
	 * @return void
	 */
	private function createSampleUsers(): void
	{
		User::factory(2)->create();
	}

	/**
	 * Assigns one or more baskets to each user, keeping the latest one open while checking out all the previous ones.
	 * It also ensure the creation/checkout dates make sense.
	 *
	 * @return void
	 */
	private function assignBasketsToAllUsers(): void
	{
		User::all()->each(function ($user)
		{
			/*
			Always assigns exactly 2 baskets (one checked out, one open) to the test users
			so we can rely on it for testing.
			*/
			$numberOfBasketsForThisUser = (in_array($user->id, [1, 2]) ? 2 : rand(1, 3));

			for ($i = 0; $i < $numberOfBasketsForThisUser; $i++)
			{
				$previousBasket = Basket::orderBy('id', 'desc')->first();

				if ($i === 0)
				{
					$basket = Basket::factory()->create([
						'user_id' => $user->id,
						'created_at' => $user->created_at,
						'updated_at' => $user->created_at,
					]);
				}
				else
				{
					$startDate = ($previousBasket !== null ? $previousBasket->created_at : 'now');

					$randomCreationDate = fake()->dateTimeBetween($startDate, '-' . ($numberOfBasketsForThisUser - $i) . ' day');
					$basket = Basket::factory()->create([
						'user_id' => $user->id,
						'created_at' => $randomCreationDate,
						'updated_at' => $randomCreationDate,
					]);
				}

				if ($previousBasket !== null && $previousBasket->user_id === $basket->user_id)
				{
					$previousBasket->checkout_date = ($basket->created_at !== null ? $basket->created_at : now())->format('Y-m-d');
					$previousBasket->save();
				}

				$basket->save();
			}
		});
	}
}
