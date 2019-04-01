<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\PostDeleting;

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

	protected $dispatchesEvents = [
		'deleting' => PostDeleting::class,
	];

	public function chan()
	{
		return $this->belongsTo(Chan::class, 'chan_id', 'id');
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function postSubscribers()
	{
		return $this->hasMany(PostSubscriber::class);
	}

	public function setContentAttribute($value)
	{
		$this->attributes['content'] = clean($value);
	}

	public function hasImage() {
		return $this->getImage() != 'nothing';
	}

	public function getImage() {
        if($start = strpos($this->content, '<img')) {
            $end = strpos($this->content, '>', $start);
            $img = substr($this->content, $start, $end-$start+1);
            return preg_replace('/^.*src=.([^ \'"]*). .*$/', '$1', $img);
        }
        else {
            return 'nothing';
        }
    }
}
