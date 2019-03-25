<?php

namespace App\Bot\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Chan;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\IrcName;
use phpDocumentor\Reflection\Types\Boolean;
use App\ChanUser;

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
		foreach (User::where('admin', '1')->get() as $user) {
			foreach ($user->ircNames as $ircName) {
				$owners[] = strtolower($ircName->name);
			}
		}
		$chans = [];
		foreach (Chan::all() as $chan) {
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
		$last_update = Cache::get('bot-config-last-update', function () {
			$now = Carbon::now();
			Cache::put('bot-config-last-update', $now);
			return $now;
		});
		return [
			'lastUpdate' => $last_update,
			'owners' => $owners,
			'realname' => env('BOT_NAME', config('app.name', 'Sekhmet')),
			'myname' => env('BOT_NAME', config('app.name', 'Sekhmet')),
			'debug' => env('BOT_DEBUG', false),
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
		$command = $this->validate($request, [
			"command" => "required|string",
		])['command'];
		$source = $this->validate($request, [
			"source" => "required|exists:irc_names,name"
		])['source'];
		$sourceUser = IrcName::where('name', $source)->first()->user;
		if(in_array($command, ['owner'])) { // Commandes owner
			if(!$sourceUser->isAdmin()) return [
				'error' => true,
				'message' => "Vous n'êtes pas owner."
			];
		}
		if(in_array($command, ['youtube', 'spam', 'event', 'admin', 'badwords'])) { // Commandes admin
			$target = $this->validate($request, [
				'target' => 'required|exists:chans,name'
			])['target'];
			$chan = Chan::where('name', $target)->first();
			if(!($chan->isAdmin($sourceUser) || $sourceUser->isAdmin())) return [
				'error' => true,
				'message' => "Vous n'êtes pas admin sur #$chan->name."
			];
		}
		if(in_array($command, ['youtube', 'spam', 'event'])) {
			$subCommand = $this->validate($request, [
				'subCommand' => [
					'required',
					Rule::in(['timer', 'active'])
				]
			])['subCommand'];
			$field = "config_".$command."_".$subCommand;
			switch($subCommand) {
				case 'timer':
					$data = (int) $this->validate($request, [
						'data' => 'required|numeric'
					])['data'];
					$chan->$field = $data;
					$chan->save();
					return [
						'error' => false,
						'message' => "Le timer $command pour #$chan->name est maintenant de $data secondes. (Jusqu'à une minute peut être nécessaire pour l'actualisation des paramètres)"
					];
					break;
				case 'active':
					$data = $this->validate($request, [
						'data' => 'required'
					])['data'];
					$data = ($data == "True");
					$chan->$field = $data;
					$chan->save();
					return [
						'error' => false,
						'message' => "Le module $command est maintenant ".($chan->$field?'actif':'inactif')." pour #$chan->name. (Jusqu'à une minute peut être nécessaire pour l'actualisation des paramètres)"
					];
					break;
			}
		}
		if(in_array($command, ['owner', 'admin', 'badwords'])) {
			$subCommand = $this->validate($request, [
				'subCommand' => [
					'required',
					Rule::in(['add', 'remove'])
				]
			])['subCommand'];
			if(in_array($command, ['owner', 'admin']))
			{
				$data = strtolower($this->validate($request, [
					'data' => 'required|exists:irc_names,name'
				])['data']);
				$targetUser = IrcName::where('name', $data)->first()->user;
			}
			else
			{
				$data = strtolower($this->validate($request, [
					'data' => 'required|string'
				]));
			}
			switch($command) {
				case "owner":
					if($subCommand == 'add') {
						$targetUser->admin = true;
						$message = 'est maintenant';
					}
					else {
						$targetUser->admin = false;
						$message = "n'est plus";
					}
					$targetUser->save();
					return [
						'error' => false,
						'message' => "$targetUser->name($data) $message owner. (Jusqu'à une minute peut être nécessaire pour l'actualisation des paramètres)"
					];
					break;
				case "admin":
					if(!$chanUser = ChanUser::where('chan_id', $chan->id)->where('user_id', $targetUser->id)->first()) {
						$chanUser = ChanUser::create([
							'chan_id' => $chan->id,
							'user_id' => $targetUser->id
						]);
					}
					if($subCommand == 'add') {
						$chanUser->admin = true;
						$message = 'est maintenant';
					}
					else {
						$chanUser->admin = false;
						$message = "n'est plus";
					}
					$chanUser->save();
					return [
						'error' => false,
						'message' => "$targetUser->name($data) $message admin sur #$chan->name. (Jusqu'à une minute peut être nécessaire pour l'actualisation des paramètres)"
					];
					break;
				case "badwords":
					if($subCommand == 'add') {
						if(in_array($data, $chan->badwords)) {
							return [
								'error' => true,
								'message' => "$data déjà badword sur #$chan->name."
							];
						}
						else
						{
							array_push($chan->config_badwords, $data);
							$chan->save();
							return [
								'error' => false,
								'message' => "$data ajouté aux badwords de #$chan->name"
							];
						}
					}
					else
					{
						if(!in_array($data, $chan->badwords)) {
							return [
								'error' => true,
								'message' => "$data n'est pas dans les badwords de #$chan->name."
							];
						}
						else {
							array_pull($chan->config_badwords, $data);
							$chan->save();
							return [
								'error' => false,
								'message' => "$data retiré des badwords de #$chan->name."
							];
						}
					}
					break;
			}
		}
		return [
			'error' => true,
			'message' => "Commande $command n'existe pas."
		];
	}

	public function check()
	{
		$last_update = Cache::get('bot-config-last-update', function () {
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
