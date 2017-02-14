<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::get('socket', 'socketController@index');
Route::post('sendmessage', 'socketController@sendMessage');
Route::get('writemessage', 'socketController@writemessage');

/** Support Routes */
Route::group(['middleware' => 'web'], function () {
    Route::auth();
    
    Route::get('/social/redirect/{provider}', [
        'as' => 'social.redirect',
        'uses' => 'Auth\AuthController@getSocialRedirect'
    ]);

    Route::get('/social/handle/{provider}', [
        'as' => 'social.handle',
        'uses' => 'Auth\AuthController@getSocialHandle'
    ]);

    /** Users */
    Route::post('/user/create/', 'UsersController@register');

    /** Access to States and Cities from different places of code */
    Route::post('/get/states/', 'StatesController@statesByCountry');
    Route::post('/get/cities/', 'CityController@getCityByState');

});
/**
 * Ajax Handlers
 * todo: change all data getters from _POST to _GET
 */
Route::group([
    'middleware' => ['web', 'auth', 'roles'],
    'roles' => ['Male', 'Female'],
], function(){
    Route::post('expense', 'ExpenseController@handler');
    Route::post('horoscope', 'HoroscopeController@handler');

    /**
     * Contacts
     */
    Route::post('flp', 'FLController@getFlp');
    Route::post('fle', 'FLController@getFle');
});


Route::group([  'prefix'        => LaravelLocalization::setLocale(),
                'middleware'    => ['web', 'auth', 'roles', /*'localeSessionRedirect', 'localizationRedirect'*/],
                'roles'         => ['Alien', 'Male', 'Female']
], function(){
    Route::get('chat', 'ChatController@index');

    Route::post('chat/ajax', 'ChatController@ajax');
    /** Users profile */
    Route::get('profile/message', 'MessagesController@index');
    Route::get('profile/{id}', 'UsersController@edit');
    Route::get('profile/{id}/photo', 'UsersController@profilePhoto');
    Route::get('profile/{id}/video', 'UsersController@profileVideo');
    Route::get('profile/{id}/mail', 'UsersController@profileMail');
    Route::get('profile/{id}/smiles', 'UsersController@profileSmiles');
    Route::get('profile/{id}/gifts', 'UsersController@profileGifts');
    Route::get('profile/{id}/finance', 'UsersController@profileFinance');


    Route::get('profile/album/add', 'AlbumController@create');
    Route::get('profile/{id}/gallery/{aid}', 'AlbumController@show');

    Route::post('profile/album/add', 'AlbumController@make');
    //todo edit album

    Route::post('profile/message', 'MessagesController@send');
    Route::post('profile/update/{id}', 'UsersController@update');

    Route::post('remove/album', 'AlbumController@drop');
    Route::post('remove/image', 'AlbumController@dropImage');


    /** Videos */
    Route::get('profile/{id}/video/add', 'VideoController@create');

    Route::post('wink', 'SmilesController@sendSmile');
    Route::get('wink', 'SmilesController@getSmileFromUser');
});


