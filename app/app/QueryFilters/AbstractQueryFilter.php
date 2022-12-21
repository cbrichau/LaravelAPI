<?php

declare(strict_types=1);

namespace App\QueryFilters;

abstract class AbstractQueryFilter
{
	/**
	 * @var array<string, string> $operationsMap
	 */
	protected array $operationsMap = [
		'is' => '=',
		'greaterThan' => '>',
		'lowerThan' => '<',
	];
}
