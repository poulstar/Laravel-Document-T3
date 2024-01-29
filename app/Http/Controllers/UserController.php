<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Actions\SMS;

class UserController extends Controller
{
    public function getLoginCode($phone)
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return $this->failResponse([
                'message' => 'User Not Found'
            ], 403);
        }

        $randomCode = Str::random(4);
        $user->verify_code = Hash::make($randomCode);
        $user->save();
        $state = SMS::sendSMS($user->phone, $user->name, $randomCode);
        if ($state) {
            return $this->successResponse([
                'message' => "Check Your Phone",
            ], 200);
        } elseif (!$state) {
            return $this->failResponse([
                'error' => "your request failed",
            ], 500);
        } else {
            return $this->failResponse([
                'error' => $state,
            ], 500);
        }
    }
}
