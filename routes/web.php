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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat', function () {
    return view('chat');
})->middleware('auth');

Route::get('/messages', function (){
    return App\Models\Message::with('user')->get();
})->middleware('auth');

Route::post('/messages', function (){
    // Store the new message
    $user = auth()->user();

    $message = $user->messages()->create([
        'message' => request()->get('message')
    ]);

    // Announce that a new message has been posted
    //event(new \App\Events\MessagePosted($message, $user));
    broadcast(new \App\Events\MessagePosted($message, $user))->toOthers();
    //broadcast(new \App\Events\MessagePosted($message, $user));

    return ['status' => 'OK'];
})->middleware('auth');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
