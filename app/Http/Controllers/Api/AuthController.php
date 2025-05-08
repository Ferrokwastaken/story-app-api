<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The AuthController
 * 
 * This class handles both the logging in and out of moderators for the platform.
 */
class AuthController extends Controller
{
    /**
     * Allows moderators to login
     * 
     * This method validates the input request with the database, and then
     * creates a token that will be saved in local storage on the frontend until
     * the user logs out.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP instance request to login.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON, indicating either a successful or erroneous login attempt.
     */
    public function moderatorLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->hasRole('moderator') || $user->hasRole('admin')) {
                $token = $user->createToken('moderator-token')->plainTextToken;
                return response()->json(['token' => $token, 'name' => $user->name, 'message', 'Login successful!']);
            } else {
                return response()->json(['message' => 'Unauthorized: User is not a moderator'], 401);
            }
        } else {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }
    }

    /**
     * Takes care of the logout.
     * 
     * This method makes the moderator logout, making sure that
     * their token is removed in the proccess.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request instance to logout.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON indicating a successful logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
