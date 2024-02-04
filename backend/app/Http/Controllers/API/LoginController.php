<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|min:10',
        ]);

        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json(['message' => 'Could not process a user with that phone number.'], 401);
        }

        // reference ->  https://laravel-notification-channels.com/twilio/#contents
    }
}
