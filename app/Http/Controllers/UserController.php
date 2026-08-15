<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(User $user)
    {

        return view('user.profile', compact('user'));
    }

    public function edit(User $user)
    {
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
            $data['image'] = 'storage/'.$path;
        }
        $data['private_account'] = $request->has('private_account');

        $user->update($data->toArray());
        session()->flash('success', __('Your profile has been updated successfully!'));

        return redirect()->route('user_profile', $user);
    }
}
