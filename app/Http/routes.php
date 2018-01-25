<?php
Blade::setContentTags('<%', '%>'); // for variables and all things Blade
Blade::setEscapedContentTags('<%%', '%%>');
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

Route::post('auth/login', 'Auth\AuthController@login');
Route::post('auth/signup', 'Auth\AuthController@signup');
Route::get('auth/confirm/{id}', 'Auth\AuthController@confirm');
Route::post('auth/google', 'Auth\SocialController@google');
Route::post('auth/facebook', 'Auth\SocialController@facebook');
Route::post('auth/twitter', 'Auth\SocialController@twitter');
Route::post('auth/linkedin', 'Auth\SocialController@linked');

Route::group(array(/*'before' => 'auth', */'prefix' => 'api/v1'), function() {
    Route::post('reject','api\v1\API_WingsController@reject');
    Route::post('approve','api\v1\API_WingsController@approve');
    Route::post('request','api\v1\API_WingsController@request');
    Route::resource('contacts','api\v1\API_ContactsController');
    Route::resource('organisations','api\v1\API_OrganisationsController');
    Route::resource('deals','api\v1\API_DealsController');
    Route::resource('stages','api\v1\API_StagesController');
    Route::resource('currencies','api\v1\API_CurrenciesController');
    Route::resource('languages','api\v1\API_LanguagesController');
    Route::resource('comments','api\v1\API_CommentsController');
    Route::resource('users','api\v1\API_UsersController');
    Route::resource('useraccount','api\v1\API_UserAccountController');
    Route::post('deal-favorite/{id}', 'api\v1\API_DealFavoriteController@create');
    Route::delete('deal-favorite/{id}', 'api\v1\API_DealFavoriteController@delete');
    Route::post('contact-favorite/{id}', 'api\v1\API_ContactFavoriteController@create');
    Route::delete('contact-favorite/{id}', 'api\v1\API_ContactFavoriteController@delete');
    Route::post('organisation-favorite/{id}', 'api\v1\API_OrganisationFavoriteController@create');
    Route::delete('organisation-favorite/{id}', 'api\v1\API_OrganisationFavoriteController@delete');
    Route::post('delete-contacts','api\v1\API_DeleteContactsController@deleteContacts');
    Route::post('delete-organisations', 'api\v1\API_DeleteOrganisationsController@deleteOrganisations');
    Route::post('send-reset-password-link', 'api\v1\API_ResetPasswordController@sendLink');
    Route::post('reset-password', 'api\v1\API_ResetPasswordController@resetPassword');
    Route::get('download/guide', [
        'uses'  => 'api\v1\API_FileController@getGuide',
        'as'    => 'download.guide'
    ]);
    Route::post('company-from-social', 'Auth\SocialController@addCompanyFromSocial');
});
