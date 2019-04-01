<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Chan;
use Carbon\Carbon;

class PostController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Chan $chan)
	{
		$this->authorize('index', [Post::class, $chan]);
		$posts = Post::where('chan_id', $chan->id)->whereDate('date', '>=', now()->toDateTimeString())->orderBy('date', 'ASC')->with(['comments', 'postSubscribers'])->get();
		$oldPosts = Post::where('chan_id', $chan->id)->whereDate('date', '<', now()->toDateTimeString())->orderBy('date', 'ASC')->with(['comments', 'postSubscribers'])->get();
		return view('posts.index', compact('chan', 'posts', 'oldPosts'));
	}

	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function create(Chan $chan)
	{
		$this->authorize('create', [Post::class, $chan]);
		return view('posts.create', compact('chan'));
	}

	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store(Request $request, Chan $chan)
	{
		$this->authorize('create', [Post::class, $chan]);
		$validated = $this->validate($request, [
			'date' => 'required|date:Y-m-d\TH:i|after:now',
			'content' => 'required|string',
			'name' => 'required|string'
		]);
		$validated['comments_allowed'] = $request->has('comments_allowed');
		$validated['date'] = Carbon::createFromFormat('Y-m-d\TH:i', $validated['date']);
		$validated['chan_id'] = $chan->id;
		Post::create($validated);
		success('Event ' . $validated['name'] . ' créé.');
		return redirect()->route('posts.index', ['chan' => $chan->name]);
	}

	/**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
	public function show(Chan $chan, Post $post)
	{
		$this->authorize('view', $post);
		return view('posts.show', compact('chan', 'post'));
	}

	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
	public function edit(Chan $chan, Post $post)
	{
		$this->authorize('update', $post);
		return view('posts.edit', compact('chan', 'post'));
	}

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, Chan $chan, Post $post)
	{
		$this->authorize('update', $post);
		if(!auth()->user()->isAdmin()) {
			$validated = $this->validate($request, [
				'date' => 'required|date:Y-m-d\TH:i|after:now',
				'content' => 'required|string',
				'name' => 'required|string'
			]);
		}
		else {
			$validated = $this->validate($request, [
				'date' => 'required|date:Y-m-d\TH:i',
				'content' => 'required|string',
				'name' => 'required|string'
			]);
		}
		$validated['comments_allowed'] = $request->has('comments_allowed');
		$validated['date'] = Carbon::createFromFormat('Y-m-d\TH:i', $validated['date']);
		$post->fill($validated);
		$post->save();
		success('Event ' . $post->name . ' modifié.');
		return redirect()->route('posts.show', ['chan' => $chan->name, 'post' => $post->id]);
	}

	/**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
	public function destroy(Chan $chan, Post $post)
	{
		$this->authorize('delete', $post);
		$name = $post->name;
		$post->delete();
		success("Event $name supprimé.");
		return redirect()->route('posts.index', ['chan' => $chan]);
	}
}
