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
		Schema::table('baskets', function (Blueprint $table)
		{
			$table->foreignId('user_id')->constrained();
			$table->dateTime('checkout_date')->nullable();
		});

		Schema::table('basket_product', function (Blueprint $table)
		{
			$table->renameColumn('date_removed', 'removal_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('baskets', 'user_id'))
		{
			Schema::table('baskets', function (Blueprint $table)
			{
				$table->dropForeign(['user_id']);
				$table->dropColumn('user_id');
			});
		}

		if (Schema::hasColumn('baskets', 'checkout_date'))
		{
			Schema::table('baskets', function (Blueprint $table)
			{
				$table->dropColumn('checkout_date');
			});
		}

		if (Schema::hasColumn('basket_product', 'removal_date'))
		{
			Schema::table('basket_product', function (Blueprint $table)
			{
				$table->renameColumn('removal_date', 'date_removed');
			});
		}
	}
};
