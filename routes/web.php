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

Route::get('/', 'InputController@index');
// Route::get('process', 'InputController@process');
Route::get('process', ['as' => 'process',  'uses' => 'InputController@process']);
Route::post('process', ['as' => 'process',  'uses' => 'InputController@process']);

