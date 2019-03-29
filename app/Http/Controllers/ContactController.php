<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chan;
use Illuminate\Support\Facades\Mail;
use App\Mail\Contact;
use App\ChanUser;
use App\User;

class ContactController extends Controller
{
    public function show(Request $request) {
		if($request->has('chan')) {
			$chan = Chan::where('name', $request->input('chan'))->first();
		}
		else $chan = null;
		$chans = Chan::where('hidden', false)->get();
		return view('contact.show', compact('chan', 'chans'));
	}

	public function store(Request $request) {
		if($request->input('chan_id') == 0) {
			$users = User::where('admin', true)->get();
			$validated = $this->validate($request, [
				'from' => 'required|email|confirmed',
				'fromName' => 'required|string',
				'body' => 'required|string'
			]);
		}
		else {
			$validated = $this->validate($request, [
				'chan_id' => 'required|numeric|exists:chans,id',
				'from' => 'required|email|confirmed',
				'fromName' => 'required|string',
				'body' => 'required|string'
			]);
			$users = ChanUser::where('chan_id', $validated['chan_id'])->where('admin', true)->with('user')->get()->pluck('user');
		}
		foreach($users as $user) {
			Mail::to($user)->queue(new Contact($validated));
		}
		success('Mail envoyé.');
		if($request->input('chan_id') == 0) {
			return redirect()->route('home');
		}
		return redirect()->route('chans.show', ['chan' => Chan::where('id', $validated['chan_id'])->first()->name]);
	}
}
