<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'role' => ['required', 'in:admin,founder,investor']
        ]);
        $verificationCode = rand(1000, 9999);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => null,
            'verification_code' => $verificationCode,
            'verification_code_sent_at' => now(),
        ]);
        event(new Registered($user));
        $this->sendVerificationCode($user, $verificationCode);
        return response()->json([
            'message' => 'Registration successful. Verification code sent to your email.',
            'user_id' => $user->id
        ], 201);
    }

    private function sendVerificationCode($user, $code)
    {
        $user->notify(new \App\Notifications\VerificationCodeNotification($code));
    }
}
