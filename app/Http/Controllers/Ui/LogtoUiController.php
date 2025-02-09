<?php

namespace App\Http\Controllers\Ui;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Logto\Sdk\LogtoClient;

class LogtoUiController
{
    public function callback(LogtoClient $client)
    {
        try {
            $client->handleSignInCallback(); // Handle a lot of stuff
        } catch (\Throwable $exception) {
            return $exception; // Change this to your error handling logic
        }

        $userInfo = $client->fetchUserInfo();
        User::firstOrCreate(
            ['external_id' => $userInfo->sub],
            [
                'name' => $userInfo->name ?? '',
                'email' => $userInfo->email,
                'external_id' => $userInfo->sub,
                'password' => Hash::make(
                    $userInfo->sub
                ),
                'email_verified' => true
            ]
        );


        return redirect('/'); // Redirect the user to the home page after a successful sign-in
    }
}
