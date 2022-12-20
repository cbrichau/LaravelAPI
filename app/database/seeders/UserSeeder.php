<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	/**
	 * @return void
	 */
	public function run()
	{
		User::factory()->create([
			'name' => 'James Bond',
			'email' => 'james.bond@example.com',
			'email_verified_at' => now(),
			'password' => Hash::make('007'),
			'remember_token' => Str::random(10),
		]);
	}
}
