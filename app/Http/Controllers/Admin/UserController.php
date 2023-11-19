<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::get();
        return response()->json(compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateUserRequest $request)
    {
        $inputs = $request->validated();
        $data['user'] = User::create($inputs);
        $data['message'] = 'Created user successfully.';
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json(compact('user'));
        }
        return response()->json(['message' => 'User not found.'], '404');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $inputs = $request->validated();
        $data['user'] = $user->update($inputs);
        $data['message'] = 'Updated user successfully.';
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

        if (optional($user->transactions)->sum('unpaid') > 0) {
            $data['message'] = 'Cannot Delete user before pay all dues.';
            return response()->json($data, 401);
        }
        $user->delete();
        $data['message'] = 'Deleted user successfully.';
        return response()->json($data);
    }
}
