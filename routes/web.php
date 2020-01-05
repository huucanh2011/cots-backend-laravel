<?php

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('reset-password/{token}', 'PasswordResetController@showReset');
