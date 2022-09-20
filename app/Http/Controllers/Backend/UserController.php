<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    //-------------------------Index----------------------------------------------
    public function index(Request $request)
    {
        $users = User::all();
        if ($request->has('search')) {
            $users = User::where('username', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%")->get();
        }
        return view('users.index', compact('users'));
    }


    //-------------------------Create/Add----------------------------------------------
    public function create()
    {
        $roles = Role::get(['id', 'name']);

        return view('users.create', compact('roles'));
    }


    //-------------------------Store/Save----------------------------------------------
    public function store(UserStoreRequest $request)
    {
        User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => Carbon::now()
        ]);

        //-----or---------
        // User::create($request->validated());

        //-----or---------
        // $request->validated();

        // User::create([
        //   'username' => $request->username,
        //   'first_name' => $request->first_name,
        //   'last_name' => $request->last_name,
        //   'email' => $request->email,
        //   'password' => Hash::make($request->password),
        // ]);

        return redirect()->route('users.index')->with('message', 'User Register Succesfully');
    }


    //-------------------------Edit----------------------------------------------
    public function edit(User $user)
    {
        $roles = Role::get(['id', 'name']);
        return view('users.edit', compact('user', 'roles'));
    }


    //-------------------------Update----------------------------------------------
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        //----------Or--------------
        //$country->update($request->validated());

        return redirect()->route('users.index')->with('message', 'User Updated Succesfully');
    }


    //-------------------------Delete----------------------------------------------
    public function destroy(User $user)
    {
        if (auth()->user()->id == $user->id) {
            return redirect()->route('users.index')->with('message', 'You are deleting yourself.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('message', 'User Deleted Succesfully');
    }
    public function change_leaves(Request $request, User $user)
    {
        $request->validate([
            'casual_leave' => ['required', 'integer'],
            'annual_leave' => ['required', 'integer'],
        ]);

        $user->update([
            'casual_leave' => $request->casual_leave,
            'annual_leave' => $request->annual_leave
        ]);

        return redirect()->route('users.index')->with('message', 'User leaves Updated Succesfully');
    }
    public function verify_user(User $user)
    {
        $user->update(['email_verified_at' => Carbon::now()]);
        return redirect()->back()->with('success', 'User successfully verified!!!');
    }
}
