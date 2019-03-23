<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\IrcName;
use App\PostSubscriber;

class PostSubscribersCheck
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
        //
	}

	public function ircNameCreated($event)
	{
		$ircName = $event->ircName;
		$postSubscribers = PostSubscriber::where('name', $ircName->name)->get();
		foreach($postSubscribers as $postSubscriber)
		{
			$postSubscriber->user_id = $ircName->user_id;
			$postSubscriber->save();
		}
		return true;
	}

	public function ircNameDeleting($event)
	{
		$ircName = $event->ircName;
		$postSubscribers = PostSubscriber::where('name', $ircName->name)->get();
		$newIrcName = $ircName->user->ircNames->firstWhere('name', '!=', $ircName->name);
		foreach($postSubscribers as $postSubscriber)
		{
			if($newIrcName)
			{
				$postSubscriber->name = $newIrcName->name;
			}
			else {
				$postSubscriber->user_id = null;
			}
			$postSubscriber->save();
		}
		return true;
	}

	public function subscribe($events)
	{
		$events->listen('App\Events\IrcNameCreated', 'App\Listeners\PostSubscribersCheck@ircNameCreated');
		$events->listen('App\Events\IrcNameDeleting', 'App\Listeners\PostSubscribersCheck@ircNameDeleting');
	}
}
