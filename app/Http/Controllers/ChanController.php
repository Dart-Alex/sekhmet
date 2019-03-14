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
		$chans = Chan::where('hidden', false)->orderBy('name', 'ASC')->get();
		return view('chans.index', compact('chans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
		$request->merge(['name' => strtolower($request->name)]);
        $validated = $request->validate([
            'name' => 'required|unique:chans|alpha_dash',
            'description' => 'required'
        ]);
        $validated['hidden'] = $request->has('hidden');
        $chan = Chan::create($validated);
        success("Le chan #".ucfirst($chan->name)." a été créé.");
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chan  $chan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chan $chan)
    {
        //
    }
}
