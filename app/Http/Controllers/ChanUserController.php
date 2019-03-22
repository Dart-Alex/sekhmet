<?php

namespace App\Http\Controllers;

use App\ChanUser;
use Illuminate\Http\Request;
use App\Chan;
use App\User;

class ChanUserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Chan $chan)
    {
		$this->authorize('create', [ChanUser::class, $chan]);
		$user = auth()->user();
		if(($user->isAdmin() || $chan->isAdmin($user)) && $request->has('user_id')) {
			$user_id = (int) $this->validate($request, [
				'user_id' => 'required|numeric|exists:users,id'
			])['user_id'];
			$addedUser = User::where('id', $user_id)->first();
		}
		else {
			$addedUser = $user;
		}
		if($chan->hasUser($addedUser)) {
			danger("$addedUser->name a déjà rejoint ".$chan->displayName().".");
		}
		else {
			ChanUser::create(['user_id' => $addedUser->id, 'chan_id' => $chan->id]);
			success("$addedUser->name a rejoint ".$chan->displayName().".");
		}
		return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ChanUser  $chanUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chan $chan, ChanUser $chanUser)
    {
		$this->authorize('update', [$chanUser, $chan]);
		$chanUser->admin = !$chanUser->admin;
		$chanUser->save();
		$name = $chanUser->user->name;
		$message = ($chanUser->admin?'est maintenant':"n'est plus");
		success("$name $message administrateur sur ".$chan->displayName());
		return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ChanUser  $chanUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chan $chan, ChanUser $chanUser)
    {
		$this->authorize('delete', [$chanUser, $chan]);
		$name = $chanUser->user->name;
		$chanUser->delete();
		success("$name est parti de ".$chan->displayName().'.');
		return redirect()->back();
    }
}
