<?php

declare(strict_types=1);

namespace App\QueryFilters\API\V1;

use App\QueryFilters\AbstractQueryFilter;

class ProductQueryFilter extends AbstractQueryFilter
{
	/**
	 * @var array<string, array<int, string>> $allowedFilters
	 */
	private array $allowedFilters = [
		'basket_id' => ['is'],
		'product_id' => ['is'],
		'add_to_basket_date' => ['greaterThan', 'lowerThan'],
		'removed_from_basket_date' => ['greaterThan', 'lowerThan'],
		'product_price' => ['greaterThan', 'lowerThan']
	];

	/**
	 * @var array<string, string> $dbColumns
	 */
	private array $dbColumns = [
		'basket_id' => 'basket_id',
		'product_id' => 'product_id',
		'add_to_basket_date' => 'basket_product.created_at',
		'removed_from_basket_date' => 'removal_date',
		'product_price' => 'price',
		// 'basket_checkout_date', 'product_name',
	];

	/**
	 * Converts an array of URL parameters into a query usable with DB::table()->where($query).
	 *
	 * @param array<string, array<string, int|string>> $queryFilters
	 * @return array<int, array<int, int|string>>
	 */
	public function convertToQuery(array $queryFilters): array
	{
		$query = [];

		foreach ($queryFilters as $property => $operations)
		{
			if (isset($this->allowedFilters[$property]) && is_array($operations))
			{
				foreach ($operations as $operator => $operand)
				{
					if (isset($this->operationsMap[$operator]))
					{
						$query[] = [
							$this->dbColumns[$property],
							$this->operationsMap[$operator],
							$operand
						];
					}
				}
			}
		}

		return $query;
	}
}
