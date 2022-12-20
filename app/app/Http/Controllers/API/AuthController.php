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
		)
		{
			return $this->returnErrorResponse(401, ['Wrong email and/or password']);
		}

		$data = [
			'name' => $user->name,
			'token' =>  $user->createToken('userToken')->plainTextToken
		];

		return $this->returnSuccessResponse(200, $data);
	}
}
