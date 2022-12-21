<?php

declare(strict_types=1);

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
		$this->createAssignmentInput();
		$this->createSampleProducts();
	}

	/**
	 * Creates the products prodvided in the assignment (c.f. /products.json).
	 *
	 * @return void
	 */
	private function createAssignmentInput(): void
	{
		$assignmentInput = [
			// name, price
			['Pioneer DJ Mixer', 699],
			['Roland Wave Sampler', 485],
			['Reloop Headphone', 159],
			['Rokit Monitor', 189.9],
			['Fisherprice Baby Mixer', 120],
		];

		foreach ($assignmentInput as $product)
		{
			Product::factory()->create([
				'name' => $product[0],
				'price' => $product[1]
			]);
		}
	}

	/**
	 * Creates extra sample products.
	 *
	 * @return void
	 */
	private function createSampleProducts(): void
	{
		Product::factory(20)->create();
	}
}
