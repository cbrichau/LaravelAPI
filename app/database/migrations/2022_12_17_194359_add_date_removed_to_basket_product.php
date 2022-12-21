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
		Schema::table('basket_product', function (Blueprint $table)
		{
			$table->dateTime('date_removed')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('basket_product', 'date_removed'))
		{
			Schema::table('basket_product', function (Blueprint $table)
			{
				$table->dropColumn('date_removed');
			});
		}
	}
};
