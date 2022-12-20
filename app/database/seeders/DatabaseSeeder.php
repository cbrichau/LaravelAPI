<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Basket;
use App\Models\Product;
use DateTime;
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
			BasketSeeder::class,
			ProductSeeder::class
		]);

		$baskets = Basket::all();
		$products = Product::all();

		// Assigns 0-3 rancom products to every basket
		$baskets->each(function ($basket) use ($products)
		{
			$randomProductIds = $products->random(rand(0, 3))->pluck('id')->toArray();
			$basket->products()->attach($randomProductIds);
		});

		// Makes non-random assignments so we can always rely on them when testing
		$testUser = User::find(1);

		/** @var Basket $basket1 */
		$basket1 = Basket::find(1);
		$basket1->user()->associate($testUser);
		$basket1->products()->attach(1);
		$basket1->checkout_date = (new DateTime())->format('Y-m-d H:i:s');
		$basket1->save();

		/** @var Basket $basket2 */
		$basket2 = Basket::find(2);
		$basket2->user()->associate($testUser);
		$basket2->products()->attach(1);
		if ($basket2->products->find(2))
		{
			$basket2->products()->detach(2);
		}
		$basket2->save();
	}
}
