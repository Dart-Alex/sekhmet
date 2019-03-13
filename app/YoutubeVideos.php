<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideos extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chan_name', 'yid', 'name'
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
}
