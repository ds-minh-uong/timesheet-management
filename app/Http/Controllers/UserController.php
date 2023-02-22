<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{

    public function index()
    {
        $this->authorize('manageUser', Auth::user());
        $users = User::with('manager')->get();

        return view('manage-user', ['users' => $users]);
    }

    //update user's role
    public function update(User $user, ProfileUpdateRequest $request) {
        $req = $request->validated();
        $this->authorize('updateRole', Auth::user());

        $user->role = $req['role'];
        $user->save();
        return Redirect::route('manage.user');
    }
    public function destroy(User $user)
    {
        $this->authorize('delete', Auth::user());
        $user->delete();
        return Redirect::route('manage.user');
    }
}
