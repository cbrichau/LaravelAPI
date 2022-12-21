<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\APIController;
use App\Models\Basket;
use Illuminate\Support\Facades\Hash;

class AuthController extends APIController
{
	/**
	 * @OA\Post(
	 *   operationId="signUp",
	 *   tags={"Authentication"},
	 *   path="/api/auth/sign-up",
	 *   summary="Create a new user.",
	 *   @OA\RequestBody(
	 *     required=true,
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="name", type="string", example="James Bond"),
	 *       @OA\Property(property="email", type="string", example="james@example.com"),
	 *       @OA\Property(property="password", type="string", example="007"),
	 *       @OA\Property(property="password_confirmation", type="string", example="007"),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=200,
	 *     description="New user created",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="true"),
	 *       @OA\Property(property="data", type="array",
	 *         @OA\Items(type="object", properties = {
	 *           @OA\Property(property="name", type="string", example="James Bond"),
	 *           @OA\Property(property="email", type="string", example="james@example.com"),
	 *           @OA\Property(property="basket", type="int", example="12"),
	 *           @OA\Property(property="token", type="string", example="1|XZmaKMKJJBt9IXJ6I5ob8Cf6yrfAJSPbqxyGvevM"),
	 *         }),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=400,
	 *     description="Bad request.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="false"),
	 *       @OA\Property(property="errors", type="object",
	 *         @OA\Property(property="messages", type="object",
	 *           @OA\Property(property="name", type="array", example={
	 *               "The name field is required.",
	 *             },
	 *             @OA\Items(type="string"),
	 *           ),
	 *           @OA\Property(property="email", type="array", example={
	 *               "The email field is required.",
	 *               "The email must be a valid email address.",
	 *               "The email has already been taken."
	 *             },
	 *             @OA\Items(type="string"),
	 *           ),
	 *           @OA\Property(property="password", type="array", example={
	 *               "The password field is required.",
	 *             },
	 *             @OA\Items(type="string"),
	 *           ),
	 *           @OA\Property(property="password_confirmation", type="array", example={
	 *               "The password confirmation field is required.",
	 *               "The password confirmation and password must match.",
	 *             },
	 *             @OA\Items(type="string"),
	 *           ),
	 *         ),
	 *       ),
	 *     ),
	 *   ),
	 * )
	 *
	 * Registers a new user.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function signUp(Request $request): JsonResponse
	{
		$payload = $request->all();

		$validator = Validator::make($payload, [
			'name' => 'required',
			'email' => 'required|email|unique:users,email',
			'password' => 'required',
			'password_confirmation' => 'required|same:password',
		]);

		if ($validator->fails())
		{
			return $this->returnErrorResponse(400, (array) $validator->errors());
		}

		$payload['password'] = Hash::make($payload['password']);
		$user = User::create($payload);
		$userBasket = Basket::create(['user_id' => $user->id]);

		$data = [
			'name' =>  $user->name,
			'email' =>  $user->email,
			'basket' => $userBasket->id,
			'token' =>  $user->createToken('userToken')->plainTextToken
		];

		return $this->returnSuccessResponse(201, $data);
	}

	/**
	 * @OA\Post(
	 *   operationId="signIn",
	 *   tags={"Authentication"},
	 *   path="/api/auth/sign-in",
	 *   summary="Log in an existing user.",
	 *   @OA\RequestBody(
	 *     required=true,
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="email", type="string", example="james.bond@example.com"),
	 *       @OA\Property(property="password", type="string", example="007"),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=200,
	 *     description="User logged in",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="true"),
	 *       @OA\Property(property="data", type="array",
	 *         @OA\Items(type="object", properties = {
	 *           @OA\Property(property="name", type="string", example="James Bond"),
	 *           @OA\Property(property="token", type="string", example="1|XZmaKMKJJBt9IXJ6I5ob8Cf6yrfAJSPbqxyGvevM"),
	 *         }),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=400,
	 *     description="Bad request.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="false"),
	 *       @OA\Property(property="errors", type="object",
	 *         @OA\Property(property="messages", type="object",
	 *           @OA\Property(property="email", type="array", example={
	 *               "The email field is required.",
	 *               "The email must be a valid email address.",
	 *             },
	 *             @OA\Items(type="string"),
	 *           ),
	 *           @OA\Property(property="password", type="array", example={
	 *               "The password field is required.",
	 *             },
	 *             @OA\Items(type="string"),
	 *           ),
	 *         ),
	 *       ),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=401,
	 *     description="Access denied.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="false"),
	 *       @OA\Property(property="errors", type="array",
	 *         @OA\Items(type="string", example="Wrong email and/or password"),
	 *       ),
	 *     ),
	 *   ),
	 * )
	 *
	 * Logs in a valid user.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function signIn(Request $request): JsonResponse
	{
		$payload = $request->all();

		$validator = Validator::make($payload, [
			'email' => 'required|email',
			'password' => 'required'
		]);

		if ($validator->fails())
		{
			return $this->returnErrorResponse(400, (array) $validator->errors());
		}

		$credentials = [
			'email' => $payload['email'],
			'password' => $payload['password']
		];

		if (
			Auth::attempt($credentials) === false ||
			($user = User::where('email', $request->email)->first()) === null
		) {
			return $this->returnErrorResponse(401, ['Wrong email and/or password']);
		}

		$data = [
			'name' => $user->name,
			'token' =>  $user->createToken('userToken')->plainTextToken
		];

		return $this->returnSuccessResponse(200, $data);
	}

	/**
	 * Returns the 401 standard reponse.
	 * Used in /app/app/Http/Middleware/Authenticate.php to enable /app/app/Http/Controllers/API/V1/ProductController.php
	 * to fail gracefully when requesting a CSV rather than the standard JSON response.
	 *
	 * @return JsonResponse
	 */
	public function unauthorized(): JsonResponse
	{
		return response()->json(['message' => 'Unauthenticated.'], 401);
	}
}
