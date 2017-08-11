<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: accept,content-type,x-xsrf-token');
header('Content-Type: application/json');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::post('author/create',  'AuthorController@createAuthor');
Route::post('article/create', 'ArticleController@createArticle');
Route::get('article/getall', 'ArticleController@getAllArticle');
Route::post('article/update', 'ArticleController@updateArticle');
Route::get('article/delete/{id}', 'ArticleController@deleteArticle');

Route::get('/', function () {
    return view('welcome');
});
