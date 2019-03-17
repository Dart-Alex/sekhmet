<?php

/**
 * Config routes, controller App\Bot\BotConfigController
 * GET	/bot/config	(index)		bot.config.index
 * POST	/bot/config	(update)	bot.config.update
 */
Route::get('/bot/config', "BotConfigController@index")->name('bot.config.index');
Route::post('/bot/config', "BotConfigController@update")->name('bot.config.update');

/**
 * Youtube routes, controller App\Bot\YoutubeVideoController
 * GET	/bot/yt/{chan}				(getRandom)			bot.yt.getRandom
 * GET	/bot/yt/{chan}/{name}		(getRandomByUser)	bot.yt.getRandomByUser
 * GET	/bot/yt/count/{chan}		(count)				bot.yt.count
 * GET	/bot/yt/count/{chan}/{name}	(countByUser)		bot.yt.countByUser
 * POST	/bot/yt/search/{chan}		(search)			bot.yt.search
 * POST	/bot/yt/fetch/{chan}		(fetch)				bot.yt.fetch
 */
Route::get('/bot/yt/{chan}', 'YoutubeVideoController@getRandom')->name('bot.yt.getRandom');
Route::get('/bot/yt/{chan}/{name}', 'YoutubeVideoController@getRandomByUser')->name('bot.yt.getRandomByUser');
Route::get('/bot/yt/count/{chan}', 'YoutubeVideoController@count')->name('bot.yt.count');
Route::get('/bot/yt/count/{chan}/{name}', 'YoutubeVideoController@countByUser')->name('bot.yt.countByUser');
Route::post('/bot/yt/search/{chan}', 'YoutubeVideoController@search')->name('bot.yt.countByUser');
Route::post('/bot/yt/fetch/{chan}', 'YoutubeVideoController@fetch')->name('bot.yt.fetch');

