<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckAPIHeaders
{
	/**
	 * Checks the request contains JSON headers.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return Closure|JsonResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		if (!$request->hasHeader('accept') || $request->header('accept') !== 'application/json')
		{
			$errors['MISSING_HEADER_ACCEPT'] = 'Requests to the API must include the "accept = application/json" header.';
		}

		if (
			in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) &&
			(!$request->hasHeader('content-type') || $request->header('content-type') !== 'application/json')
		) {
			$errors['MISSING_HEADER_CONTENT_TYPE'] = 'POST, PUT and PATCH requests to the API  must include the "content-type = application/json" header.';
		}

		if (isset($errors))
		{
			return response()->json(['success' => false, 'errors' => $errors], 400);
		}

		return $next($request);
	}
}
