<?php

namespace App\Policies;

use App\User;
use App\IrcName;
use Illuminate\Auth\Access\HandlesAuthorization;

class IrcNamePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the irc name.
     *
     * @param  \App\User  $user
     * @param  \App\IrcName  $ircName
     * @return mixed
     */
    public function view(?User $user, IrcName $ircName)
    {
        return true;
    }

    /**
     * Determine whether the user can create irc names.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the irc name.
     *
     * @param  \App\User  $user
     * @param  \App\IrcName  $ircName
     * @return mixed
     */
    public function update(User $user, IrcName $ircName)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the irc name.
     *
     * @param  \App\User  $user
     * @param  \App\IrcName  $ircName
     * @return mixed
     */
    public function delete(User $user, IrcName $ircName)
    {
        return $user->id == $ircName->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the irc name.
     *
     * @param  \App\User  $user
     * @param  \App\IrcName  $ircName
     * @return mixed
     */
    public function restore(User $user, IrcName $ircName)
    {
        //
    }
}
