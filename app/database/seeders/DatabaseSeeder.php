<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Basket;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call([
			UserSeeder::class,
			ProductSeeder::class
		]);

		$this->seedBasketAdditionsAndRemovals();
		$this->addNonRandomSampleForTests();
	}

	/**
	 * Creates item additions to / removals from baskets.
	 *
	 * @return void
	 */
	private function seedBasketAdditionsAndRemovals(): void
	{
		$baskets = Basket::all();
		$products = Product::all();

		$baskets->each(function ($basket) use ($products)
		{
			$randomProductIds = $products->random(rand(0, 5))->pluck('id')->toArray();

			foreach ($randomProductIds as $productId)
			{
				$randomChangeDate = fake()->dateTimeBetween($basket->created_at, 'now');

				$basket->products()->sync([
					$productId => [
						'created_at' => $randomChangeDate,
						'updated_at' => $randomChangeDate,
						'removal_date' => (rand(0, 2) === 1 ? null : $randomChangeDate),
					]
				], false);
			}

			$basket->save();
		});
	}

	/**
	 * Makes non-random assignments so we can always rely on them when testing.
	 *
	 * @return void
	 */
	private function addNonRandomSampleForTests(): void
	{
		/*
		Users 1 and 2 always have 2 baskets each (1-2 and 3-4, c.f. /app/database/seeders/UserSeeder.php),
		so we just need to make sure their open baskets (2 and 4):
		- always contain product #1 with removal_date=null
		- always contain product #2 with removal_date=not null
		- never contains product #3.
		*/
		foreach ([2, 4] as $basketId)
		{
			/** @var Basket $basket */
			$basket = Basket::find($basketId);

			$randomDate = fake()->dateTimeBetween($basket->created_at, $basket->checkout_date);

			if (!$basket->products->find(1))
			{
				$basket->products()->sync([
					1 => [
						'created_at' => $randomDate,
						'updated_at' => $randomDate,
						'removal_date' => null,
					]
				], false);
			}

			$basket->products()->sync([
				2 => [
					'created_at' => $randomDate,
					'updated_at' => $randomDate,
					'removal_date' => $randomDate,
				]
			], false);

			if ($basket->products->find(3))
			{
				$basket->products()->detach(3);
			}

			$basket->save();
		}
	}
}
