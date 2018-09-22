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

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redis', function () {
    $redis = \Illuminate\Support\Facades\Redis::connection();

    $msn = $redis->hvals('messages');

    dd($msn);
});

Route::middleware('auth')->group(function () {

    Route::get('/chat', function () {
        return view('chat');
    });
    Route::get('/messages', 'MessagesController@index');
    Route::post('/messages', 'MessagesController@store');
    Route::post('/messages/file', 'MessagesController@file');
});

Route::get('/home', 'HomeController@index')->name('home');
