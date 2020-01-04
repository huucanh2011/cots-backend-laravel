<?php

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
// // add any additional headers you need to support here
// header('Access-Control-Allow-Headers: Origin, Content-Type,X-Requested-With,Authorization');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('reset-password/{token}', 'PasswordResetController@showReset');
