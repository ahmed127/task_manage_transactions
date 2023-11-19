<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\LoginAdminRequest;

class AuthController extends Controller
{
    public function login(LoginAdminRequest $request)
    {
        $request->validated();

        $admin = Admin::firstWhere('email', $request->email);

        if (Hash::check(request('password'), $admin->password)) {
            return response()->json([
                'access_token'  => auth('api.admin')->login($admin),
                'token_type'    => 'bearer',
                'expires_in'    => auth('api.admin')->factory()->getTTL() * 60,
                'admin'         => $admin
            ], 200);
        }

        return response()->json([
            'messages' => 'These credentials do not match our records.'
        ], 403);
    }

    public function logout()
    {
        auth('api.admin')->logout(true);
        auth('api.admin')->invalidate(true);
        return response()->json(['message' => 'Logout Successfully.'], 403);
    }
}
