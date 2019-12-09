<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function () {
    Route::group(['namespace' => 'Api', 'prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login'); 
        Route::post('register', 'AuthController@register');
        Route::get('logout', 'AuthController@logout');
        Route::put('updatedetails', 'AuthController@updateDetails');
        Route::post('updateavatar', 'AuthController@updateAvatar');
        Route::get('me', 'AuthController@me');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('forgotpassword', 'AuthController@forgotpassword');
        Route::put('resetpassword/{token}', 'AuthController@resetpassword');
    });
    
    Route::group(['namespace' => 'Api'], function () {
        Route::apiResources([
            'roles' => 'RolesController',
            'users' => 'UsersController',
            'decentralizations' => 'DecentralizationController',
            'partners' => 'PartnersController',
            'posts' => 'PostsController',
            'comments' => 'CommentsController',
            'tours' => 'ToursController',
            'tour-orders' => 'TourOrdersController',
            'locations' => 'LocationsController',
            'ratings' => 'RatingsController',
            'image-locations' => 'ImageLocationsController',
            'image-posts' => 'ImagePostsController',
            'image-tours' => 'ImageToursController'
        ]);
        //block user
        Route::post('block-user', 'DecentralizationController@block');

        //block post
        Route::post('block-post', 'PostsController@block');

        //block-tour
        Route::post('block-tour', 'ToursController@blockTour');

        //active tour order
        Route::post('active-tourorder', 'TourOrdersController@active');

        //search
        Route::get('search-roles', 'RolesController@search');
        Route::get('search-users', 'UsersController@search');
        Route::get('search-partners', 'PartnersController@search');
        Route::get('search-locations', 'LocationsController@search');
        Route::get('search-posts', 'PostsController@search');
        Route::get('search-decentralizations', 'DecentralizationController@search');
        //search
        Route::get('search', 'SearchController@search');

        //top-location
        Route::get('top-location', 'LocationsController@topLocation');
        
        //top-partner
        Route::get('top-partner', 'PartnersController@topPartner');

        //post-home
        Route::get('post-home', 'PostsController@postHome');

        //tour-cate
        Route::get('tour-category', 'TourCatesController@index');

        //date-departure-crud
        Route::get('date-departure/{id}', 'ToursController@getDate');
        Route::post('date-departure', 'ToursController@postDate');
        Route::delete('d-date-departure/{id}', 'ToursController@deleteDate');

        //index-tours-partner
        Route::get('tour-partner', 'ToursController@indexPartner');

        //count partner block
        Route::get('count-partner-block', 'PartnersController@countBlock');

        Route::post('likes', 'LikesController@create');
        Route::get('likes/{id}', 'LikesController@destroy');
    });    
});