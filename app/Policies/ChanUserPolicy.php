<?php

namespace App\Policies;

use App\User;
use App\ChanUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Chan;

class ChanUserPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can create chan users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Chan $chan)
    {
		return (!$chan->hasUser($user) && !$chan->hidden) || $user->isAdmin() || $chan->isAdmin($user);
    }

    /**
     * Determine whether the user can update the chan user.
     *
     * @param  \App\User  $user
     * @param  \App\ChanUser  $chanUser
     * @return mixed
     */
    public function update(User $user, ChanUser $chanUser, Chan $chan)
    {
        return $user->isAdmin() || $chan->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the chan user.
     *
     * @param  \App\User  $user
     * @param  \App\ChanUser  $chanUser
     * @return mixed
     */
    public function delete(User $user, ChanUser $chanUser, Chan $chan)
    {
        return $chanUser->user_id == $user->id || $user->isAdmin() || $chan->isAdmin($user);
    }
}
