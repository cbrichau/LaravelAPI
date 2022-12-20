<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

abstract class APIController extends Controller
{
	/**
	 * @param int $status
	 * @param array<string, string> $data
	 * @return JsonResponse
	 */
	protected function returnSuccessResponse(int $status, array $data = []): JsonResponse
	{
		return response()->json(['success' => true, 'data' => $data], $status);
	}

	/**
	 * @param int $status
	 * @param array<string|int, array<string, string>|string> $errors
	 * @return JsonResponse
	 */
	protected function returnErrorResponse(int $status, array $errors = []): JsonResponse
	{
		return response()->json(['success' => false, 'errors' => $errors], $status);
	}
}
