<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Basket extends Model
{
	use HasFactory;

	/**
	 * @return BelongsTo<User>
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return BelongsToMany<Product>
	 */
	public function products(): BelongsToMany
	{
		return $this->belongsToMany(Product::class)->withTimestamps()->withPivot('removal_date');
	}
}
