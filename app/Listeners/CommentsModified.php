<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;

class CommentsModified
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
		$comment = $event->comment;
		Cache::forget('comments-'.$comment->post_id);
		return true;
		}

		public function subscribe($events) {
			$events->listen('App\Events\CommentAdded', '\App\Listeners\CommentsModified');
			$events->listen('App\Events\CommentDeleted', '\App\Listeners\CommentsModified');
			$events->listen('App\Events\CommentUpdated', '\App\Listeners\CommentsModified');
		}
}
