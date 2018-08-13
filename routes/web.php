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
use App\User;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::group([
	'middleware' => 'auth:api'
],
function () {
	Route::get('/huy', function () {
		echo "string";
	});
}
);
Route::get('/home', 'HomeController@index')->name('home');

Route::get('list-user', function(){
	return User::all();
});

Route::get('category/list', 'CategoryController@list');
Route::get('category/all', 'CategoryController@index');
Route::get('category/getone/{cate_id}', 'CategoryController@getOneCate');

Route::post('post/list', 'PostController@index');
Route::get('post/all/{limit?}', 'PostController@getAll');
Route::get('post/find/{id}', 'PostController@findById');
Route::post('post/save', 'PostController@save');

Route::get('post_category/{cate_id}/{limit?}', 'PostController@getPostCate');
Route::get('post/hot/{limit?}', 'PostController@getHotPost');
Route::get('post/same_cate/{post_id?}', 'PostController@getPostSameCate');

Route::get('search/{keyword?}', 'PostController@search');