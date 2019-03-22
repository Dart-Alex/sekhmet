<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ChanUserAdminSet;
use App\Events\ChanUserDeleting;

class ChanUser extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'user_id', 'admin', 'chan_id'
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
		'updated_at' => 'datetime',
		'admin' => 'boolean'
	];

	/**
     * The event map for the model.
     *
     * @var array
     */
	protected $dispatchesEvents = [
		'deleting' => ChanUserDeleting::class,
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function chan()
	{
		return $this->belongsTo(Chan::class, 'chan_id', 'id');
	}

	public function setAdminAttribute($value)
	{
		if ($value != $this->admin) event(new ChanUserAdminSet());
		$this->attributes['admin'] = $value;
	}
}
