<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function moderatorLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->hasRole('moderator') || $user->hasRole('admin')) {
                $token = $user->createToken('moderator-token')->plainTextToken;
                return response()->json(['token' => $token, 'message', 'Login successful!']);
            } else {
                return response()->json(['message' => 'Unauthorized: User is not a moderator'], 401);
            }
        } else {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
