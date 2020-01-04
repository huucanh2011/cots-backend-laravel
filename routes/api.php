<?php

// use Illuminate\Http\Request;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
// // add any additional headers you need to support here
// header('Access-Control-Allow-Headers: Origin, Content-Type,X-Requested-With,Authorization');
// header('Access-Control-Allow-Credentials': true)

Route::group(['middleware' => 'cors', 'prefix' => 'v1'], function () {
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
            'image-posts' => 'ImagePostsController'
        ]);

        Route::get('dashboard', 'DashboardController@index');
        
        //filter
        Route::get('filter-tour-orders', 'TourOrdersController@filter');

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
        Route::get('search-tours', 'ToursController@search');
        Route::get('search-tour-orders-active', 'TourOrdersController@searchActive');
        Route::get('search-tour-orders-block', 'TourOrdersController@searchBlock');
        //search
        Route::get('search', 'SearchController@search');

        //top-location
        Route::get('top-location', 'LocationsController@topLocation');
        
        //top-partner
        Route::get('top-partner', 'PartnersController@topPartner');

        //post-home
        Route::get('index-home', 'PostsController@indexHome');

        //comment-home
        Route::get('comment-home', 'CommentsController@getCommentHome');

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

        Route::get('count-tour-order-block', 'TourOrdersController@countTourOrderBlock');

        Route::post('likes', 'LikesController@create');
        Route::get('likes/{id}', 'LikesController@destroy');
    });    
});