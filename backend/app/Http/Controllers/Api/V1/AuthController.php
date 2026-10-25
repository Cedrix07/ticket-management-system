<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => true]))
        {
            return response()
                ->json([
                    'message' => 'Invalid credentials or account is deactivated',
                ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()
            ->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()
            ->json([
                'message' => 'Logged out successfully',
            ], 200);
    }

    // Returns current authenticated user profile
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }


}
