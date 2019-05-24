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

class UsersController extends Controller
{
	public function __construct(){
		
	}

	public function show(){
		$users = User::join('city', 'users.city_id', '=', 'city.city_id')
					 ->select('users.*', 'city.city_en_name')
					 ->get();
		return view('cpanel.users.users', compact('users'));
	}

	public function create(){
		$countries = DB::table('country')->get();
		return view('cpanel.users.create', compact('countries'));
	}

	public function store(Request $request){
		$validate = Validator::make($request->all(), [
			'phone' => 'required|unique:users',
			'email' => 'required|unique:users'
		]);

		if($validate->fails()){
			$request->session()->flash('errors', $validate->errors()->first());
			return redirect()->back()->withInput();
		}
		//users model object
		$user = new User();
		$image = url('admin-assets/images/avatar_ic.jpg');
		$invitation_code = str_random(7);
		//setting data to insert it
		$user->full_name       = $request->input('full_name');
		$user->email           = $request->input('email');
		$user->phone           = $request->input('phone');
		$user->country_code    = $request->input('country_code');
		$user->password        = md5($request->input('password'));
		$user->invitation_code = $invitation_code;
		$user->profile_pic     = $image;
		$user->status    	   = 0;
		$user->city_id         = $request->input('city');

		//save user
		$userSave = $user->save();
		if($userSave){
			$request->session()->flash('success', 'User has been added successfully');
			return redirect()->route('user.show');
		}else{
			$request->session()->flash('errors', 'Failed to add Please try again later');
			return redirect()->back()->withInput();
		}
	}

	public function edit($id){
		$user       = User::where('users.user_id', $id)
					      ->join('city', 'users.city_id', '=', 'city.city_id')
					      ->join('country', 'city.country_id', '=', 'country.country_id')
					      ->select('users.*', 'country.country_id')
					      ->first();
		if($user != NULL){
			$country_id = $user->country_id;
		}else{
			return redirect()->route('user.show');
		}

		$countries = DB::table('country')->get();
		$cities    = DB::table('city')->where('country_id', $country_id)->get();
		return view('cpanel.users.edit', compact('user', 'country_id', 'countries', 'cities'));
	}

	public function update(Request $request){
		$validate = Validator::make($request->all(), [
			'phone' => 'required|unique:users,phone,'.$request->input('user_id').',user_id',
			'email' => 'required|unique:users,email,'.$request->input('user_id').',user_id'
		]);

		if($validate->fails()){
			$request->session()->flash('errors', $validate->errors()->first());
			return redirect()->back()->withInput();
		}

		//check if he changed the phone
		$check = User::where('phone', $request->input('phone'))
					 ->where('country_code', $request->input('country_code'))
					 ->where('user_id', $request->input('user_id'))
					 ->first();
		if($check != NULL){
			$status = 1;
		}else{
			$status = 0;
		}


		//users model object
		$user = new User();
		// $image = url('admin-assets/images/avatar_ic.jpg');
		// $invitation_code = str_random(7);
		//setting data to insert it
		if(!empty($request->input('password2'))){
			$update = User::where('user_id', $request->input('user_id'))
						  ->update([
						  		'full_name' => $request->input('full_name'),
						  		'email'     => $request->input('email'),
						  		'phone'     => $request->input('phone'),
						  		'country_code' => $request->input('country_code'),
						  		'password'     => md5($request->input('password2')),
						  		'status'       => $status,
						  		'city_id'      => $request->input('city')
						  	]);
		}else{
			$update = User::where('user_id', $request->input('user_id'))
						  ->update([
						  		'full_name' => $request->input('full_name'),
						  		'email'     => $request->input('email'),
						  		'phone'     => $request->input('phone'),
						  		'country_code' => $request->input('country_code'),
						  		'status'       => $status,
						  		'city_id'      => $request->input('city')
						  	]);
		}
		if($update){
			$request->session()->flash('success', 'User has been updated successfully');
			return redirect()->route('user.show');
		}else{
			$request->session()->flash('errors', 'Failed to update Please try again later');
			return redirect()->back()->withInput();
		}
	}
}
