<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and assign a role.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => $request->password, // auto-hashed by the model cast
        ]);

        // assign the Spatie role (passenger or driver)
        $user->assignRole($request->role);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'user'    => $user,
            'role'    => $request->role,
            'token'   => $token,
        ], 201);
    }

    /**
     * Log a user in and issue a token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user'    => $user,
            'roles'   => $user->getRoleNames(), // shows assigned roles
            'token'   => $token,
        ]);
    }

    /**
     * Log out (revoke the current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user'  => $request->user(),
            'roles' => $request->user()->getRoleNames(),
        ]);
    }
}
