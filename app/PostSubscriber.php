<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostSubscriber extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'name', 'post_id', 'user_id'
	];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function post() {
		return $this->belongsTo(Post::class);
	}

	public function getNameAttribute() {
		if($this->user_id !== null)
		{
			return $this->user->name;
		}
		return $this->attributes['name'];
	}
}
