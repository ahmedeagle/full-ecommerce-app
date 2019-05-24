<?php

namespace App\Http\Controllers\Admin;

/**
 * Class ProviderController.
 * it is a class to manage all provider functionalities
 * Zad provider sign up
 * Zad provider log in
 * Zad provider forget password
 * ..etc.
 * @author Mohamed Salah <mohamedsalah7191@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Providers;
use App\Meals;
use App\Deliveries;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

class HomeController extends Controller
{
	public function __construct(){
		
	}

	public function index(){
		$providers  = Providers::count();
		$deliveries = Deliveries::count();
		$users      = User::count();
		$comments   = DB::table('meal_comments')->count();
		$sale       = DB::table('orders_headers')->where('status_id', 4)->select(DB::raw('IFNULL((app_value + delivery_app_value),0) AS total'))->first();
		$sale =  ($sale != NULL)? $sale->total : 0;
		$return = DB::table('orders_headers')->where('status_id', '!=', 4)
											 ->where('payment_type', '!=', 1)
											 ->select(DB::raw('IFNULL((app_value + delivery_app_value),0) AS total'))->first();
		$return = ($return != NULL)? $return->total : 0;
		$meals  = Meals::where('publish', 1)->count();
		return view('cpanel.home', compact('sale', 'return', 'comments', 'deliveries', 'providers', 'deliveries', 'users', 'meals'));
	}
}
