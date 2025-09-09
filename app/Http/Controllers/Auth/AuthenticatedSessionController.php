<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = $request->user();
        if (!$user->hasVerifiedEmail()) {
            if ($request->has('resend_verification')) {
                $user->sendEmailVerificationNotification();
                return response()->json([
                    'message' => 'Verification link sent again. Please check your email.',
                    'verified' => false
                ], 403);
            }
            return response()->json([
                'message' => 'Please confirm your email before logging in.',
                'verified' => false,
                'user_id' => $user->id
            ], 403);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
            'verified' => true,
            'message' => 'Logged in successfully'
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user'
                ], 401);
            }
            $request->user()->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.'. $e->getMessage()
            ], 500);
        }
    }
}
