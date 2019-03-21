<?php

namespace App\Bot\Controllers;

use App\YoutubeVideo;
use Illuminate\Http\Request;
use App\Chan;
use Carbon\CarbonInterval;


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
			$return['name'] = $video->name;
			$return['index'] = $video->getIndex();
		}
		return $return;
	}

	protected function returnByYid(Chan $chan, $name, $yid) {
		$new = true;
		if($video = YoutubeVideo::where('chan_name', $chan->name)->where('yid', $yid)->first()) {
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
			if($video = YoutubeVideo::where('chan_name', $chan->name)->inRandomOrder()->first()) {
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

	public function getRandomByUser(Chan $chan, $name) {
		$videoInfo = false;
		while(!$videoInfo) {
			if($video = YoutubeVideo::where('chan_name', $chan->name)->where('name', $name)->inRandomOrder()->first()) {
				$videoInfo = $video->getInfo();
				if(!$videoInfo) $video->delete();
			}
			else {
				return [
					"message" => "Aucune vidéo pour ".$name." sur ".$chan->displayName().".",
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
		$count = YoutubeVideo::where('chan_name', $chan->name)->where('name', $name)->count();
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
		$return['oldest'] = YoutubeVideo::where('chan_name', $chan->name)->where('name', $name)
			->orderBy('created_at', 'ASC')
			->first()
			->created_at
			->format('d/m/Y');
		return $return;
	}

	public function search(Request $request, Chan $chan) {
		$query = $request->search_query;
		$name = $request->name;
		if(YoutubeVideo::where('chan_name', $chan->name)->where('name', $query)->exists()) {
			return $this->getRandomByUser($chan, $query);
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
