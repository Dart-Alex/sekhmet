<?php

namespace App\Policies;

use App\User;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Chan;

class PostPolicy
{
    use HandlesAuthorization;

	public function index(?User $user, Chan $chan)
	{
		if(!$chan->hidden) return true;
		return ($user->isAdmin() || $chan->isAdmin($user));
	}
    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function view(?User $user, Post $post)
    {
		if(!$post->chan->hidden) return true;
		return ($user->isAdmin() || $post->chan->isAdmin($user));
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Chan $chan)
    {
        return $user->isAdmin() || $chan->isAdmin($user);
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        return $user->isAdmin() || $post->chan->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        return $user->isAdmin() || $post->chan->isAdmin($user);
    }
}
