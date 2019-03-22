<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Events\UserAdminSet;
use App\Events\UserDeleting;

class User extends Authenticatable implements MustVerifyEmail
{
	use Notifiable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'name', 'email', 'password', 'admin', 'email_verified_at'
	];

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'email_verified_at' => 'datetime',
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
		'deleting' => UserDeleting::class,
	];

	public function isAdmin()
	{
		return $this->admin;
	}

	public function ircNames()
	{
		return $this->hasMany(IrcName::class);
	}

	public function chanUsers()
	{
		return $this->hasMany(ChanUser::class);
	}

	public function hasChan(Chan $chan)
	{
		return ChanUser::where('chan_id', $chan->id)->where('user_id', $this->id)->exists();
	}

	public function setAdminAttribute($value)
	{
		if ($value != $this->admin) event(new UserAdminSet());
		$this->attributes['admin'] = $value;
	}
}
