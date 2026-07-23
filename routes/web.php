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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('posts/post', 'HomeController@post')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('event/test',function(){
    return event(new \App\Events\EventDooney('Wellcom to our library'));
});

Route::get('email/test',function(){
    Mail::to ('dooney66@.com')->send(new \App\Mail\Dooney('wellcom from the web route link'));
    return "this is my firest email that i have to send to you";


   

});

Route::get('queue/test',function(){

      $job=( new App\Jobs\sendMailJob)->delay(\Carbon\Carbon::now()->addSeconds(20));
      dispatch( $job);

          return "send queue with job";
});



Route::get('segment/test',function(Illuminate\Support\Facades\Request $request){



        return $request::segment(2);
});














Route::get('books','admin\adminauth@books');
Route::get('delete/user/{id}','adminauth2@Deleteuser');


Route::pattern('id','[0-9]+');
Route::get('all_news', 'adminauth2@all_news_send');
Route::post('all_news', 'adminauth2@all_news_post');
Route::delete('all_news/{id?}','adminauth2@delete');

Route::get('todynews/{id}',function(){
    return 'mohi';
});

Route::get('uploadfile','adminauth2@uploadfileget');
Route::post('uploadfile','adminauth2@uploadfile');
Route::post('storagefile','adminauth2@storagefile');


Route::get('GATE', function() {

    if(Gate::allows('dooneyGate', Auth::guard('admin'))){

        return view('home');



        
    }
    else{
        return 'good by duma';

    }
});
Route::get('/userid{id}','adminauth2@todynews');
Route::post('/comment{id}','adminauth2@comment');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
