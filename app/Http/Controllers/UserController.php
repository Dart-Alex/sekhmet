<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$this->authorize('index', User::class);
		$users = User::all();
		return view('users.index', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
		$this->authorize('update', $user);
		return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
		$this->authorize('update', $user);
		$validated = [];
		if(auth()->user()->isAdmin()) {
			$validated['admin'] = $request->has('admin');
		}
		if($request->has('email') && $request->email != $user->email) {
			$validated = array_merge($validated, $this->validate($request, [
				"email" => 'email|confirmed|unique:users,email'
			]));
			$validated['email_verified_at'] = null;
		}
		if($request->has('name') && $request->name != $user->name) {
			$validated = array_merge($validated, $this->validate($request, [
				"name" => 'unique:users,name'
			]));
		}
		$user->update($validated);
		success('Profil mis à jour.');
		return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
		$this->authorize('delete', $user);
		$self = $user->id == auth()->user()->id;

		if($self) {
			return redirect()->route('logout');
		}
		return redirect()->route('users.index');
    }
}
