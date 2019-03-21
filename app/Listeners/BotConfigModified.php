<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BotConfigModified
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
		Cache::put('bot-config-last-update', Carbon::now());
		return true;
	}

	public function subscribe($events)
	{
		$events->listen('App\Events\ChanAdded', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\ChanDeleted', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\ChanUpdated', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\UserAdminSet', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\ChanUserAdminSet', 'App\Listeners\BotConfigModified');
	}
}
