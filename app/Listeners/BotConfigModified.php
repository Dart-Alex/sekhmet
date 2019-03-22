<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\ChanUser;

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

	public function chanUserDeleting($event)
	{
		if($event->chanUser->admin) {
			Cache::put('bot-config-last-update', Carbon::now());
		}
		return true;
	}

	public function userDeleting($event)
	{
		if($event->user->isAdmin() || ChanUser::where('user_id', $event->user->id)->where('admin', true)->exists()) {
			Cache::put('bot-config-last-update', Carbon::now());
		}
		return true;
	}

	public function subscribe($events)
	{
		$events->listen('App\Events\ChanAdded', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\ChanDeleted', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\ChanUpdated', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\UserAdminSet', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\UserDeleting', 'App\Listeners\BotConfigModified@userDeleting');
		$events->listen('App\Events\ChanUserAdminSet', 'App\Listeners\BotConfigModified');
		$events->listen('App\Events\ChanUserDeleting', 'App\Listeners\BotConfigModified@chanUserDeleting');
	}
}