/** Admin route group */
Route::group([  'prefix' => LaravelLocalization::setLocale().'/admin',
                'middleware' => ['web', 'auth', 'roles', /*'localeSessionRedirect', 'localizationRedirect'*/],
                'roles' => ['Owner', 'Moder', 'Partner']
], function(){

    Route::get('/', function(){
        return redirect('admin/dashboard');
    });

    Route::get('dashboard', 'Admin\AdminController@dashboard');
    Route::get('profile', 'Admin\AdminController@profile'); //end

    Route::post('profile', 'Admin\AdminController@profile_update');

    /** Start Blog Routing */
    Route::get('blog', 'Admin\BlogController@index');
    Route::get('blog/new', 'Admin\BlogController@create');

    Route::get('blog/edit/{id}','Admin\BlogController@edit');
    Route::get('blog/drop/{id}', 'Admin\BlogController@destroy');

    Route::post('blog/new', 'Admin\BlogController@store');
    Route::post('blog/update', 'Admin\BlogController@update');
    /** Stop Blog Routing */

    /** Start Mailer sender delivery */
    Route::get('sender', 'Admin\MessageSenderController@index');
    Route::get('sender/new/{girl_id}', 'Admin\MessageSenderController@create');
    Route::get('sender/edit/{id}','Admin\MessageSenderController@edit');
    Route::get('sender/drop/{id}', 'Admin\MessageSenderController@destroy');
    Route::get('sender/send/{id}', 'Admin\MessageSenderController@send');

    Route::post('sender', 'Admin\MessageSenderController@index');
    Route::post('sender/store', 'Admin\MessageSenderController@store');
    Route::post('sender/update', 'Admin\MessageSenderController@update');
    Route::post('sender/ajax', 'Admin\MessageSenderController@ajax');
    /** End Mailer sender delivery */

    /** Start Partners Profile routing */
    Route::get('partners', 'Admin\PartnerController@index');
    Route::get('partner/new', 'Admin\PartnerController@create');
    Route::get('partner/show/{id}', 'Admin\PartnerController@show');
    Route::get('partner/edit/{id}', 'Admin\PartnerController@edit');
    Route::get('partner/drop/{id}', 'Admin\PartnerController@destroy');

    Route::post('partner/store', 'Admin\PartnerController@store');
    Route::post('partner/edit/{id}', 'Admin\PartnerController@update');
    /** End partners profile routing */

    /** Start Moderator Profile routing */
    Route::get('moderators', 'Admin\ModeratorController@index');
    Route::get('moderator/new', 'Admin\ModeratorController@create');
    Route::get('moderator/show/{id}', 'Admin\ModeratorController@show');
    Route::get('moderator/edit/{id}', 'Admin\ModeratorController@edit');
    Route::get('moderator/drop/{id}', 'Admin\ModeratorController@destroy');

    Route::post('moderator/store', 'Admin\ModeratorController@store');
    Route::post('moderator/edit/{id}', 'Admin\ModeratorController@update');
    /** End Moderator Profile routing */

    /** Start Girls Profile routing */
    Route::get('girls', 'Admin\GirlsController@index'); //All
    Route::get('girl/new', 'Admin\GirlsController@create'); //Add new

    Route::get('girls/{status}', 'Admin\GirlsController@getByStatus'); //Return all by status

    Route::get('girl/edit/{id}', 'Admin\GirlsController@edit'); // Edit Girl profile
    Route::get('girl/edit/{id}/add_album', 'Admin\GirlsController@createAlbum'); // Create Girl albums
    Route::get('girl/edit/{id}/edit_album/{aid}', 'Admin\GirlsController@editAlbum'); // Edit Girl albums

    //dropImageAlbum

    Route::get('girl/show/{id}', 'Admin\GirlsController@show'); // Show Girl profile
    Route::get('girl/drop/{id}', 'Admin\GirlsController@destroy');//

    Route::post('girl/edit/{id}/add_album', 'Admin\GirlsController@addAlbum'); // Create Girl save albums
    Route::post('girl/edit/{id}/edit_album/{aid}', 'Admin\GirlsController@saveAlbume'); // Save editing Girl albums
    Route::post('girl/dropImageAlbum/{aid}', 'Admin\GirlsController@dropImageAlbum'); // Delete photo from Girl albums
    Route::post('girl/dropProfileFoto/{fid}', 'Admin\GirlsController@dropProfileFoto'); // Delete photo from Girl albums
    //showAlbum
    Route::post('girl/deleteAlbum/{albumID}', 'Admin\GirlsController@deleteAlbum'); // Edit Girl save albums
    //    //admin/girl/deleteAlbum/3
    Route::post('girl/check', ['as' => 'check_pass', 'uses' => 'Admin\GirlsController@check']); // Check passport at DB
    Route::post('girl/store', 'Admin\GirlsController@store'); //Store new to db
    Route::post('girl/edit/{id}','Admin\GirlsController@update');// Update db
    Route::post('girl/changeStatus', 'Admin\GirlsController@changeStatus'); //change girlStatus from edit profile page
    Route::post('girl/changepartner', 'Admin\GirlsController@changePartner'); //change girlStatus from edit profile page
    /** End Girls Profile routing */


    /** Start Gifts  */
    Route::get('gifts/', 'Admin\GiftsController@index');
    Route::get('gifts/new', 'Admin\GiftsController@create');
    Route::get('gifts/edit/{id}', 'Admin\GiftsController@edit');
    Route::get('gifts/drop/{id}', 'Admin\GiftsController@drop');

    Route::post('gifts/store', 'Admin\GiftsController@store');
    Route::post('gifts/update/{id}', 'Admin\GiftsController@update');

    Route::post('gifts/check_lang', ['as' => 'check_lang', 'uses' => 'Admin\GiftsController@check_language']);
    Route::post('gifts/save_present_translation', ['as' => 'save_present_translation', 'uses' => 'Admin\GiftsController@save_present_translation']);
    /** End gifts */

    /** Ticket System Routes */
    Route::get('support', 'Admin\TicketController@index');
    Route::get('support/new', 'Admin\TicketController@newTicket');
    Route::get('support/show/{ticket_id}', 'Admin\TicketController@show'); // show one ticket

    Route::get('support/answered', 'Admin\TicketController@answered');
    Route::get('support/closed', 'Admin\TicketController@closed');

    Route::post('support', 'Admin\TicketController@create'); //create new ticket
    Route::post('support/show/{ticket_id}', 'Admin\TicketController@answer');
    Route::post('support/close/{ticket_id}', 'Admin\TicketController@close');
    Route::post('support/{ticket_id}', 'Admin\TicketController@answer'); //add new answer to ticket
    /** End ticket System Routes */

    /** Finance */
    Route::get('finance', 'Admin\FinanceController@index');
    Route::get('finance/control', 'Admin\FinanceController@control');
    Route::get('finance/stat', 'Admin\FinanceController@stat');

    Route::post('finance/{id}','Admin\FinanceController@saveData');
    /* End finance */

    /** Pages */
    Route::get('pages', 'Admin\PagesController@index');
    Route::get('pages/add', 'Admin\PagesController@create');
    Route::get('pages/edit/{id}', 'Admin\PagesController@edit');
    Route::get('pages/drop/{id}', 'Admin\PagesController@destroy');

    Route::post('pages/add', 'Admin\PagesController@store');
    Route::post('pages/edit/{id}', 'Admin\PagesController@update');
    Route::post('pages/dropFile', 'Admin\PagesController@dropFile');
    /** End pages */

    /** Horoscope */
    Route::get('horoscope', 'Admin\HoroscopeController@index');
    Route::get('horoscope/add', 'Admin\HoroscopeController@create');
    Route::get('horoscope/edit/{id}', 'Admin\HoroscopeController@edit');

    Route::post('horoscope/add', 'Admin\HoroscopeController@store');
    Route::post('horoscope/edit/{id}', 'Admin\HoroscopeController@update');
    /** End horoscope */

});



/**
 *  Free Routes
 */

Route::group([ 'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['web', 'localeSessionRedirect', 'localizationRedirect'],
], function(){
    Route::get('/','HomeController@index');
    Route::get('contacts', 'ContactsController@show');
    Route::get('contacts/tickets', 'TicketController@index');
    Route::get('contacts/tickets/close/{id}', 'TicketController@closeTicket');
    Route::post('contacts/tickets/create', 'TicketController@createTicket');
    Route::post('contacts/tickets/reply/{id}', 'TicketController@createReply');
    Route::post('contacts/message', 'ContactsController@sendMessage');


    Route::get('blog', 'BlogController@all');
    Route::get('blog/{id}', 'BlogController@post');

    Route::get('search', 'SearchController@index');
    Route::post('search', 'SearchController@search');

    Route::get('users/online', 'UsersController@online');

    Route::get('profile/show/{id}', 'UsersController@show');

    /** Payments */
    Route::get('pricing', 'PaymentsController@addBalance');
    Route::get('payments/checkout', 'PaymentsController@checkOut'); //@todo payments gateway

    /** Pages */
    Route::get('{slug}', 'PagesController@show');
});

Route::post('sendmessage', 'ChatController@sendMessage');
