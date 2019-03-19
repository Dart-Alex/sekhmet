<?php

namespace App\Bot\Controllers;

use App\BotConfig;
use Illuminate\Http\Request;
use App\User;
use App\Chan;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BotConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$owners = [];
        foreach(User::where('admin', '1')->get() as $user) {
			foreach($user->ircNames as $ircName) {
				$owners[] = strtolower($ircName->name);
			}
		}
		$chans = [];
		foreach(Chan::all() as $chan) {
			$chans[$chan->name] = $chan->getConfig();
		}
		// $realname = Cache::get('botconfig-realname', function () {
		// 	Cache::put('botconfig-realname', config('app.name', 'Sekhmet'));
		// 	return config('app.name', 'Sekhmet');
		// });
		// $myname = Cache::get('botconfig-myname', function () {
		// 	Cache::put('botconfig-realname', config('app.name', 'Sekhmet'));
		// 	return config('app.name', 'Sekhmet');
		// });
		// $server = Cache::get('botconfig-server', function () {
		// 	Cache::put('botconfig-server', [
		// 		'password' => env('BOT_PASSWORD'),
		// 		'port' => env('BOT_PORT'),
		// 		'address' => env('BOT_ADDRESS')
		// 	]);
		// 	return [
		// 		'password' => env('BOT_PASSWORD'),
		// 		'port' => env('BOT_PORT'),
		// 		'address' => env('BOT_ADDRESS')
		// 	];
		// });
		$last_update = Cache::get('bot-config-last-update', function() {
			$now = Carbon::now();
			Cache::put('bot-config-last-update', $now);
			return $now;
		});
		return [
			'lastUpdate' => $last_update,
			'owners' => $owners,
			'realname' => env('BOT_NAME', config('app.name', 'Sekhmet')),
			'myname' => env('BOT_NAME', config('app.name', 'Sekhmet')),
			'server' => [
				'password' => env('BOT_PASSWORD'),
				'port' => env('BOT_PORT'),
				'address' => env('BOT_ADDRESS')
			],
			'chans' => $chans
		];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BotConfig  $botConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
	}

	public function check() {
		$last_update = Cache::get('bot-config-last-update', function() {
			$now = Carbon::now();
			Cache::put('bot-config-last-update', $now);
			return $now;
		});
		return [
			'lastUpdate' => $last_update,
			'error' => false
		];
	}
}
