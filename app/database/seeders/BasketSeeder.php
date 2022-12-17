<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Basket;

class BasketSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Basket::factory(10)->create();
	}
}
