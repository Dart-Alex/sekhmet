<?php

namespace App\Http\Controllers;

use App\YoutubeVideo;
use Illuminate\Http\Request;
use App\Chan;
use App\User;

class YoutubeVideoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Chan $chan)
    {
		$youtubeVideos = YoutubeVideo::where('chan_name', $chan->name)->orderBy('id', 'DESC')->with('ircName.user')->paginate(20);

		return view('youtubeVideos.index', compact('chan', 'youtubeVideos'));
	}

	public function indexName(Chan $chan, $name)
	{
		$names = [$name];
		if($user = User::where('name', $name)->with('ircNames')->first()) {
			$names = array_merge($names, $user->ircNames->pluck('name')->toArray());
		}
		$youtubeVideos = YoutubeVideo::where('chan_name', $chan->name)->whereIn('name', $names)->orderBy('id', 'DESC')->with('ircName.user')->paginate(20);

		return view('youtubeVideos.index', compact('chan', 'youtubeVideos', 'name'));
	}

	public function search(Request $request, Chan $chan) {
		$name = $this->validate($request, [
			'name' => 'required'
		])['name'];
		$names = [$name];
		if($user = User::where('name', $name)->with('ircNames')->first()) {
			$names = array_merge($names, $user->ircNames->pluck('name')->toArray());
		}
		if(YoutubeVideo::where('chan_name', $chan->name)->whereIn('name', $names)->exists()) {
			return redirect()->route('youtubeVideos.indexName', ['chan' => $chan->name, 'name' => $name]);
		}
		warning("Aucune vidéo trouvée pour $name.");
		return redirect()->back();
	}
}
