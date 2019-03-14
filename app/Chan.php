<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chan extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'name', 'description', 'hidden'
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

	public function getRouteKeyName()
	{
		return 'name';
	}
	public function chanUsers()
	{
		return $this->hasMany(ChanUser::class);
	}
	public function isAdmin(User $user) {
		$admins = ChanUser::where('admin', true)
			->where('chan_id', $this->id)
			->get()
			->pluck('user_id')
			->toArray();
		return in_array($user->id, $admins);
	}
	public function displayName() {
		return '#'.ucfirst($this->name).(($this->hidden)?' (caché)':'');
	}
}
