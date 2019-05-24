<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| author   ahmed Emam
| email    ahmedaboemam123@gmail.com  
*/

             

 


Route::group(['middleware' => ['api_auth'], 'prefix' => 'api'], function() {
 
      
   Route::post('/test', [
		'uses' => 'UserController@test', 
		'as'   => 'test'
	]);

	Route::post('/userSignUp', [
		'uses' => 'UserController@userSignUp', 
		'as'   => 'userSingUp'
	]);

	Route::get('/userSignUp', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'userSingUpGet'
	]);



	Route::post('/userActivation', [
		'uses' => 'UserController@activateUserAccount', 
		'as'   => 'userActivation'
	]);

	Route::get('/userActivation', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'userActivationGet'
	]);

	Route::post('/resendUserActivationCode','UserController@resendActivationCode');

	Route::get('/resendUserActivationCode','UserController@echoEmpty');

 

	Route::post('/userLogin', [
		'uses' => 'UserController@userLogin',
		'as'   => 'userLogin'
	]);

	Route::get('/userLogin', [
		'uses' => 'UserController@echoEmpty',
		'as'   => 'userLoginGet'
	]);

    Route::post('/userForgetPassword', 'UserController@forgetPassword');
	Route::get('/userForgetPassword', 'UserController@echoEmpty');

	Route::post('/userUpdatePassword', 'UserController@updatePassword');
	Route::get('/userUpdatePassword', 'UserController@echoEmpty');

	Route::post('/mainCats', 'UserController@mainCats');
	Route::get('/mainCats', 'UserController@mainCats');

	Route::post('/GetCategoryProviders', 'UserController@get_nearest_providers_inside_main_sub_categories');
	Route::get('/GetCategoryProviders', 'UserController@GetCategoryProviders');

 

                    


	Route::post('/getProviderPage','UserController@prepareProviderPage');

	Route::get('/getProviderPage','UserController@echoEmpty');
 
	Route::post('/getProductDetails','UserController@getProductDetails');

	Route::get('/getProductDetails', 'UserController@echoEmpty');

  
  	Route::post('/productLikeAndDislike','UserController@like_product');

	Route::get('/productLikeAndDislike','UserController@echoEmpty');

	Route::post('/getUserFavorites','UserController@getUserFavorites');

	Route::get('/getUserFavorites','UserController@echoEmpty');
 
	Route::post('/addComment','UserController@addComment');

	Route::get('/addComment','UserController@echoEmpty');

	Route::post('/addAddress', 'UserController@addAdress');

	Route::get('/addAddress','UserController@echoEmpty');

	Route::post('/getUserAddress','UserController@getUserAddresses');

	Route::get('/getUserAddress', 'UserController@echoEmpty');

	Route::post('/deleteUserAddress','UserController@deleteUserAddress');

	Route::get('/deleteUserAddress', 'UserController@echoEmpty');

    Route::post('/editProfile', 'UserController@getProfileData');
    
	Route::get('/editProfile', 'UserController@echoEmpty');

    Route::post('/updateProfile', 'UserController@UpdateProfile');

	Route::get('/editProfile','UserController@echoEmpty');

  
	/*Route::post('/activateUser', [
		'uses' => 'UserController@activateUser', 
		'as'   => 'activateUser'
	]);

	Route::get('/activateUser', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'activateUserGet'
	]);*/

	 


});