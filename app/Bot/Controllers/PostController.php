<?php

namespace App\Bot\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Chan;
use App\PostSubscriber;
use App\IrcName;

class PostController extends Controller
{

    public function show(Chan $chan)
    {
		if(!$post = Post::where('chan_id', $chan->id)->whereDate('date', '>=', now()->toDateTimeString())->orderBy('date', 'ASC')->first()) {
			return ['error' => true];
		}
        return [
			"error" => false,
			"name" => $post->name,
			"date" => $post->date->diffForHumans().' ('.$post->date->isoFormat('LLLL').')',
			"url" => route('posts.show', ['chan' => $chan->name, 'post' => $post->id]),
			"subscribed" => $post->postSubcribers->count(),
			"comments" => $post->comments->count()
		];
    }

    public function list(Chan $chan)
    {
        if(!$post = Post::where('chan_id', $chan->id)->whereDate('date', '>=', now()->toDateTimeString())->orderBy('date', 'ASC')->first()) {
			return ['error' => true];
		}
		return [
			"error" => false,
			"subscribed" => $post->postSubscribers->pluck('name')->toArray()
		];
    }

    public function register(Request $request, Chan $chan)
    {
        if(!$post = Post::where('chan_id', $chan->id)->whereDate('date', '>=', now()->toDateTimeString())->orderBy('date', 'ASC')->first()) {
			return ['error' => true];
		}
		$messages = [];
		$data = $request->data;
		foreach($data as $name)
		{
			$name = strtolower($name);
			if($ircName = IrcName::where('name', $name)->first()) {
				if(PostSubscriber::where('user_id', $ircName->user_id)->where('post_id', $post->id)->exists()) {
					$messages[] = $ircName->user->name.' est déjà inscrit.';
				}
				else {
					PostSubscriber::create([
						'user_id' => $ircName->user_id,
						'post_id' => $post->id,
						'name' => $name
					]);
					$messages[] = $ircName->user->name.' inscrit.';
				}
			}
			else {
				if(PostSubscriber::where('post_id', $post->id)->where('name', $name)->exists()) {
					$messages[] = "$name est déjà inscrit.";
				}
				else {
					PostSubscriber::create([
						'user_id' => null,
						'post_id' => $post->id,
						'name' => $name
					]);
					$messages[] = $name.' inscrit.';
				}
			}
		}
		return [
			'error' => false,
			'messages' => $messages
		];
    }

    public function remove(Request $request, Chan $chan)
    {
        if(!$post = Post::where('chan_id', $chan->id)->whereDate('date', '>=', now()->toDateTimeString())->orderBy('date', 'ASC')->first()) {
			return ['error' => true];
		}
		$messages = [];
		$data = $request->data;
		foreach($data as $name)
		{
			$name = strtolower($name);
			if($ircName = IrcName::where('name', $name)->first()) {
				if($postSubscriber = PostSubscriber::where('user_id', $ircName->user_id)->where('post_id', $post->id)->first()) {
					$postSubscriber->delete();
					$messages[] = $ircName->user->name." n'est plus inscrit.";
				}
				else {
					$messages[] = $ircName->user->name." n'était pas inscrit.";
				}
			}
			else {
				if($postSubscriber = PostSubscriber::where('name', $name)->where('post_id', $post->id)->first())
				{
					$postSubscriber->delete();
					$messages[] = $name." n'est plus inscrit.";
				}
				else {
					$messages[] = $name." n'était pas inscrit.";
				}
			}
		}
		return [
			'error' => false,
			'messages' => $messages
		];
    }
}
