<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->validate([
                                              'email' => 'required|email',
                                              'password' => 'required'
                                          ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Generate a unique token for the user
            $token = md5(uniqid(rand(), true));

            // Get the authenticated user
            $user = Auth::user();

            // Update the api_token field of the user
            $user->api_token = $token;
            $user->save();

            // Return the token in the response
            return response()->json(['token' => $token], 200);
        }

        // Return unauthorized error if authentication fails
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout()
    {
        Auth::user()->api_token = null;
        Auth::user()->save();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

}
