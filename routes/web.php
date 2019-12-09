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

Route::group(['middleware' => ['web']], function(){
    // authentication routes
    Route::get('/logout','Auth\LoginController@logout' );
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');


    Route::get('blog/{slug}', ['as'=> 'blog.single', 'uses'=>'Blogcontroller@getSingle'])
    ->where('slug', '[\w\d\-\_]+');
    Route::get('blog', ['uses'=> 'BlogController@getIndex', 'as'=> 'blog.index']);
    Route::get('/', 'PagesController@getIndex');
    Route::get('/about', 'PagesController@getAbout');

    Route::get('/contact', 'PagesController@getContact');
    Route::post('/contact', 'PagesController@postContact');// send contact form email

    Route::resource('posts', 'PostController');
    //tag routes
    Route::resource('tags', 'TagController', ['except' => ['create']]);

    //category routes
    Route::resource('categories', 'CategoryController', ['except' => ['create']]);

    //Comments routes
    Route::post('comments/{post_id}', ['uses'=> 'CommentsController@store', 'as'=> 'comments.store']);
    Route::get('comments/{id}/edit', ['uses'=> 'CommentsController@edit', 'as'=> 'comments.edit']);
    Route::put('comments/{id}/', ['uses'=> 'CommentsController@update', 'as'=> 'comments.update']);

    Route::get('comments/{id}/delete', ['uses'=> 'CommentsController@delete', 'as'=> 'comments.delete']);
    Route::delete('comments/{id}/', ['uses'=> 'CommentsController@destroy', 'as'=> 'comments.destroy']);

});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



