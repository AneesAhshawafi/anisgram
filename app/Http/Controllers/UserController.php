<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\User;
// use Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(User $user)
    {

        return view('user.profile', compact('user'));
    }

    public function edit(User $user)
    {
        // method 1
        // if($user->id !== Auth::id()){
        //         abort(403,"You are not autherized to access this page!");
        //     }
        // method 2
        // abort_if($user->id !== Auth::id(),403,"You are not autherized to access this page!");
        // method 3
        // abort_unless($user->id === Auth::id(),403,"You are not autherized to access this page!");
        // method 4
        // abort_if(Auth::user()->cannot('edit_update_profile',$user),403,"You are not autherized to access this page!");
        // method 5
        // abort_if(!Gate::allows('edit_update_profile',$user),403,"You are not autherized to access this page!");
        // abort_if(Gate::denies('edit_update_profile',$user),403,"You are not autherized to access this page!");
        // Gate::authorize('edit_update_profile',$user);

        return view('user.edit', compact('user'));
    }

    public function update(UpdateUserProfileRequest $request, User $user)
    {
        $data = $request->safe()->collect();
        // if ($data['password'] == '') {
        //     unset($data['password']);
        // } else {
        //     $data['password'] = Hash::make($data['password']);
        // }
        if ($data->get('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data->forget('password');
        }

        if ($data->has('image')) {
            $path = $request->file('image')->store('users', 'public');
            $data['image'] = '/storage/'.$path;
        }
        $data['private_account'] = $request->has('private_account');

        $user->update($data->toArray());
        session()->flash('success', __('Your profile has been updated successfully!'));

        return redirect()->route('user_profile', $user);
    }

    public function follow(User $user)
    {
        auth()->user()->follow($user);

        return back();
    }

    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);

        return back();
    }
}
