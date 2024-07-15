<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Notifications\WelcomeEmail;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(private VoucherService $voucherService)
    {
    }

    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create($registerRequest->validated());

            $this->voucherService->createVoucherForUser($user);

            DB::commit();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'User created successfully',
            'token' => $user->createToken('Auth Token')->plainTextToken
        ], 200);
    }

    public function login(LoginRequest $loginRequest)
    {
        if (!Auth::attempt($loginRequest->only(['username', 'password']))) {
            return response()->json([
                'message' => 'The username or password you entered isn\'t connected to an account..'
            ], 401);
        }

        $user = User::whereUsername($loginRequest->username)->first();

        $code =

            $user->notify(new WelcomeEmail($user));

        return response()->json([
            'message' => 'User created successfully',
            'token' => $user->createToken('Auth Token')->plainTextToken
        ], 200);
    }
}
