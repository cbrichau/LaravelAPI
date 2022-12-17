<?php

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
			BasketSeeder::class,
			ProductSeeder::class
		]);

		$baskets = Basket::all();
		$products = Product::all();

		$baskets->each(function ($basket) use ($products)
		{
			$randomProductSelection = $products->random(rand(0, 3))->pluck('id')->toArray();
			$basket->products()->attach($randomProductSelection);
		});
	}
}
