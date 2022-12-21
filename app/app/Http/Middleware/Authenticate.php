<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string|null
	 */
	protected function redirectTo($request)
	{
		// c.f. /app/app/Http/Controllers/API/AuthController.php @unauthorized
		if (!$request->expectsJson())
		{
			return 'api/401';
		}
	}
}
