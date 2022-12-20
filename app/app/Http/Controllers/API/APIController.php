<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

abstract class APIController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	protected function returnSuccessResponse(int $status, array $data = []): JsonResponse
	{
		return response()->json(['success' => true, 'data' => $data], $status);
	}

	/**
	 * @return JsonResponse
	 */
	protected function returnErrorResponse(int $status, array $errors = []): JsonResponse
	{
		return response()->json(['success' => false, 'errors' => $errors], $status);
	}
}
