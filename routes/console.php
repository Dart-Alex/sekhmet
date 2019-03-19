<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use App\YoutubeVideo;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('importYoutube', function () {
	$path = base_path('bot/youtube.json');
	$content = json_decode(file_get_contents($path), true);
	$count = 0;
	$yids = [];
	foreach($content as $chan => $videos) {
		$chanName = strtolower(str_replace(' ', '', str_replace('#', '', $chan)));
		$yids[$chanName] = [];
		$count += count($videos);
	}
	$bar = $this->output->createProgressBar($count);
	$bar->start();
	foreach($content as $chan => $videos) {
		$chanName = strtolower(str_replace('#', '', $chan));
		foreach($videos as $yid => $video) {
			if(!in_array($yid, $yids[$chanName])) {
				$yids[$chanName][] = $yid;
				$date = Carbon::createFromTimestamp($video['timestamp']);
				YoutubeVideo::create([
					'chan_name' => $chanName,
					'name' => $video['nick'],
					'created_at' => $date,
					'yid' => $yid
				]);
			}
			$bar->advance();

		}
	}
	$bar->finish();
	$this->info('Les entrées ont été importées');
})->describe('Imports youtube json to database');

Artisan::command('bot:start', function () {
	$process = new Process(['python3',base_path('bot/sekhmet.py'),env('BOT_URL'),'>',base_path('storage/bot.log')]);
	$process->setTimeout(null);
	$process->setIdleTimeout(null);
	$process->start();
	Cache::put('bot-process-pid', $process->getPid());
	$this->info('Bot started (PID:'+$process->getPid()+')');
});

Artisan::command('bot:stop', function() {
	$processPid = Cache::get('bot-process-pid');
	$process = new Process(['kill', '-SIGKILL', $processPid]);
	$process->run();
	$this->info('Bot stopped (PID:'+$processPid+')');
});
