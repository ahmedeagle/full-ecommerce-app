<?php

namespace App\Http\Controllers\Admin;

/**
 * 
 * @author Mohamed Salah <mohamedsalah7191@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Providers;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use Illuminate\Foundation\Validation\ValidatesRequests;

//Class needed for login and Logout logic
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//Auth facade
use Auth;

class AdminController extends Controller
{

	//Trait
    use AuthenticatesUsers;

	protected $redirectTo = '/zad-cpanel/home';

	//Custom guard for seller
    protected function guard()
    {
      return Auth::guard('zad_admin');
    }

	public function loginView(){
		return view('cpanel.admin.login');
	}

	public function getLogin(){
		return redirect()->route('loginView');
	}

	public function create(Request $request){

		$validator = Validator::make($request->all(), [
			'name'     => 'required', 
			'email'    => 'email|unique:admin',
			'password' => 'min:8',
		]);

		if($validator->fails()){
			return redirect()->back()->with('errors', $validator->errors())->withInput();
		}else{
			$insert = DB::table('admin')->insert([
							'full_name' => $request->input('name'),
							'email'     => $request->input('email'),
							'password'  => bcrypt($request->input('password'))
					  ]);

			if($insert){
				return redirect()->back()->with('msg', 'Admin added successfully');
			}else{
				$err = array('Failed to add admin');
				return redirect()->back()->with('errors', $err)->withInput();
			}
		}
	}

	// public function login(Request $request){
	// 	$validator = Validator::make($request->all(), [
	// 		'email'    => 'email|exists:admin,email',
	// 		'password' => 'min:8',
	// 	]);

	// 	if($validator->fails()){
	// 		return redirect()->back()->with('errors', $validator->errors())->withInput();
	// 	}else{
	// 		$check = DB::table('admin')->where('email', $request->email)
	// 								   ->where('password', md5($request->password))
	// 								   ->first();
	// 		if($check != NULL){
	// 			return redirect()->route('create_admin');
	// 		}else{
	// 			return redirect()->back()->withErrors('Failed to login')->withInput();
	// 		}
	// 	}
	// }

	public function create_admin(){
		return view('cpanel.admin.create');
	}

	public function getCountryCitites(Request $request){
		$country = $request->input('country_id');

		$cities  = DB::table('city')->where('publish', 1)->get();

	}
}
