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

Route::get('/', 'ChanController@index')->name('home');
/**
 * GET /chans (index)
 * GET /chans/create (create)
 * GET /chans/bla (show)
 * POST /chans (store)
 * GET /chans/bla/edit (edit)
 * PATCH /chans/bla (update)
 * DELETE /projects/1 (destroy)
 */
Route::resource('chans', 'ChanController');

