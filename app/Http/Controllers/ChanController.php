<?php

namespace App\Http\Controllers;

use App\Chan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\User;

class ChanController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index()
	{
		$chans = Chan::orderBy('name', 'ASC')->get();
		return view('chans.index', compact('chans'));
	}

	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function create()
	{
		$this->authorize('create', Chan::class);
		return view('chans.create');
	}

	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store(Request $request)
	{
		$this->authorize('create', Chan::class);
		$request->merge(['name' => strtolower($request->name)]);
		$validated = $request->validate([
			'name' => 'required|unique:chans|alpha_dash',
			'description' => 'required',
			'config_youtube_timer' => 'numeric',
			'config_spam_timer' => 'numeric',
			'config_event_timer' => 'numeric'
		]);
		$validated['hidden'] = $request->has('hidden');
		$validated['config_youtube_active'] = $request->has('config_youtube_active');
		$validated['config_spam_active'] = $request->has('config_spam_active');
		$validated['config_event_active'] = $request->has('config_event_active');
		$chan = Chan::create($validated);
		success("Le chan " . $chan->displayName() . " a été créé.");
		return redirect()->route('chans.index');
	}

	/**
     * Display the specified resource.
     *
     * @param  \App\Chan  $chan
     * @return \Illuminate\Http\Response
     */
	public function show(Chan $chan)
	{
		$this->authorize('view', $chan);
		$users = null;
		if(Gate::allows('update', $chan)) $users = User::orderBy('name', 'ASC')->get();

		return view('chans.show', compact('chan', 'users'));
	}

	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chan  $chan
     * @return \Illuminate\Http\Response
     */
	public function edit(Chan $chan)
	{
		$this->authorize('update', $chan);
		return view('chans.edit', compact('chan'));
	}

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chan  $chan
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, Chan $chan)
	{
		$this->authorize('update', $chan);
		$validated = [];
		$validated['hidden'] = $request->has('hidden');
		$validated['config_youtube_active'] = $request->has('config_youtube_active');
		$validated['config_spam_active'] = $request->has('config_spam_active');
		$validated['config_event_active'] = $request->has('config_event_active');
		if ($request->has('name') && auth()->user()->isAdmin()) {
				$request->merge(['name' => strtolower($request->name)]);
				if ($request->name != $chan->name) {
					$validated = array_merge($validated, $this->validate($request, [
						"name" => 'required|unique:chans|alpha_dash'
					]));
				}
			}
		if ($request->has('description') && $request->description != $chan->description) {
				$validated = array_merge($validated, $this->validate($request, [
					"description" => 'required'
				]));
			}
		if ($request->has('config_youtube_timer') && $request->config_youtube_timer != $chan->config_youtube_timer) {
				$validated = array_merge($validated, $this->validate($request, [
					"config_youtube_timer" => 'numeric'
				]));
			}
		if ($request->has('config_spam_timer') && $request->config_spam_timer != $chan->config_spam_timer) {
				$validated = array_merge($validated, $this->validate($request, [
					"config_spam_timer" => 'numeric'
				]));
			}
		if ($request->has('config_event_timer') && $request->config_event_timer != $chan->config_event_timer) {
				$validated = array_merge($validated, $this->validate($request, [
					"config_event_timer" => 'numeric'
				]));
			}
		$chan->update($validated);
		success("Le chan " . $chan->displayName() . " a été modifié.");
		return redirect()->route('chans.show', ['chan' => $chan->name]);
	}

	/**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chan  $chan
     * @return \Illuminate\Http\Response
     */
	public function destroy(Chan $chan)
	{
		$this->authorize('delete', $chan);
		$chan->delete();
		success("Le chan " . $chan->displayName() . " a été supprimé.");
		return redirect()->route('chans.index');
	}
}
