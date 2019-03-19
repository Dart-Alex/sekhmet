<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function chan() {
		return $this->belongsTo(Chan::class, 'chan_id', 'id');
	}
}
