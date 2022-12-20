<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\APIController;
use Illuminate\Support\Facades\Hash;

class AuthController extends APIController
{
	/**
	 * Registers a new user.
	 *
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

		$data = [
			'name' =>  $user->name,
			'email' =>  $user->email,
			'token' =>  $user->createToken('userToken')->plainTextToken
		];

		return $this->returnSuccessResponse(201, $data);
	}

	/**
	 * Logs in a valid user.
	 *
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

		if (Auth::attempt($credentials) === false)
		{
			return $this->returnErrorResponse(401, ['Wrong email and/or password']);
		}

		$user = User::where('email', $request->email)->first();
		$data = [
			'name' =>  $user->name,
			'token' =>  $user->createToken('userToken')->plainTextToken
		];

		return $this->returnSuccessResponse(200, $data);
	}
}
