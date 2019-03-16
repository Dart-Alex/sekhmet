<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
		if(Cache::has('yt-video-index-'.$this->id)) {
			$index = Cache::get('yt-video-index-'.$this->id);
		}
		else {
			$index = static::where('chan_name', $this->chan_name)->where('created_at', '<=', $this->created_at)->count();
			Cache::put('yt-video-index-'.$this->id, $index, now()->addMonth());
		}
		return $index;
	}

	static function fetchInfo($yid) {
		if(!$yid) return false;
		if(Cache::has('yid-'.$yid)) {
			return Cache::get('yid-'.$yid);
		}
		$result = \Youtube::getVideoInfo($yid);
		Cache::put('yid-'.$yid, $result, now()->addMonth());
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
