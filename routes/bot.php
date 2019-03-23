<?php

/**
 * Config routes, controller App\Bot\BotConfigController
 * GET	/bot/config	(index)		bot.config.index
 * POST	/bot/config	(update)	bot.config.update
 */
Route::get('/bot/config', "BotConfigController@index")->name('bot.config.index');
Route::post('/bot/config', "BotConfigController@update")->name('bot.config.update');
Route::get('/bot/config/check', 'BotConfigController@check')->name('bot.config.check');

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
Route::get('/bot/ytcount/{chan}', 'YoutubeVideoController@count')->name('bot.yt.count');
Route::get('/bot/ytcount/{chan}/{name}', 'YoutubeVideoController@countByUser')->name('bot.yt.countByUser');
Route::post('/bot/ytsearch/{chan}', 'YoutubeVideoController@search')->name('bot.yt.countByUser');
Route::post('/bot/ytfetch/{chan}', 'YoutubeVideoController@fetch')->name('bot.yt.fetch');

/**
 * Name verification route
 * GET	/bot/confirm/{name}/{token}	(confirm)	bot.confirm
 */
Route::get('/bot/confirm/{name}/{token}', 'IrcNameController@confirm')->name('bot.confirm');

/**
 * Events routes
 * GET 	/bot/events/{chan}			(show)		bot.events.show
 * GET 	/bot/events/{chan}/list		(list)		bot.events.list
 * POST	/bot/events/{chan}/register	(register)	bot.events.register
 * POST	/bot/events/{chan}/remove	(remove)	bot.events.remove
 */
Route::get('/bot/events/{chan}', 'PostController@show')->name('bot.events.show');
Route::get('/bot/events/{chan}/list', 'PostController@list')->name('bot.events.list');
Route::post('/bot/events/{chan}/register', 'PostController@register')->name('bot.events.register');
Route::post('/bot/events/{chan}/remove', 'PostController@remove')->name('bot.events.remove');
