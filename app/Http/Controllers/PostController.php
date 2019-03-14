<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Chan;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Chan $chan)
    {
		$posts = Post::where('chan_id', $chan->id)->whereDate('date', '>=', now()->toDateTimeString())->orderBy('date', 'ASC')->get();
		$oldPosts = Post::where('chan_id', $chan->id)->whereDate('date', '<', now()->toDateTimeString())->orderBy('date', 'ASC')->get();
		return view('posts.index', compact('chan', 'posts', 'oldPosts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Chan $chan)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Chan $chan)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Chan $chan, Post $post)
    {
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
