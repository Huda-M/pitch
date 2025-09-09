<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        try {
            $url = Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl();
            return response()->json([
                'success' => true,
                'data' => ['url' => $url],
                'message' => 'Google authentication URL generated'
            ]);
        } catch (\Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Google authentication URL'
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            if (!$request->has('code')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing authorization code from Google'
                ], 400);
            }
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
            $user = User::firstOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'founder',
                    'email_verified_at' => now(),
                ]
            );
            $redirectUri = 'com.example.app://auth-callback';
            $deepLink = $redirectUri . '?token=$token&user_id=' . $user->id;
            return redirect()->away($deepLink);
        } catch (\Exception $e) {
            Log::error('Google auth error: ' . $e->getMessage());
            return response()->json([
            'success' => false,
            'message' => 'Failed to authenticate with Google'
            ], 500);
        }
    }
}
