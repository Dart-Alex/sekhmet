<?php

namespace App\Http\Controllers;

use App\PostSubscriber;
use Illuminate\Http\Request;
use App\Chan;
use App\Post;

class PostSubscriberController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Chan $chan, Post $post)
    {
		$this->authorize('create', [PostSubscriber::class, $post]);
		$validated = $this->validate($request, [
			'name' => 'required|string'
		]);
		$validated['name'] = strtolower($validated['name']);
		$validated['user_id'] = null;
		if(PostSubscriber::where('name', $validated['name'])->where('post_id', $post->id)->exists())
		{
			danger($validated['name']." participe déjà à l'event.");
			return redirect()->back();
		}
		if(!auth()->guest())
		{
			$validated['user_id'] = auth()->user()->id;
			if(PostSubscriber::where('user_id', $validated['user_id'])->where('post_id', $post->id)->exists())
			{
				danger(auth()->user()->name." participe déjà à l'event.");
				return redirect()->back();
			}
		}
		$validated['post_id'] = $post->id;
		PostSubscriber::create($validated);
		success($validated['name']." participe à l'event.");
		return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PostSubscriber  $postSubscriber
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chan $chan, Post $post, PostSubscriber $postSubscriber)
    {
		$this->authorize('delete', $postSubscriber);
		$name = $postSubscriber->name;
		$postSubscriber->delete();
		success("$name ne participe plus à l'event.");
		return redirect()->back();
    }
}
