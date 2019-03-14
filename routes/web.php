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
Route::get('/', 'ChanController@index')->name('home');
Route::resource('chans', 'ChanController');

/**
 * Posts
 * GET		/chans/{chan}/posts				(index)		posts.index
 * GET		/chans/{chan}/posts/create		(create)	posts.create
 * GET		/chans/{chan}/posts/{post}		(show)		posts.show
 * POST		/chans/{chan}/posts				(store)		posts.store
 * GET		/chans/{chan}/posts/{post}/edit	(edit)		posts.edit
 * PATCH	/chans/{chan}/posts/{post}		(update)	posts.update
 * DELETE	/chans/{chan}/posts/{post}		(delete)	posts.delete
 */
Route::get('/chans/{chan}/posts', "PostController@index")->name('posts.index');
Route::get('/chans/{chan}/posts/create', "PostController@create")->name('posts.create');
Route::get('/chans/{chan}/posts/{post}', "PostController@show")->name('posts.show');
Route::post('/chans/{chan}/posts', "PostController@store")->name('posts.store');
Route::get('/chans/{chan}/posts/{post}/edit', "PostController@edit")->name('posts.edit');
Route::patch('/chans/{chan}/posts/{post}', "PostController@update")->name('posts.update');
Route::delete('/chans/{chan}/posts/{post}', "PostController@delete")->name('posts.delete');

