<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Comments
 * GET		/api/comments/{post}	(index)		api.comments.index
 * POST		/api/comments/	(store)		api.comments.store
 * PATCH	/api/comments/{comment}	(update)	api.comments.update
 * DELETE	/api/comments/{comment}	(destroy)	api.comments.destroy
 */
Route::get('/api/comments/{post}', "CommentController@index")->name('api.comments.index');
Route::post('/api/comments', "CommentController@store")->name('api.comments.store');
Route::patch('/api/comments/{comment}', 'CommentController@update')->name('api.comments.update');
Route::delete('/api/comments/{comment}', 'CommentController@destroy')->name('api.comments.destroy');
