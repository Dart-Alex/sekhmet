<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class YoutubeVideo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chan_name', 'yid', 'name', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
	];

	public function chan() {
		return $this->belongsTo(Chan::class, 'chan_name', 'name');
	}

	public function ircName() {
		return $this->belongsTo(IrcName::class, 'name', 'name');
	}

	public function getInfo() {
		return static::fetchInfo($this->yid);
	}

	public function getIndex() {
		if(!$index = Cache::get('yt-video-index-'.$this->id)) {
			$index = static::where('chan_name', $this->chan_name)->where('id', '<=', $this->id)->orderBy('id', 'ASC')->count();
			// $videos = static::where('chan_name', $this->chan_name)->orderBy('created_at', 'ASC')->get();
			// $id = $this->id;
			// $index = $videos->search(function ($video, $key) use ($id) {
			// 	return $video->id == $id;
			// }) + 1;
			Cache::put('yt-video-index-'.$this->id, $index, now()->addMonth());
		}
		if(!Cache::has("yt-video-yidByIndex-$this->chan_name-$index")) {
			Cache::put("yt-video-yidByIndex-$this->chan_name-$index", $this->yid, now()->addMonth());
		}
		return $index;
	}

	static function getYidByIndex(int $index, Chan $chan) {
		if($yid = Cache::get("yt-video-yidByIndex-$chan->name-$index")) {
			return $yid;
		}
		if($video = YoutubeVideo::where('chan_name', $chan->name)->orderBy('created_at', 'ASC')->offset($index-1)->first()) {
			Cache::put("yt-video-yidByIndex-$chan->name-$index", $video->yid, now()->addMonth());
			return $video->yid;
		}
		return null;
	}

	static function fetchInfo($yid) {
		if(!$yid) return false;
		if(!$result = Cache::get('yid-'.$yid)) {
			$result = \Youtube::getVideoInfo($yid);
			Cache::put('yid-'.$yid, $result, now()->addMonth());
		}
		return $result;
	}

	static function search($q) {
		if(Cache::has('yt-search-'.$q)) {
			return static::fetchInfo(Cache::get('yt-search-'.$q));
		}
		$params = [
			'q' => $q,
			'type' => 'video',
			'regionCode' => config('app.locale', 'fr'),
			'relevanceLanguage' => config('app.locale', 'fr'),
			'maxResults' => '1',
			'part' => 'id'
		];
		if($result = \Youtube::searchAdvanced($params)) $yid = $result[0]->id->videoId;
		else $yid = false;
		Cache::put('yt-search-'.$q, $yid, now()->addHour());
		return static::fetchInfo($yid);
	}
}
