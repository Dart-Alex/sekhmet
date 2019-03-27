<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\IrcNameDeleting;
use App\Events\IrcNameCreated;

class IrcName extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id'
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

	protected $dispatchesEvents = [
		'deleting' => IrcNameDeleting::class,
		'created' => IrcNameCreated::class,
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function youtubeVideos() {
		return $this->hasMany(YoutubeVideo::class, 'name', 'name');
	}
}
