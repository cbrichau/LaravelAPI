<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$assignmentInput = [
			['name' => 'Pioneer DJ Mixer', 'price' => 699],
			['name' => 'Roland Wave Sampler', 'price' => 485],
			['name' => 'Reloop Headphone', 'price' => 159],
			['name' => 'Rokit Monitor', 'price' => 189.9],
			['name' => 'Fisherprice Baby Mixer', 'price' => 120],
		];

		foreach ($assignmentInput as $product)
			Product::factory()->create($product);

		Product::factory(10)->create();
	}
}
