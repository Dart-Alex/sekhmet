<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chan_id', 'name', 'content', 'date', 'comments_allowed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
		'date' => 'datetime',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
	];

	public function chan() {
		return $this->belongsTo(Chan::class, 'chan_id', 'id');
	}

	public function comments() {
		return $this->hasMany(Comment::class);
	}
}
