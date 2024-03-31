<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    //index
    public function index(Request $request)
    {
        //user with pagination
        $users = DB::table('users')
        ->when($request->input('name'), function ($query, $name) {
            $query->where ('name', 'like', '%'. $name . '%')
                ->orwhere ('email', 'like', '%'. $name . '%')
            ;
        })
        ->paginate (5);

        return view('pages.user.index', compact('users'));
    }

    //create
    public function create()
    {
        return view('pages.user.create');
    }

    //store
    public function store(Request $request)
    {
        //return view('pages.user.create');
        $request->validate([
        'name'=>'required',
        'email'=> 'required',
        'password'=> 'required|min:8',
        'role'=> 'required|in:admin,staff,user',
        ]);

        //store request

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('success', 'user created successfuly');

    }

    //show
    public function show($id)
    {
        return view('pages.dashboard');
    }

    //edit
    public function edit($id)
    {
        $user = User::FindOrFail($id);
        return view('pages.user.edit', compact('user'));
    }

    //update
    public function update(Request $request, $id)
    {
        //return view('pages.dashboard');
        $request->validate([
            'name'=>'required',
            'email'=> 'required',
            'role'=> 'required|in:admin,staff,user',
        ]);

        //update request
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        //if password not empty
        if ($request->password){
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('user.index')->with('success', 'user updated successfuly');
    }

    //destroy
    public function destroy($id)
    {
        //return view('pages.dashboard');
        //delete request
        $user = user::find($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'user delete successfuly');
    }


}
