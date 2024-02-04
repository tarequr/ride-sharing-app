<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\LoginNeedsVerification;
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

        $user->notify(new LoginNeedsVerification());
        // reference ->  https://laravel-notification-channels.com/twilio/#contents

        return response()->json(['message' => 'Text message notification sent successfully.']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        $user = User::where('phone', $request->phone)
                ->where('login_code', $request->login_code)
                ->first();

        if ($user) {
            $user->update([
                'login_code' => null
            ]);

            return $user->createToken($request->login_code)->plainTextToken();
        }

        return response()->json(['message' =>'Invalid varificaton code.'], 401);
    }
}
