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

Route::get('/', 'IndexController@index');
Route::get('/stats/all', 'IndexController@allStats');
Route::get('/stats/cf', 'IndexController@cloudFoundryStats');
Route::get('/stats/travis', 'IndexController@travisStats');
Route::get('/stats/github', 'IndexController@gitHubStats');
Route::get('/stats/build_versions', 'IndexController@buildVersionStats');
Route::get('/stats/uptime', 'IndexController@uptimeStats');
Route::get('/stats/tests', 'IndexController@testStats');
