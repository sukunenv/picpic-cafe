<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'member',
        ]);

        $months = \App\Models\SystemSetting::where('key', 'point_expiry_months')
                    ->value('value') ?? 3;

        $user->update([
            'points' => 50,
            'point_expires_at' => now()->addMonths((int)$months)
        ]);

        $tokenResult = $user->createToken('auth_token', ['*'], now()->addDays(30));
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'expires_at' => $tokenResult->accessToken->expires_at
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        if ($request->header('X-App-Type') === 'admin') {
            if ($user->role === 'member') {
                return response()->json([
                    'message' => 'Akses ditolak. Akun ini bukan akun staff.'
                ], 403);
            }
        }

        if ($user->role === 'member') {
            // Member: 30 hari
            $tokenResult = $user->createToken('auth_token', ['*'], now()->addDays(30));
        } else {
            // Admin, Owner, Kasir: 8 jam
            $tokenResult = $user->createToken('auth_token', ['*'], now()->addHours(8));
        }

        return response()->json([
            'user' => $user,
            'token' => $tokenResult->plainTextToken,
            'expires_at' => $tokenResult->accessToken->expires_at
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|nullable|max:20',
            'avatar' => 'sometimes|string|nullable|max:2048',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|different:old_password',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'new_password.different' => 'Password baru harus berbeda dengan password lama.',
            'new_password_confirmation.same' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        
        // Delete tokens first
        $user->tokens()->delete();
        
        // Soft delete the user
        $user->delete();

        return response()->json([
            'message' => 'Akun berhasil dihapus. Sampai jumpa lagi!'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak terdaftar.'], 404);
        }

        // Generate token
        $token = Str::random(64);

        // Store in password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send Email
        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email, $user->name));

        return response()->json(['message' => 'Link reset password telah dikirim ke email kamu.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json(['message' => 'Token reset tidak valid atau sudah kedaluwarsa.'], 422);
        }

        // Check expiry (say 60 minutes)
        if (now()->parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Token sudah kedaluwarsa. Silakan minta link baru.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        // Update Password
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil diubah. Silakan login kembali.']);
    }
}
