<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\CommentAdded;
use App\Events\CommentUpdated;
use App\Events\CommentDeleted;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'user_id', 'reply_to', 'name', 'content'
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
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
	];

	/**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => CommentAdded::class,
		'updated' => CommentUpdated::class,
		'deleting' => CommentDeleted::class,
    ];

	public function post() {
		return $this->belongsTo(Post::class);
	}

	public function replies() {
		return $this->hasMany(Comment::class, 'reply_to', 'id');
	}

	public function user() {
		return $this->belongsTo(User::class);
	}
}
