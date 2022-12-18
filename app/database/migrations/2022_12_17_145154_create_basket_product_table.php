<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('basket_product', function (Blueprint $table)
		{
			$table->foreignId('basket_id')->constrained();
			$table->foreignId('product_id')->constrained();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('basket_product', function (Blueprint $table)
		{
			$table->dropForeign('basket_product_basket_id_foreign');
			$table->dropForeign('basket_product_product_id_foreign');
		});

		Schema::dropIfExists('basket_product');
	}
};
