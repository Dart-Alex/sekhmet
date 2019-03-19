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
		'name',
		'description',
		'hidden',
		'config_quiet',
		'config_youtube_active',
		'config_youtube_timer',
		'config_event_active',
		'config_event_timer',
		'config_spam_active',
		'config_spam_timer',
		'config_badwords'

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
		'hidden' => 'boolean',
		'config_quiet' => 'boolean',
		'config_youtube_active' => 'boolean',
		'config_youtube_timer' => 'integer',
		'config_event_active' => 'boolean',
		'config_event_timer' => 'integer',
		'config_spam_active' => 'boolean',
		'config_spam_timer' => 'integer',
		'config_badwords' => 'array'

	];

	/**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => ChanAdded::class,
		'updated' => ChanUpdated::class,
		'deleting' => ChanDeleted::class,
    ];

	public function getRouteKeyName()
	{
		return 'name';
	}

	public function setNameAttribute($value)
	{
		$this->attributes['name'] = strtolower($value);
	}

	public function chanUsers()
	{
		return $this->hasMany(ChanUser::class);
	}

	public function posts()
	{
		return $this->hasMany(Post::class, 'id', 'chan_id');
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

	public function getConfig() {
		$adminUsers = ChanUser::where('chan_id', $this->id)->where('admin', true)->get();
		$admins = [];
		foreach($adminUsers as $adminUser) {
			foreach($adminUser->user->ircNames as $ircName) {
				$admins[] = strtolower($ircName->name);
			}
		}
		return [
			"quiet" => $this->config_quiet,
			"youtube" => [
				"active" => $this->config_youtube_active,
				"timer" => $this->config_youtube_timer
			],
			"spam" => [
				"active" => $this->config_spam_active,
				"timer" => $this->config_spam_timer
			],
			"event" => [
				"active" => $this->config_event_active,
				"timer" => $this->config_event_timer
			],
			"badwords" => $this->config_badwords,
			"admins" => $admins
		];
	}
}
