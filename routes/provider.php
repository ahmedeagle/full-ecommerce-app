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

 



Route::post('ahmed','ProviderController@test');
 

Route::get('/notfound', function(){
	return view('cpanel.404');
})->name('404');

Route::get('/', function (){
    return view('welcome');
});



Route::group(['middleware' => ['api_auth'], 'prefix' => 'api'], function() {
	
	Route::post('/getCats',[
		'uses' => 'UserController@getCategories',
		'as'   => 'getCats'
	]);

	Route::get('/getCats',[
		'uses' => 'UserController@echoEmpty',
		'as'   => 'getCatsGet'
	]);

	 
	


	Route::post('/addOrder',[
		'uses' => 'UserController@addOrder',
		'as'   => 'addOrder'
	]);

	Route::get('/addOrder',[
		'uses' => 'UserController@echoEmpty',
		'as'   => 'addOrderGet'
	]);

	Route::post('/addVisitorOrder',[
		'uses' => 'UserController@addVisitorOrder',
		'as'   => 'addVisitorOrder'
	]);

	Route::get('/addVisitorOrder',[
		'uses' => 'UserController@echoEmpty',
		'as'   => 'addVisitorOrderGet'
	]);

	Route::post('/orders', [
		'uses' => 'UserController@getUserOrders', 
		'as'   => 'orders'
	]);

	Route::get('/orders', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'ordersGet'
	]);
  

	Route::post('/addAddress', [
		'uses' => 'UserController@addAdress', 
		'as'   => 'addAdress'
	]);

	Route::get('/addAddress', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'addAdressGet'
	]);

	Route::post('/getUserAddress', [
		'uses' => 'UserController@getUserAddresses', 
		'as'   => 'getUserAddress'
	]);

	Route::get('/getUserAddress', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'getUserAddressGet'
	]);

	Route::post('/deleteUserAddress', [
		'uses' => 'UserController@deleteUserAddress', 
		'as'   => 'deleteAdd'
	]);

	Route::get('/deleteUserAddress', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'deleteAddGet'
	]);

	Route::post('/providerReport', [
		'uses' => 'UserController@makeReport', 
		'as'   => 'report'
	]);

	Route::get('/providerReport', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'reportGet'
	]);

	Route::post('/provider_evaluate', [
		'uses' => 'UserController@provider_evaluate',
		'as'   => 'provider.evaluate'
	]);

	Route::get('/provider_evaluate', [
		'uses' => 'UserController@echoEmpty',
		'as'   => 'provider.evaluateGet'
	]);

	Route::post('/delivery_evaluate', [
		'uses' => 'UserController@delivery_evaluate',
		'as'   => 'delivery.evaluate'
	]);

	Route::get('/delivery_evaluate', [
		'uses' => 'UserController@echoEmpty',
		'as'   => 'delivery.evaluateGet'
	]);
	Route::post('/addAttach', [
		'uses' => 'UserController@addAttach', 
		'as'   => 'addAttach'
	]);

	Route::get('/addAttach', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'addAttachGet'
	]);

	Route::post('/deleteAttach', [
		'uses' => 'UserController@deleteAttach', 
		'as'   => 'deleteAttach'
	]);

	Route::get('/deleteAttach', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'deleteAttachGet'
	]);

	Route::post('/complain', [
		'uses' => 'UserController@addComplain', 
		'as'   => 'complain'
	]);

	Route::get('/complain', [
		'uses' => 'UserController@echoEmpty', 
		'as'   => 'complainGet'
	]);

	Route::post('/timeLine', [
		'uses' => 'UserController@fetchTimeLine',
		'as'   => 'timeLine'
	]);

	Route::get('/timeLine', [
		'uses' => 'UserController@echoEmpty',
		'as'   => 'timeLineGet'
	]);

	

	
	Route::post('/search', 'UserController@search');
	Route::get('/search', 'UserController@echoEmpty');
	Route::post('/prepareSearch', 'UserController@prepareSearch');
	Route::get('/prepareSearch', 'UserController@echoEmpty');
	Route::post('/preparePayment', 'UserController@preparePayment');
	Route::get('/preparePayment', 'UserController@echoEmpty');
	Route::post('/sms', 'UserController@sms');
	Route::get('/sms', 'UserController@echoEmpty');
	Route::post('/ttt', 'UserController@getFollowsAndLikes');
	Route::get('/ttt', 'UserController@echoEmpty');
	Route::post('/prepareSignUp', 'UserController@prepareSignUp');
	Route::get('/prepareSignUp', 'UserController@echoEmpty');
	Route::post('/orderDetails', 'UserController@getOrderDetails');
	Route::get('/orderDetails', 'UserController@echoEmpty');
	Route::post('/providerMeals', 'UserController@getProviderMeals');
	Route::get('/providerMeals', 'UserController@echoEmpty');
	Route::post('/edit_phone', 'UserController@edit_phone');
	Route::get('/edit_phone', 'UserController@echoEmpty');
	Route::post('/userCancelOrder', 'UserController@cancel_order');
	Route::get('/userCancelOrder', 'UserController@echoEmpty');

	Route::post('/userBalance', 'UserController@get_user_balance');
	Route::get('/userBalance', 'UserController@echoEmpty');

	Route::post('/userWithdraw', 'UserController@balance_withdraw');
	Route::get('/userWithdraw', 'UserController@echoEmpty');
	///////////// provider application /////////////
	
 Route::get('/mmm',function(){

        (new \App\Http\Controllers\SmsController())->send('dsdsdsdsdd' ,'01032878227');


 });
	
	     

	     // ahmed emam
	
	   // prepare signup first step  provider personal account apis 
	Route::post('/getProviderSignUpFirstStep', 'ProviderController@prepareSignUp');
	Route::get('/getProviderSignUpFirstStep', 'ProviderController@echoEmpty');

     //store signup for first Step
	Route::post('/providerSignUpFirstStep', 'ProviderController@signUp');
	Route::get('/providerSignUpFirstStep', 'ProviderController@echoEmpty');



    Route::post('/activateAccount', 'ProviderController@activateAccount');
	Route::get('/activateAccount', 'ProviderController@echoEmpty');

	Route::post('/resendActivationCode', 'ProviderController@resendActivationCode');
	Route::get('/resendActivationCode', 'ProviderController@echoEmpty');
 


  // prepare signup second step
	Route::post('/getProviderSignUpSecondStep', 'ProviderController@prepareSignUpSecondStep');
	Route::get('/getProviderSignUpSecondStep', 'ProviderController@echoEmpty');

     //store signup for second Step
	Route::post('/providerSignUpSecondStep', 'ProviderController@signUpSecondStep');
	Route::get('/providerSignUpSecondStep', 'ProviderController@echoEmpty');
 
    

	//Route::post('/activateProvider', 'ProviderController@activate_provider');
	//Route::get('/activateProvider', 'ProviderController@echoEmpty');

	Route::post('/providerLogin', 'ProviderController@providerLogin');
	Route::get('/providerLogin', 'ProviderController@echoEmpty');


	Route::post('/forgetPassword', 'ProviderController@forgetPassword');
	Route::get('/forgetPassword', 'ProviderController@echoEmpty');

	Route::post('/updatePassword', 'ProviderController@updatePassword');
	Route::get('/updatePassword', 'ProviderController@echoEmpty');

  

	Route::post('/prepareEditProfile', 'ProviderController@getProfileData');
	Route::get('/prepareEditProfile', 'ProviderController@echoEmpty');


	Route::post('/UpdateProfile', 'ProviderController@UpdateProfile');
	Route::get('/UpdateProfile', 'ProviderController@echoEmpty');


              // provider categories apis  

    Route::post('/getProviderMainCat', 'ProviderController@getProviderMainCats');
	Route::get('/getProviderMainCat', 'ProviderController@echoEmpty');

	 Route::post('/getProviderStoreCategories', 'ProviderController@getProviderStoreCategories');
	Route::get('/getProviderStoreCategories', 'ProviderController@echoEmpty');

 

    Route::post('/addProviderCategory', 'ProviderController@addProviderCategory');
	Route::get('/addProviderCategory', 'ProviderController@echoEmpty');

     
     Route::post('/editProviderCategory', 'ProviderController@editProviderCategory');
	Route::get('/editProviderCategory', 'ProviderController@echoEmpty');


	Route::post('/updateProviderCategory', 'ProviderController@updateProviderCategory');
	Route::get('/updateProviderCategory', 'ProviderController@echoEmpty');


	Route::post('/deleteProviderCategory', 'ProviderController@deleteProviderCategory');
	Route::get('/deleteProviderCategory', 'ProviderController@echoEmpty');

   


        // provider Offers APIs
 
   
     Route::post('/getProviderOffers', 'ProviderController@getProviderOffers');
	Route::get('/getProviderOffers', 'ProviderController@echoEmpty');


    Route::post('/addProviderOffer', 'ProviderController@addProviderOffer');
	Route::get('/addProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/addProviderOffer', 'ProviderController@addProviderOffer');
	Route::get('/addProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/payProviderOffer', 'ProviderController@payProviderOffer');
	Route::get('/payProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/stopProviderOffer', 'ProviderController@stopProviderOffer');
	Route::get('/stopProviderOffer', 'ProviderController@echoEmpty');


    /*Route::post('/editProviderOffer', 'ProviderController@editProviderOffer');
	Route::get('/editProviderOffer', 'ProviderController@echoEmpty');*/

 



     // provider  products  APIs

	Route::post('/addProduct', 'ProviderController@addProduct');
	Route::get('/addProduct', 'ProviderController@echoEmpty');

	Route::post('/getProducts', 'ProviderController@getProducts');
	Route::get('/getProducts', 'ProviderController@echoEmpty');

	Route::post('/deleteProduct', 'ProviderController@delete_Product');
	Route::get('/deleteProduct', 'ProviderController@echoEmpty');


	Route::post('/prepareProductUpdate', 'ProviderController@prepare_Product_Update');
	Route::get('/prepareProductUpdate', 'ProviderController@echoEmpty');



    Route::post('/updateProduct', 'ProviderController@updateProduct');
	Route::get('/updateProduct', 'ProviderController@echoEmpty');

	
	//////// provider Jobs apis /////////////


	 Route::post('/getProviderJobs', 'ProviderController@providerJobs');
	Route::get('/getProviderJobs', 'ProviderController@echoEmpty');

 

    Route::post('/addProviderJob', 'ProviderController@addProviderJob');
	Route::get('/addProviderJob', 'ProviderController@echoEmpty');

     
     Route::post('/editProviderJob', 'ProviderController@editProviderJob');
	Route::get('/editProviderJob', 'ProviderController@echoEmpty');


	Route::post('/updateProviderJob', 'ProviderController@updateProviderJob');
	Route::get('/updateProviderJob', 'ProviderController@echoEmpty');


	Route::post('/deleteProviderJob', 'ProviderController@deleteProviderJob');
	Route::get('/deleteProviderJob', 'ProviderController@echoEmpty');


	Route::post('/deleteProviderJob', 'ProviderController@deleteProviderJob');
	Route::get('/deleteProviderJob', 'ProviderController@echoEmpty');
     

    Route::post('/getProviderJobDetails', 'ProviderController@getJobDetails');
	Route::get('/getProviderJobDetails', 'ProviderController@echoEmpty');

	Route::post('/getJobApplicants', 'ProviderController@jobApplicants');
	Route::get('/getJobApplicants', 'ProviderController@echoEmpty');

 

	/////// ///////  Request for excellence  APIS //////////
 
     Route::post('/getProviderExcellenceRequests', 'ProviderController@getExcellenceRequests');
	Route::get('/getProviderExcellenceRequests', 'ProviderController@echoEmpty');



    Route::post('/addProviderExcellenceRequests', 'ProviderController@addExcellenceRequests');
	Route::get('/addProviderExcellenceRequests', 'ProviderController@echoEmpty');



	Route::post('/payProviderExcellenceRequests', 'ProviderController@payExcellenceRequests');
	Route::get('/payProviderExcellenceRequests', 'ProviderController@echoEmpty');

 
    Route::post('/ProviderExcellenceRequestDetails', 'ProviderController@ExcellenceRequestDetails');
	Route::get('/ProviderExcellenceRequestDetails', 'ProviderController@echoEmpty');



 


 


	Route::post('/editProviderPhone', 'ProviderController@update_provider_phone');
	Route::get('/editProviderPhone', 'ProviderController@echoEmpty');

	Route::post('/deleteMeal', 'ProviderController@delete_meal');
	Route::get('/deleteMeal', 'ProviderController@echoEmpty');

 
 
	

	Route::post('/ordersCounts', 'ProviderController@fetchOrdersCounts');
	Route::get('/ordersCounts', 'ProviderController@echoEmpty');

	Route::post('/getProviderOrders', 'ProviderController@getProviderOrders');
	Route::get('/getProviderOrders', 'ProviderController@echoEmpty');

	Route::post('/orderAcceptance', 'ProviderController@orderAcceptance');
	Route::get('/orderAcceptance', 'ProviderController@echoEmpty');

	Route::post('/getComplains', 'ProviderController@getComplains');
	Route::get('/getComplains', 'ProviderController@echoEmpty');

	Route::post('/changeOrderStatus', 'ProviderController@changeOrderStatus');
	Route::get('/changeOrderStatus', 'ProviderController@echoEmpty');

	Route::post('/getBalances', 'ProviderController@getProviderBalance');
	Route::get('/getBalances', 'ProviderController@echoEmpty');

	Route::post('/withdraw_request', 'ProviderController@withdraw');
	Route::get('/withdraw_request', 'ProviderController@echoEmpty');

	Route::post('/order_properties', 'ProviderController@getProviderOrderProperties');
	Route::get('/order_properties', 'ProviderController@echoEmpty');

	Route::post('/save_order_properties', 'ProviderController@saveOrderProperties');
	
	Route::get('/save_order_properties', 'ProviderController@echoEmpty');

	Route::post('/receiveOrderSwitch', 'ProviderController@receiveOrderSwitch');
	Route::get('/receiveOrderSwitch', 'ProviderController@echoEmpty');

	Route::post('/accept_video_file', 'ProviderController@accept_video_file');
	Route::post('/pTest', 'ProviderController@test');

	Route::post('/getProviderFollowers', 'ProviderController@getProviderFollowers');
	Route::post('/getDeliveries', 'ProviderController@getDeliveries');

    ////  add route to get is delivery or allow recive order ////
    Route::post('/isReceiveOrders', 'ProviderController@isReceiveOrders');
    Route::get('/isReceiveOrders', 'ProviderController@echoEmpty');

	//marketer
	Route::post('/marketerSignUp', 'ProviderController@marketerSignUp');
	Route::get('/marketerSignUp', 'ProviderController@echoEmpty');

	Route::post('/activateMarketer', 'ProviderController@activate_marketer');
	Route::get('/activateMarketer', 'ProviderController@echoEmpty');

	Route::post('/marketerLogin', 'ProviderController@marketerLogin');
	Route::get('/marketerLogin', 'ProviderController@echoEmpty');

	Route::post('/marketerEditProfile', 'ProviderController@marketerEditProfile');
	Route::get('/marketerEditProfile', 'ProviderController@echoEmpty');

	Route::post('/editMarketerPhone', 'ProviderController@update_marketer_phone');
	Route::get('/editMarketerPhone', 'ProviderController@echoEmpty');

	Route::post('/getMarketerBalances', 'ProviderController@getMarketerBalance');
	Route::get('/getMarketerBalances', 'ProviderController@echoEmpty');

	Route::post('/getMarketerClients', 'ProviderController@getMarketerClients');
	Route::get('/getMarketerClients', 'ProviderController@echoEmpty');

	Route::post('/marketerBalanceDetails', 'ProviderController@marketerBalanceDetails');
	Route::get('/marketerBalanceDetails', 'ProviderController@echoEmpty');

    // route to get marketer data
    Route::post("/prepareEditMarketerProfile" , "ProviderController@prepareEditMarketerProfile");
    Route::get("/prepareEditMarketerProfile" , "ProviderController@echoEmpty");


	//crons
	Route::get('/crons', 'Crons@cron_job');
  	Route::get('/resetMealsQty', 'Crons@reset_meal_qty_crone');
 	Route::get('/refuseMissedOrdes', 'Crons@refuse_missed_orders_crone');

	/// route to change order status after 30 min if the provider didn't proccess it //////
     Route::get("/prepare_limit" , "Crons@prepare_limit");

    /// route to change the status of order after 15 min if the dekuvery didn't accept or reject  ////
     Route::get("/delivery_accept_limit" , "Crons@delivery_accept_limit");

	//deliveries
	Route::post('/deliveryPrepareSignUp', 'DeliveryController@prepareSignUp');
	Route::get('/deliveryPrepareSignUp', 'DeliveryController@echoEmpty');

	Route::post('/deliverySignUp', 'DeliveryController@signUp');
	Route::get('/deliverySignUp', 'DeliveryController@echoEmpty');

	Route::post('/activateDelivery', 'DeliveryController@activateDelivery');
	Route::get('/activateDelivery', 'DeliveryController@echoEmpty');

	Route::get('/dForgetPass/{deliveryId}', [
		'uses' => 'DeliveryController@dforgetPassView',
		'as'   => 'pGetForgetPass'
	]);

	Route::post('/dForgetPassAction',[
		'uses' => 'DeliveryController@dForgetPassAction',
		'as'   => 'dForgetPassAction'
	]);

	Route::get('/dForgetPassAction',[
		'uses' => 'DeliveryController@echoEmpty',
		'as'   => 'dForgetPassActionGet'
	]);

	Route::post('/dSendMail', 'DeliveryController@sendMailApi');
	Route::get('/dSendMail', 'DeliveryController@echoEmpty');

	Route::post('/deliveryLogin', 'DeliveryController@deliveryLogin');
	Route::get('/deliveryLogin', 'DeliveryController@echoEmpty');

	Route::post('/deliveryOrdersCounts', 'DeliveryController@fetchOrdersCounts');
	Route::get('/deliveryOrdersCounts', 'DeliveryController@echoEmpty');

	Route::post('/deliveryGetComplains', 'DeliveryController@getComplains');
	Route::get('/deliveryGetComplains', 'DeliveryController@echoEmpty');

	Route::post('/getDeliveryProfileData', 'DeliveryController@getProfileData');
	Route::get('/getDeliveryProfileData', 'DeliveryController@echoEmpty');

	Route::post('/updateDelivery', 'DeliveryController@editProfile');
	Route::get('/updateDelivery', 'DeliveryController@echoEmpty');

	Route::post('/getDeliveryOrders', 'DeliveryController@getDeliveryOrders');
	Route::get('/getDeliveryOrders', 'DeliveryController@echoEmpty');

	Route::post('/deliveryOrderAcceptance', 'DeliveryController@orderAcceptance');
	Route::get('/deliveryOrderAcceptance', 'DeliveryController@echoEmpty');

	Route::post('/getDeliveryBalances', 'DeliveryController@getDeliveryBalance');
	Route::get('/getDeliveryBalances', 'DeliveryController@echoEmpty');

	Route::post('/dWithdraw_request', 'DeliveryController@withdraw');
	Route::get('/dWithdraw_request', 'DeliveryController@echoEmpty');

	Route::post('/deliveryReceiveOrderSwitch', 'DeliveryController@receiveOrderSwitch');
	Route::get('/deliveryReceiveOrderSwitch', 'DeliveryController@echoEmpty');

	Route::post('/deliveryDeliverCancelOrder', 'DeliveryController@orderFinalAction');
	Route::get('/deliveryDeliverCancelOrder', 'DeliveryController@echoEmpty');

	Route::post('/deliverySetLocation', 'DeliveryController@setLocTracker');
	Route::get('/deliverySetLocation', 'DeliveryController@echoEmpty');

	Route::post('/deliveryGetLocation', 'DeliveryController@getLocTracker');
	Route::get('/deliveryGetLocation', 'DeliveryController@echoEmpty');
	//reports
	Route::post('/withdrawReport', 'ProviderController@withdrawReport');
	Route::get('/withdrawReport', 'ProviderController@echoEmpty');

	Route::post('/income', 'ProviderController@getIncome');
	Route::get('/income', 'ProviderController@echoEmpty');

	Route::post('/totalIncome', 'ProviderController@getTotalIncome');
	Route::get('/totalIncome', 'ProviderController@echoEmpty');


});