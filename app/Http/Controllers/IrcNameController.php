<?php

namespace App\Http\Controllers;

use App\IrcName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IrcNameController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$this->authorize('create', IrcName::class);
		$request->merge(['name' => strtolower($request->name)]);
		$validated = $this->validate($request, [
			'name' => 'required|string|unique:irc_names,name',
			'user_id' => 'required|numeric|exists:users,id'
		]);
		if(auth()->user()->isAdmin()) {
			IrcName::create($validated);
			success("Pseudo ajouté à l'utilisateur.");
		}
		else {
			$token = Str::random(16);
			$validated['token'] = $token;
			Cache::put('ircName-validation-token-'.$token, $validated, now()->addHour());
			Cache::put('ircName-validation-user-'.$validated['user_id'], $validated, now()->addHour());
			warning("Vous avez une heure pour valider votre pseudo. Instructions sur votre profil.");
		}
		return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\IrcName  $ircName
     * @return \Illuminate\Http\Response
     */
    public function destroy(IrcName $ircName)
    {
		$this->authorize('delete', $ircName);
		$ircName->delete();
		success('Pseudo supprimé.');
		return redirect()->back();
    }
}
