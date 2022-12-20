<?php

namespace App\Models;

use App\Models\Basket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
	use HasFactory;

	/**
	 * @return BelongsToMany<Basket>
	 */
	public function baskets(): BelongsToMany
	{
		return $this->belongsToMany(Basket::class)->withTimestamps()->withPivot('removal_date');
	}
}
