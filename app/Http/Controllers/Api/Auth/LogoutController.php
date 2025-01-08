<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    //
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Revoke the user's access token
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
            $responseData = [
                'status'    => true,
                'message'   => 'Logged out successfully!',
            ];
            return response()->json($responseData, 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}
