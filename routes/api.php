<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\FieldsInvestorsController;
use App\Http\Controllers\InvestorsController;
use App\Http\Controllers\PitchController;
use App\Http\Controllers\PitchTextController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\StagesInvestorsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');
});

// استبدلي مسار التحقق الحالي بهذا
Route::post('/verify-code', function (Request $request) {
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'code' => 'required|string|min:4|max:4'
    ]);

    $user = User::find($request->user_id);

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'email is already veerified'], 400);
    }

    // التحقق من صحة الكود
    if ($user->verification_code !== $request->code) {
        return response()->json(['message' => 'varification code is incorrect'], 400);
    }

    // التحقق من انتهاء صلاحية الكود (60 دقيقة)
    if ($user->verification_code_sent_at->diffInMinutes(now()) > 60) {
        return response()->json(['message' => 'varification code has expired'], 400);
    }

    // تفعيل الحساب
    $user->email_verified_at = now();
    $user->verification_code = null;
    $user->save();

    return response()->json(['message' => 'account is already active']);
});

Route::post('/resend-verification', function (Request $request) {
    $request->validate(['user_id' => 'required|exists:users,id']);

    $user = User::find($request->user_id);

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'email is already veerified'], 400);
    }

    // إرسال كود جديد
    $user->sendVerificationCode();

    return response()->json(['message' => 'code has been send']);
});


Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

Route::post('/resend-password-reset-code', function (Request $request) {
    $request->validate(['email' => 'required|email|exists:users,email']);

    $user = User::where('email', $request->email)->first();

    // إرسال كود جديد
    $user->sendPasswordResetCode();

    return response()->json(['message' => 'Password reset code has been sent to your email.']);
});

// Public routes (لا تحتاج مصادقة)
Route::get('/public/fields', [FieldController::class, 'index']);
Route::get('/public/stages', [StageController::class, 'index']);

// Google authentication routes
Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('/callback', [GoogleAuthController::class, 'callback']);
});

// Route for handling app redirects
Route::get('/auth/app-redirect', function (Request $request) {
    $token = $request->input('token');
    $userId = $request->input('user_id');
    $redirectUri = $request->input('redirect_uri', 'com.example.app://auth-callback');

    // Validate inputs
    if (!$userId) {
        return response()->json(['error' => 'User ID is required'], 400);
    }

    $deepLink = $redirectUri . '?token=' . ($token ?? 'null') . '&user_id=' . $userId;
    return redirect()->away($deepLink);
})->name('auth.app-redirect');

