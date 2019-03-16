<?php

namespace App\Http\Controllers;

use App\Chan;
use Illuminate\Http\Request;

class ChanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$query = Chan::orderBy('name', 'ASC');
		if(auth()->guest() || !auth()->user()->isAdmin()) $query->where('hidden', false);
		$chans = $query->get();
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
            'description' => 'required'
        ]);
        $validated['hidden'] = $request->has('hidden');
        $chan = Chan::create($validated);
        success("Le chan ".$chan->displayName()." a été créé.");
        return redirect()->route('home');
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
        return view('chans.show', compact('chan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chan  $chan
     * @return \Illuminate\Http\Response
     */
    public function edit(Chan $chan)
    {
        //
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
		if($request->has('name'))
		{
			$request->merge(['name' => strtolower($request->name)]);
			if($request->name != $chan->name) {
				$validated = array_merge($validated, $this->validate($request, [
					"name" => 'required|unique:chans|alpha_dash'
				]));
			}
		}
		if($request->has('description') && $request->description != $chan->description)
		{
			$validated = array_merge($validated, $this->validate($request, [
				"description" => 'required'
			]));
		}
		$chan->update($validated);
		success("Le chan ".$chan->displayName()." a été modifié.");
		return redirect()->back();
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
		success("Le chan ".$chan->displayName()." a été supprimé.");
		return redirect()->back();

    }
}
