<?php

namespace App\Policies;

use App\User;
use App\PostSubscriber;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Post;

class PostSubscriberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create post subscribers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(?User $user, Post $post)
    {
		return $user->can('view', $post) && (auth()->guest() || !PostSubscriber::where('user_id', $user->id)->where('post_id', $post->id)->exists());
    }

    /**
     * Determine whether the user can delete the post subscriber.
     *
     * @param  \App\User  $user
     * @param  \App\PostSubscriber  $postSubscriber
     * @return mixed
     */
    public function delete(User $user, PostSubscriber $postSubscriber)
    {
        return $user->can('update', $postSubscriber->post) || $postSubscriber->user_id == $user->id;
    }

}
