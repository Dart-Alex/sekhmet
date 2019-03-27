<?php

namespace App\Bot\Controllers;

use App\YoutubeVideo;
use Illuminate\Http\Request;
use App\Chan;
use Carbon\CarbonInterval;
use App\User;


class YoutubeVideoController extends Controller
{
	protected function returnVideo(YoutubeVideo $video, $videoInfo, $new = false) {
		$return = [
			"url" => "https://youtu.be/".$videoInfo->id,
			"title" => preg_replace('/[\p{Cyrillic}]/u', '?', $videoInfo->snippet->title),
			"duration" => CarbonInterval::create($videoInfo->contentDetails->duration)->forHumans(),
			"error" => false,
			"new" => $new,
		];
		if(!$new) {
			$return["date"] = $video->created_at->format('d/m/Y');
			$return['name'] = $video->displayName();
			$return['index'] = $video->getIndex();
		}
		return $return;
	}

	protected function returnByYid(Chan $chan, $name, $yid) {
		$new = true;
		if($video = YoutubeVideo::where('chan_name', $chan->name)->where('yid', $yid)->with('ircName.user')->first()) {
			$new = false;
		}
		else {
			$video = YoutubeVideo::create([
				'chan_name' => $chan->name,
				'yid' => $yid,
				'name' => $name
			]);
		}
		if(!$videoInfo = $video->getInfo()) {
			$video->delete();
			return [
				"error" => true,
				"message" => "Aucune vidéo trouvée.",
			];
		}
		return $this->returnVideo($video, $videoInfo, $new);
	}

    public function getRandom(Chan $chan) {
		$videoInfo = false;
		while(!$videoInfo) {
			if($video = YoutubeVideo::where('chan_name', $chan->name)->inRandomOrder()->with('ircName.user')->first()) {
				$videoInfo = $video->getInfo();
				if(!$videoInfo) $video->delete();
			}
			else {
				return [
					"message" => "Aucune vidéo pour ".$chan->displayName().".",
					"error" => true,
				];
			}
		}
		return $this->returnVideo($video, $videoInfo);
	}

	public function getRandomByUser(Chan $chan, $names) {
		$videoInfo = false;
		while(!$videoInfo) {
			if($video = YoutubeVideo::where('chan_name', $chan->name)->whereIn('name', $names)->with('ircName.user')->inRandomOrder()->first()) {
				$videoInfo = $video->getInfo();
				if(!$videoInfo) $video->delete();
			}
			else {
				return [
					"message" => "Aucune vidéo pour ".$names[0]." sur ".$chan->displayName().".",
					"error" => true,
				];
			}
		}
		return $this->returnVideo($video, $videoInfo);

	}

	public function count(Chan $chan) {
		$count = YoutubeVideo::where('chan_name', $chan->name)->count();
		if($count == 0) {
			return [
				'error' => true,
				'message' => 'Aucune vidéo n\'a été partagée sur #'.$chan->name
			];
		}
		$return = [
			"error" => false,
			"count" => $count
		];
		$return['oldest'] = YoutubeVideo::where('chan_name', $chan->name)
			->orderBy('created_at', 'ASC')
			->first()
			->created_at
			->format('d/m/Y');
		return $return;
	}

	public function countByUser(Chan $chan, $name) {
		$names = [$name];
		if($user = User::where('name', $name)->wuth('ircNames')->first()) {
			$names = array_merge($names, $user->ircNames->pluck('name')->toArray());
		}
		$count = YoutubeVideo::where('chan_name', $chan->name)->whereIn('name', $names)->count();
		if($count == 0) {
			return [
				'error' => true,
				'message' => 'Aucune vidéo n\'a été partagée par '.$name.' sur #'.$chan->name
			];
		}
		$return = [
			"error" => false,
			"count" => $count
		];
		$return['oldest'] = YoutubeVideo::where('chan_name', $chan->name)->whereIn('name', $names)
			->orderBy('created_at', 'ASC')
			->first()
			->created_at
			->format('d/m/Y');
		return $return;
	}

	public function search(Request $request, Chan $chan) {
		$query = $request->search_query;
		$name = $request->name;
		$names = [$query];
		if($user = User::where('name', $query)->with('ircNames')->first()) {
			$names = array_merge($names, $user->ircNames->pluck('name')->toArray());
		}
		if(YoutubeVideo::where('chan_name', $chan->name)->whereIn('name', $names)->exists()) {
			return $this->getRandomByUser($chan, $names);
		}
		else if (preg_match('/^\d+$/', $query)) {
			$yid = YoutubeVideo::getYidByIndex((int) $query, $chan);
		}
		else $yid = YoutubeVideo::search($query)->id;
		if(!$yid) {
			return [
				"error" => true,
				"message" => "Aucune vidéo trouvée."
			];
		}
		return $this->returnByYid($chan, $name, $yid);

	}

	public function fetch(Request $request, Chan $chan) {
		$name = $request->name;
		$yid = $request->yid;
		return $this->returnByYid($chan, $name, $yid);
	}
}
