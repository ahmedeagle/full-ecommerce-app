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

Route::group(['middleware' => 'admin_auth'], function(){
	Route::get('/publishing/{id}/{val}/{proccess}/{col}/{table}', [
		'uses' => 'Controller@publishing', 
		'as'   => 'publishing' 
	]);

	Route::post('/countryCitiesAjax', [
		'uses' => 'Controller@getCountryCitites',
		'as'   => 'country.cities'
	]);

	Route::get('/print/{name}/{value}/{kind}', [
		'uses' => 'Controller@voucher',
		'as'   => 'print'
	]);
});

Route::group(['prefix' => 'zad-cpanel', 'namespace' => 'Admin'], function() {
	Route::group(['middleware' => 'admin_auth'], function(){
		Route::get('/', [
			'uses' => 'HomeController@index',
			'as'   => 'home'
		]);

		Route::get('/home', 'HomeController@index');

		Route::get('logout', 'AdminController@logout');
	});

	Route::group(['prefix' => 'adminPanel'], function(){
		Route::group(['middleware' => 'admin_guest'], function(){
			Route::get('/', [
				'uses' => 'AdminController@loginView',
				'as'   => 'loginView'
			]);

			Route::get('/login', [
				'uses' => 'AdminController@getLogin',
				'as'   => 'login'
			]);

			Route::post('/loginAction', [
				'uses' => 'AdminController@login',
				'as'   => 'admin.login'
			]);
		});

		Route::group(['middleware' => 'admin_auth'], function(){
			Route::get('/create_admin', [
				'uses' => 'AdminController@create_admin',
				'as'   => 'create_admin'
			]);

			Route::post('/creatAdmin', [
				'uses' => 'AdminController@create',
				'as'   => 'admin.store'
			]);
		});
	});

		
	Route::group(['middleware' => 'admin_auth'], function(){
		//providers routing
		Route::group(['prefix' => 'providersPanel'], function(){
			Route::get('/', 'ProviderController@show');

			Route::get('/providers', [
				'uses' => 'ProviderController@show',
				'as'   => 'provider.show'
			]);

			Route::get('/addProvider', [
				'uses' => 'ProviderController@create',
				'as'   => 'provider.create'
			]);

			Route::post('/addProvider', [
				'uses' => 'ProviderController@store',
				'as'   => 'provider.store'
			]);

			Route::get('/editProvider/{id}', [
				'uses' => 'ProviderController@edit',
				'as'   => 'provider.edit'
			]);

			Route::post('/updateProvider', [
				'uses' => 'ProviderController@update',
				'as'   => 'provider.update'
			]);

			Route::get('/orderProperties/{provider_id}', [
				'uses' => 'ProviderController@getOrderProperties',
				'as'   => 'provider.properties'
			]);

			Route::post('/setOrderProperties', [
				'uses' => 'ProviderController@saveOrderProperties',
				'as'   => 'provider.setProperties'
			]);

			Route::get('/providerIncome', [
				'uses' => 'ProviderController@getProviderIncomeView',
				'as'   => 'provider.income.show'
			]);

			Route::post('/incomeSearch', [
				'uses' => 'ProviderController@incomeSearch',
				'as'   => 'income.search'
			]);
		});

		Route::group(['prefix' => 'marketersPanel'], function(){
			Route::get('/', 'MarketerController@show');

			Route::get('/marketers', [
				'uses' => 'MarketerController@show',
				'as'   => 'marketers.show'
			]);

			Route::get('/addMarketer', [
				'uses' => 'MarketerController@create',
				'as'   => 'marketer.create'
			]);

			Route::post('/storeMarketer', [
				'uses' => 'MarketerController@store',
				'as'   => 'marketer.store'
			]);

			Route::get('/editMarketer/{id}', [
				'uses' => 'MarketerController@edit',
				'as'   => 'marketer.edit'
			]);

			Route::post('/updateMarketer', [
				'uses' => 'MarketerController@update',
				'as'   => 'marketer.update'
			]);

			Route::get('/marketerIncome', [
				'uses' => 'MarketerController@getMarketerIncomeView',
				'as'   => 'marketer.income.show'
			]);

			Route::post('/marketerIncomeSearch', [
				'uses' => 'MarketerController@incomeSearch',
				'as'   => 'marketer.income.search'
			]);
		});

		//deliveries routing
		Route::group(['prefix' => 'deliveriesPanel'], function(){
			Route::get('/deliveryIncome', [
				'uses' => 'DeliveryController@getDeliveryIncomeView',
				'as'   => 'delivery.income.show'
			]);

			Route::get('/', [
				'uses' => 'DeliveryController@show',
				'as'   => 'deliveries.show'
			]);

			Route::get('/create', [
				'uses' => 'DeliveryController@create',
				'as'   => 'deliveries.create'
			]);

			Route::post('/store', [
				'uses' => 'DeliveryController@store',
				'as'   => 'deliveries.store'
			]);

			Route::get('/edit/{id}', [
				'uses' => 'DeliveryController@edit',
				'as'   => 'deliveries.edit'
			]);
			Route::post('/activate/{id}', [
				'uses' => 'DeliveryController@activateDelivery',
				'as'   => 'deliveries.activate'
			]);

			Route::post('/update', [
				'uses' => 'DeliveryController@update',
				'as'   => 'deliveries.update'
			]);
		});

		//users routing
		Route::group(['prefix' => 'usersPanel'], function(){
			Route::get('/', 'UsersController@show');
			Route::get('/users', [
				'uses' => 'UsersController@show',
				'as'   => 'user.show'
			]);

			Route::get('/addUser', [
				'uses' => 'UsersController@create',
				'as'   => 'user.create'
			]);

			Route::post('/store', [
				'uses' => 'UsersController@store',
				'as'   => 'user.store'
			]);

			Route::get('/editUser/{id}', [
				'uses' => 'UsersController@edit',
				'as'   => 'user.edit'
			]);

			Route::post('/updateUser', [
				'uses' => 'UsersController@update',
				'as'   => 'user.update'
			]);
		});

		Route::group(['prefix' => 'requestsPanel'], function(){
			Route::get('/requests', [
				'uses' => 'FinancialController@getRequestsFilter',
				'as'   => 'requests.show'
			]);

			Route::post('/requests_search', [
				'uses' => 'FinancialController@requestsSearch',
				'as'   => 'requests.search'
			]);
			Route::get('/execute_withdraw/{id}', [
				'uses' => 'FinancialController@executeWithdraw',
				'as'   => 'requests.execute'
			]);

			Route::get('/requests/today', [
				'uses' => 'FinancialController@getTodayRequests',
				'as'   => 'requests.today'
			]);
		});

		Route::group(['prefix' => 'balancesPanel'], function(){
			Route::get('/balances', [
				'uses' => 'FinancialController@getBalancesFilter',
				'as'   => 'balances.show'
			]);

			Route::post('/balances_search', [
				'uses' => 'FinancialController@balancesSearch',
				'as'   => 'balances.search'
			]);
		});

		Route::group(['prefix' => 'income'], function(){
			Route::get('/appIncome', [
				'uses' => 'FinancialController@getAppIncome',
				'as'   => 'income.app'
			]);

			Route::post('/appIncome_search', [
				'uses' => 'FinancialController@searchAppIncome',
				'as'   => 'income.app.search'
			]);
		});

		Route::group(['prefix' => 'invoices'], function(){
			Route::get('/filter', [
				'uses' => 'FinancialController@getInvoices',
				'as'   => 'invoices.filter'
			]);

			Route::post('/search', [
				'uses' => 'FinancialController@searchInvoices',
				'as'   => 'invoices.search'
			]);

			Route::get('/create', [
				'uses' => 'FinancialController@createInvoice',
				'as'   => 'invoices.create'
			]);

			Route::post('/store', [
				'uses' => 'FinancialController@storeInvoice',
				'as'   => 'invoices.store'
			]);
		});

		Route::group(['prefix' => 'orders'], function(){
			Route::get('/', 'OrdersController@getOrdersFilter');
			Route::get('/filter', [
				'uses' => 'OrdersController@getOrdersFilter',
				'as'   => 'orders.filter'
			]);

			Route::get('/order_details/{id}', [
				'uses' => 'OrdersController@getOrderDetails',
				'as'   => 'orders.details'
			]);
		});

		Route::group(['prefix' => 'printing'], function(){
			Route::get('/receipt', [
				'uses' => 'FinancialController@getReceipt',
				'as'   => 'printing.receipt'
			]);
		});

		Route::group(['prefix' => 'setting'], function(){
			Route::get('/', [
				'uses' => 'SettingController@getSetting',
				'as'   => 'setting.show'
			]);

			Route::post('/save', [
				'uses' => 'SettingController@saveSetting',
				'as'   => 'setting.save'
			]);

			Route::post('/update', [
				'uses' => 'SettingController@updateSetting',
				'as'   => 'setting.update'
			]);
		});

		Route::group(['prefix' => 'categories'], function(){
			Route::get('/', [
				'uses' => 'CategoriesController@getCats',
				'as'   => 'category.show'
			]);

			Route::get('/create', [
				'uses' => 'CategoriesController@createCat',
				'as'   => 'category.create'
			]);

			Route::post('/store', [
				'uses' => 'CategoriesController@storeCat',
				'as'   => 'category.store'
			]);

			Route::get('/edit/{id}', [
				'uses' => 'CategoriesController@editCat',
				'as'   => 'category.edit'
			]);

			Route::post('/update', [
				'uses' => 'CategoriesController@updateCat',
				'as'   => 'category.update'
			]);
		});

		Route::group(['prefix' => 'complains'], function(){
			Route::get('/', [
				'uses' => 'ComplainsController@getComplains',
				'as'   => 'complains.show'
			]);

			Route::get('/search/{from?}/{to?}/{user?}/{provider?}/{delivery?}/{app?}', [
				'uses' => 'ComplainsController@search',
				'as'   => 'complains.search'
			]);

			Route::get('/today', [
				'uses' => 'ComplainsController@getTodayComplains',
				'as'   => 'complains.today'
			]);
		});

		Route::group(['prefix' => 'comments'], function(){
			Route::get('/', [
				'uses' => 'CommentsController@getComments',
				'as'   => 'comments.show'
			]);

			Route::get('/search/{from}/{to}/{user}/{phone}', [
				'uses' => 'CommentsController@search',
				'as'   => 'comments.search'
			]);

			Route::get('/delete/{id}', [
				'uses' => 'CommentsController@delete',
				'as'   => 'comments.delete'
			]);

			Route::get('/today', [
				'uses' => 'CommentsController@today',
				'as'   => 'comments.today'
			]);
		});

		Route::group(['prefix' => 'evaluations'], function(){
			Route::get('/provider_evaluations', [
				'uses' => 'EvaluationsController@getProviderEvaluations',
				'as'   => 'provider.evaluations.show'
			]);

			Route::get('/delivery_evaluations', [
				'uses' => 'EvaluationsController@getDeliveryEvaluations',
				'as'   => 'delivery.evaluations.show'
			]);

			Route::post('/evaluateion_search', [
				'uses' => 'EvaluationsController@evaluationSearch',
				'as'   => 'evaluations.search'
			]);

			// Route::get('/details/{id}', [
			// 	'uses' => 'EvaluationsController@evaluationDetails',
			// 	'as'   => 'evaluations.details'
			// ]);
		});
	});
});