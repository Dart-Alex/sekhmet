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
	$bar = $this->output->createProgressBar($count*2);
	$bar->start();
	$videoCollection = collect([]);
	foreach($content as $chan => $videos) {
		$chanName = strtolower(str_replace('#', '', $chan));
		foreach($videos as $yid => $video) {
			if(!in_array($yid, $yids[$chanName])) {
				$yids[$chanName][] = $yid;
				$date = Carbon::createFromTimestamp($video['timestamp']);
				// YoutubeVideo::create([
				// 	'chan_name' => $chanName,
				// 	'name' => $video['nick'],
				// 	'created_at' => $date,
				// 	'yid' => $yid
				// ]);
				$videoCollection->push([
						'chan_name' => $chanName,
						'name' => $video['nick'],
						'created_at' => $date,
						'yid' => $yid
					]);
			}
			$bar->advance();

		}
	}

	foreach($videoCollection->sortBy('created_at') as $video) {
		YoutubeVideo::create($video);
		$bar->advance();
	}
	$bar->finish();
	$this->info('Les entrées ont été importées');
})->describe('Imports youtube json to database');

Artisan::command('cacheYoutube', function() {
	$youtubeVideos = YoutubeVideo::all();
	$bar = $this->output->createProgressBar($youtubeVideos->count()*2);
	$bar->start();
	foreach($youtubeVideos as $youtubeVideo) {
		if(!$youtubeVideo->getInfo()) {
			$youtubeVideo->delete();
		}
		$bar->advance();
	}
	$youtubeVideos = YoutubeVideo::all();
	foreach($youtubeVideos as $youtubeVideo) {
		$youtubeVideo->getIndex();
		$bar->advance();
	}
	$bar->finish();
	$this->info('Cache set');
});

Artisan::command('bot:start', function () {
	if(!Cache::has('bot-process-pgid')) {
		$pid = exec('python3 '.base_path('bot/sekhmet.py').' '.env('BOT_URL').' > '.base_path('storage/logs/bot.log').' 2>&1 & echo $!; ');
		$pgid = exec('ps -o pgid= '.$pid);
		Cache::put('bot-process-pgid', $pgid);
		$this->info("Bot started (PID:$pid,PGID:$pgid)");
	}
	else $this->info('Bot already started');
});

Artisan::command('bot:stop', function() {
	if(Cache::has('bot-process-pgid')) {
		$pgid = Cache::pull('bot-process-pgid');
		exec('pkill -9 -g '.$pgid);
		$this->info('Bot terminated (PGID:'.$pgid.')');
	}
	else $this->info('No bot pid found');
});

Artisan::command('bot:restart', function() {
	if(Cache::has('bot-process-pgid')) {
		Artisan::call('bot:stop');
		Artisan::call('bot:start');
		$this->info('Bot restarted (PGID:'.Cache::get('bot-process-pgid').')');
	} else $this->info('Bot not started');
});
