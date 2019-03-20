<?php

namespace App\Policies;

use App\User;
use App\Chan;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the chan.
     *
     * @param  \App\User  $user
     * @param  \App\Chan  $chan
     * @return mixed
     */
    public function view(?User $user, Chan $chan)
    {
		if(!$chan->hidden) return true;
		else if(auth()->guest()) return false;
		else
		{
			return ($chan->isAdmin($user) || $user->isAdmin());
		}
    }

    /**
     * Determine whether the user can create chans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the chan.
     *
     * @param  \App\User  $user
     * @param  \App\Chan  $chan
     * @return mixed
     */
    public function update(User $user, Chan $chan)
    {
        return ($chan->isAdmin($user) || $user->isAdmin());
    }

    /**
     * Determine whether the user can delete the chan.
     *
     * @param  \App\User  $user
     * @param  \App\Chan  $chan
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isAdmin();
	}

	/**
	 * Determine wether the user can join the chan.
	 *
	 * @param \App\User $user
	 * @param \App\Chan $chan
	 * @return mixed
	 */
	public function join(User $user, Chan $chan) {
		if($chan->hidden) {
			return $user->isAdmin();
		}
		return true;
	}
}
