<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\LoginUserRequest;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $request->validated();

        $user = User::firstWhere('email', $request->email);

        if (Hash::check(request('password'), $user->password)) {
            return response()->json([
                'access_token'  => auth('api.user')->login($user),
                'token_type'    => 'bearer',
                'expires_in'    => auth('api.user')->factory()->getTTL() * 60,
                'user'          => $user
            ], 200);
        }

        return response()->json('These credentials do not match our records.', 403);
    }

    public function logout()
    {
        auth('api.user')->logout(true);
        auth('api.user')->invalidate(true);
        return response()->json(['message' => 'Logout Successfully.'], 403);
    }
}
