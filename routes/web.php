<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');
/**
 * Chans
 * GET		/chans				(index)		chans.index
 * GET		/chans/create		(create)	chans.create
 * GET		/chans/{chan}		(show)		chans.show
 * POST		/chans 				(store)		chans.store
 * GET		/chans/{chan}/edit	(edit)		chans.edit
 * PATCH	/chans/{chan}		(update)	chans.update
 * DELETE	/chans/{chan}		(destroy)	chans.destroy
 */
Route::resource('chans', 'ChanController');

/**
 * Events
 * GET		/chans/{chan}/events				(index)		posts.index
 * GET		/chans/{chan}/events/create			(create)	posts.create
 * GET		/chans/{chan}/events/{post}			(show)		posts.show
 * POST		/chans/{chan}/events				(store)		posts.store
 * GET		/chans/{chan}/events/{post}/edit	(edit)		posts.edit
 * PATCH	/chans/{chan}/events/{post}			(update)	posts.update
 * DELETE	/chans/{chan}/events/{post}			(destroy)	posts.destroy
 */
Route::get('/chans/{chan}/events', "PostController@index")->name('posts.index');
Route::get('/chans/{chan}/events/create', "PostController@create")->name('posts.create');
Route::get('/chans/{chan}/events/{post}', "PostController@show")->name('posts.show');
Route::post('/chans/{chan}/events', "PostController@store")->name('posts.store');
Route::get('/chans/{chan}/events/{post}/edit', "PostController@edit")->name('posts.edit');
Route::patch('/chans/{chan}/events/{post}', "PostController@update")->name('posts.update');
Route::delete('/chans/{chan}/events/{post}', "PostController@destroy")->name('posts.destroy');

/**
 * Event subscribers
 * POST		/chans/{chan}/events/{post}/subscribers						(store)		postSubscribers.store
 * DELETE	/chans/{chan}/events/{post}/subscribers/{postSubscriber}	(destroy)	postSubscribers.destroy
 */
Route::post('/chans/{chan}/events/{post}/subscribers', 'PostSubscriberController@store')->name('postSubscribers.store');
Route::delete('/chans/{chan}/events/{post}/subscribers/{postSubscriber}', 'PostSubscriberController@destroy')->name('postSubscribers.destroy');

/**
 * Users
 * GET		/profile			(edit)		profile
 * GET 		/users				(index)		users.index
 * GET 		/users/{user}/edit	(edit)		users.edit
 * GET 		/users/{user}		(show)		users.show
 * PATCH	/users/{user}		(update)	users.update
 * DELETE	/users/{user}		(destroy)	users.destroy
 */
Route::get('/profile/{user}', 'UserController@edit')->name('profile');
Route::get('/users', 'UserController@index')->name('users.index');
Route::get('/users/{user}/edit', 'UserController@edit')->name('users.edit');
Route::get('/users/{user}', 'UserController@show')->name('users.show');
Route::patch('/users/{user}', 'UserController@update')->name('users.update');
Route::delete('/users/{user}', 'UserController@destroy')->name('users.destroy');

/**
 * IrcNames
 * POST		/ircNames			(store)		ircNames.store
 * DELETE	/ircNames/{ircName}	(destroy)	ircNames.destroy
 */
Route::post('/ircNames', 'IrcNameController@store')->name('ircNames.store');
Route::delete('/ircNames/{ircName}', 'IrcNameController@destroy')->name('ircNames.destroy');


/**
 * ChanUsers
 * POST		/chanUsers					(store)		chanUsers.store
 * PATCH	/chanUsers/{chanUser}		(update)	chanUsers.update
 * DELETE	/ChanUsers/{chanUser}		(destroy)	chanUsers.destroy
 */
Route::post('/chanUsers/{chan}', 'ChanUserController@store')->name('chanUsers.store');
Route::patch('/chanUsers/{chan}/{chanUser}', 'ChanUserController@update')->name('chanUsers.update');
Route::delete('/chanUsers/{chan}/{chanUser}', 'ChanUserController@destroy')->name('chanUsers.destroy');

/**
 * Youtube videos
 * GET 	/chans/{chan}/youtube			(index)		youtubeVideos.index
 * GET 	/chans/{chan}/youtube/{name}	(indexName)	youtubeVideos.indexName
 * POST	/chans/{chan}/youtube/search	(search)	youtubeVideos.search
 */
Route::get('/chans/{chan}/youtube', 'YoutubeVideoController@index')->name('youtubeVideos.index');
Route::get('/chans/{chan}/youtube/{name}', 'YoutubeVideoController@indexName')->name('youtubeVideos.indexName');
Route::post('/chans/{chan}/youtube/search', 'YoutubeVideoController@search')->name('youtubeVideos.search');

