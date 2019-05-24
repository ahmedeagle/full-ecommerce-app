<?php

namespace App\Http\Controllers;

/**
 * Class UserController.
 * it is a class to manage all users functionalities
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */

use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Product;
use App\Providers;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use DateTime;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
	public function __construct(Request $request){
		 
	}

	//method to prevent visiting any api link
	public function echoEmpty(){
		echo "";
	}

	


	/**
	* Registation for zad user application 
	* This function is to register zad user 
	* it works by receiving data from zad user mobile application (ANDROID & IOS) 
	* then store it in the database
	*/
	public function userSignUp(Request $request){


		//get applicatoin language
		$lang = $request->input('lang');

		//set user status to be not activated
		$status = 0;

		/**
		* preparing validation error messages
		* it will be anumber 
		* that number will be a pointer to an error string from errors messeges array
		* select error messages upon the language of the application
		*/
		$messages = array(
			'full_name.required' => 1,
			'full_name.min'      => 2,
			'email.required'     => 3,
			'phone.required'     => 4,
			'phone.numeric'      => 5,
			'country_code.required'=> 22,
			'email'              => 6, 
			'longitude.required_with' => 7,
			'latitude.required_with'  => 7,
			'password.required'  => 8, 
			'password.min'       => 9,
			'phone.unique' 		 => 13,
			'email.unique' 		 => 14,
			'required_with'      => 15,
			'invitation_code.exists' => 16,
			'city_id.required'		 => 17,
			'city_id.numeric'        => 18,
			'activation_code.required' => 19,
			'reg_id.required'		   => 23,
			'country_id.required'	   => 24,
			'country_id.numeric'       => 25,
			'password.confirmed'       => 26,
			'country_id.exists'        => 27,
			'city_id.exists'           => 28,
			'password_confirmation.required'       => 29,
		);

		// seting error messages array
		if($lang == 'ar'){
			//error messages array for 
			$messagesStr = array(
				1  => 'الإسم بالكامل  مطلوب',
				2  => 'الإسم بالكامل لا يجب ان يقل عن 3 حروف',
				3  => 'البريد الإلكترونى   مطلوب',
				4  => 'الجوال حقل مطلوب',
				5  => 'حقل الجوال يجب ان يكون ارقام فقط',
				6  => 'حقل الإميل يجب ان يكون فى شكل البريد الإلكترونى', 
				7  => 'يجب تحديد مكانك على الخريطة', 
				8  => 'حقل كلمة السر مطلوب', 
				9  => 'حقل كلمة السر لا يجب ان يقل عن 8 حروف', 
				10 => 'يجب ان تحدد نوع المستخدم',
				11 => 'نوع المستخدم يجب ان يكون مكون من رقم واحد فقط',
				12 => 'نوع المستخدم يجب ان يكون رقم',
				13 => 'رقم الجوال مستخدم من قبل',
				14 => 'البريد الإلكترونى مستخدم من قبل',
				15 => 'يجب ان تحدد إمتداد صورة البروفايل',
				16 => 'خطأ فى كود الدعوه',
				17 => 'رقم المدينه مطلوب', 
				18 => 'رقم المدينه يجب ان يكون رقم',
				19 => 'كود التفعيل مطلوب',
				22 => 'يرجى إضافة كود الدوله',
				23 => 'رقم الجهاز مطلوب',
				24 => 'رقم الدوله مطلوب', 
				25 => 'رقم الدوله يجب ان يكون رقم',
				26 => 'كلمتة المرور غير متطابقة ',
				27 => 'رقم الدولة غير موجود ',
				28 => 'قم المدينة غير موجود ',
				29 => 'لابد من تاكيد كلمة المرور ',
			);
			$city_col = "city_ar_name AS city_name";
		}else{
			$messagesStr = array(
				1  => 'Full Name is required',
				2  => 'Full Name must be more than 3 characters',
				3  => 'E-mail is required',
				4  => 'Phone is required',
				5  => 'Phone must be only digits',
				6  => 'E-mail must be in e-mail format',
				7  => 'Please locate your location on map', 
				8  => 'Password is required', 
				9  => 'Password must be more than 7 characters', 
				10 => 'Please determine user type',
				11 => 'User type must be only 1 digit',
				12 => 'User type must be a digit',
				13 => 'Phone number is used before',
				14 => 'E-mail is used before',
				15 => 'You must determine profile picture extenstion',
				16 => 'Wrong invitation_code',
				17 => 'city_id is required',
				18 => 'city_id must be a number',
				19 => 'activation_code is required',
				22 => 'country code is required',
				23 =>'device register id is required',
				24 => 'country_id is required',
				25 => 'country_id must be a number',
				26 => 'password confirmation wrong',
				27 => 'country  doesn\'t exists',
				28 => 'city  doesn\'t exists',
				29 => 'password confirmation required',
			);
			$city_col = "city_en_name AS city_name";
		}
		

		$rules=[
				        'full_name'       => 'required|min:3',
				        'email'           => 'required|email|unique:users',
				        'phone'           => 'required|numeric|unique:users',
				        'country_code'    => 'required',
				        'password'        => 'required|min:8|confirmed',
				         'password_confirmation'  => 'required',
				        'city_id'         => 'required|numeric|exists:city,city_id',
				        'country_id'      => 'required|numeric|exists:country,country_id',
				        'longitude'       => 'required_with:latitude',
				        'latitude'        => 'required_with:longitude',				        
 				        'reg_id'		  => 'required'
				    ];

		$validator = Validator::make($request->all(),$rules, $messages);

		if($validator->fails()){
			 $errors   = $validator->errors();
			$error    = $errors->first();
			
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $messagesStr[$error]]);
		}else{
			//here we can set posted data sent from the mobile
			$fullName      = $request->input('full_name');
			$email         = $request->input('email');
			$phone         = $request->input('phone');
			$password      = $request->input('password');
			$image_ext     = $request->input('image_ext');
			$longitude     = $request->input('longitude');
			$latitude      = $request->input('latitude');
			$invitation    = $request->input('invitation_code');
			$city 		   = $request->input('city_id');
			$country 	   = $request->input('country_id');
			$country_code  = '+'.$request->input('country_code');
			$device_reg_id = $request->input('reg_id');
            
			if(empty($latitude)){
			    $latitude = "";
			}
            if(empty($longitude)){
			    $longitude = "";
			}
			 

			 $image = "avatar_ic.png";

			    if($request->input('profile_pic')){
 
                    $image  = $request->input('profile_pic') ;
                  //save new image   
                    $image ->store('/','users');
                                       
                  $nameOfImage = $image ->hashName();

                 $image =  $nameOfImage;

 
           }  
         
                          // send activation code to provider 
 
				    $code          = $this->generate_random_number(4);

			        $token         = $this -> getRandomString(128);

			        $activation_code = json_encode([
			            'code'   => $code,
			            'expiry' => Carbon::now()->addDays(1)->timestamp,
			        ]);
			        
			        $message = (App()->getLocale() == "en")?
			                    "Your Activation Code is :- " . $code :
			                     "رقم الدخول الخاص بك هو :- " .$code ;
 
 

			//users model object
			$user = new User();

			//setting data to insert it
			$user->full_name       = $fullName;
			$user->email           = $email;
			$user->phone           = $phone;
			$user->country_code    = $country_code;
			$user->password        = md5($password);
			$user->profile_pic     = $image;
			// $user->type            = $type;
			$user->status    	   = $status;
			$user->longitude       = $longitude;
			$user->latitude 	   = $latitude;
 			$user->city_id         = $city;
			$user->country_id      = $country;
			$user->activation_code =$activation_code;
			$user->token           = $token;
			$user->device_reg_id   = $device_reg_id;

			//save user
			$userSave = $user->save();

			if($userSave){
				 
				if($lang == "ar"){
					$successMsg = "تم التسجيل بنجاح";
				}else{
					$successMsg = "Signed up successfully";
				}

                     //initailize account balance 
				DB::table('balances')->insert(['actor_id' => $user -> id, 'type' => 'user','current_balance' => 0, 'due_balance' => 0]);


                  // send phone activation code 
                $res = (new SmsController())->send($message , $user ->phone);
 
				$userData = $this->getUserData($user->id, $lang);
				// return json_encode($response_array);
				return response()->json(['status'=> true, 'errNum' => 0, 'user' => $userData, 'msg' => $successMsg]);
			}else{
				if($lang == "ar"){
					$errMsg = "فشلت العملية";
				}else{
					$errMsg = "Proccess failed";
				}
				return response()->json(['status'=> false, 'errNum' => 21, 'user' => [], 'msg' => $errMsg]);
			}

		}

	}


	 
	 public function activateUserAccount(Request $request){
		
          $lang = $request->input('lang');

          if($lang == "ar"){
			$msg = array(
				0 => 'تم التفعيل',
				1 => 'كود غير صحيح ',
				2 => 'لابد من  ادخال الكود ',
				3 =>  'لابد من توكن المستخدم ',
				4 =>  'فشل التفعيل من فضلك حاول لاقحا',
				5=> 'كود تفعيل غير صحيح ',
			);
		}else{
			$msg = array(
				0 => 'Activated successfully',
				1 => 'incorrect code',
			    2 => 'code is required',
				3 => 'access_token required',
				4 => 'Failed to activate, please try again later',
				5 => 'Code is not Correct',
			);
		}

		$messages = array(
			'code.required'         => 2,
			'access_token.required' => 3
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
			'code'         => 'required'
		], $messages);
 
        if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}
  

         $user_id = $this->get_id($request,'users','user_id');
         if($user_id ==0 ){

         	   return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
         }

         $user = User::where('user_id',$user_id);
        $activate_phone_hash = $user -> first() -> activation_code;
		$code                = json_decode($activate_phone_hash) -> code;

		 if($code  != $request -> code)
		  {
             return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		  }
  
        $data['status']          = 1;
        $data['activation_code'] = null;

        $user -> update($data);
 
        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

	}

 

	 public function resendActivationCode(Request $request){
 
    	 $lang = $request->input('lang');

          if($lang == "ar"){
			$msg = array(
				0 => 'تم  ارسال الكود بنجاح ',
 				1 =>  'لابد من توكن المستخدم ',
 				2 => 'فشل من فضلك حاول مجددا ',
			);
		}else{
			$msg = array(
				0 => 'code sent successfully',
 				1 => 'access_token required',
 				2 => 'failed try again later',
			);
		}

  

		$messages = array(
 			'access_token.required' => 1
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
 		], $messages);



        if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}
 

     $data=[];

     $user_id = $this->get_id($request,'users','user_id');
     if($user_id == 0){

     	 return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
     }

    $user = User::where('user_id',$user_id )  ;
 
    $code          = $this->generate_random_number(4);
 
    $data['activation_code'] = json_encode([
        'code'   => $code,
        'expiry' => Carbon::now()->addDays(1)->timestamp,
    ]);
 

     $user -> update($data);

    $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                 "رقم الدخول الخاص بك هو :- " .$code ;
  
    $res = (new SmsController())->send($message , $user -> first() ->phone);
   

    return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

}


	public function userLogin(Request $request){

		$lang = $request->input('lang');
		
		if($lang == "ar"){
			$msg = array(
				0 => 'تم الدخول',
				1 => 'رقم التليفون مطلوب',
				2 => 'تاكد من رقم التليفون مع إضافة كود الدوله', 
				3 => 'كلمة السر مطلوبه', 
				4 => 'خطأ فى البيانات',
				5 => 'لم يتم تفعيل الحساب بعد',
				6 => 'رقم الجهاز مطلوب'
			);
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$msg = array(
				0 => 'Logined successfully',
				1 => 'Phone is required',
				2 => 'Wrong phone number ', 
				3 => 'Password is required', 
				4 => 'Faild To authentication',
				5 => 'You need to activate your account',
				6 => 'device nubmer (reg_id) is required'
			);
			$city_col = "city.city_en_name AS city_name";
		}
		$messages = array(
		    
                'phone.required'    => 1,
				'password.required' => 3,
				'reg_id.required'   => 6

			);
		$validator = Validator::make($request->all(), [
			'phone'    => 'required',
			'password' => 'required',
			'reg_id'   => 'required'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $msg[$error]]); 
		}else{
			$user = new User();
			$getUser = $user->where('users.password', md5($request->input('password')))
							->where(function($q) use ($request){
						        $q->where('users.phone', $request->input('phone'))
						          ->orWhere(DB::raw('CONCAT(users.country_code,users.phone)'), $request->input('phone'));
						    })
							->join('city', 'users.city_id', 'city.city_id')
							->select('users.*', $city_col)
							->first();
			if($getUser != NULL && !empty($getUser) && $getUser->count()){
				$user->where('user_id', $getUser->user_id)->update(['device_reg_id' => $request->input('reg_id')]);
				$userData = array(
					'user_id'     => $getUser->user_id, 
					'full_name'   => $getUser->full_name,
					'profile_pic' => env('APP_URL').'/public/userProfileImages/'.$getUser->profile_pic,
					'status'          => $getUser->status,
 					'phone' 		  => $getUser->phone,
					'country_code'    => $getUser->country_code,
					'email' 		  => $getUser->email,
					'longitude'       => $getUser->longitude, 
					'latitude' 		  => $getUser->latitude,
					'city_id'         => $getUser->city_id,
					'country_id'      => $getUser->country_id,
					'city_name'       => $getUser->city_name,
					'access_token'    => $getUser->token,

				);
				if($getUser->status == 0 || $getUser->status == "0"){
					return response()->json(['status'=> false, 'errNum' => 5, 'user' => $userData, 'msg' => $msg[5]]); 
				}
				
				return response()->json(['status'=> true, 'errNum' => 0, 'user' => $userData, 'msg' => $msg[0]]); 
			}else{
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]); 
			}
		}
	}
 

 
	public function forgetPassword(Request $request){
		   
		    $lang = $request->input('lang');
 

		if($lang == "ar"){
			$msg = array(
 				1 => 'رقم الهاتف مطلوب ',
				2 => 'رقم هاتف غير صحيح ',
				3 => 'رقم الهاتف غير موجود ',
				4 => 'تم ارسال كود تفعيل الي هاتفك ',
				5 => 'رقم الهاتف غير مفعل ',
				
			);
			 
		}else{
			$msg = array(
 				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'phone doesn\'t exists',
				4 => 'activation code sent successfully',
  				5 => 'phone not active'    
			);
			 
		}
	        $rules    = [
                   "phone" => "required|numeric|exists:users,phone"
		        ];

		        $messages = [
		                "required" => 1,
		                "numeric"  => 2,
		                "exists"   => 3
		        ];
		        
		        $validator  = Validator::make($request->all(), $rules, $messages);

		        if($validator->fails()){
		            $error = $validator->errors()->first();
		            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		        }

		        //select proser vider base on his/her phone number if exists
		        $userData = DB::table("users")->where("phone" , $request->input("phone"))->select("user_id")->first();

		        $user = User::where('user_id',$userData -> user_id);
                 

 		        if($user -> first()->  phoneactivated == '0' ){

		            return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);

		        }


		        $code = $this -> generate_random_number(4);

		        $message = (App()->getLocale() == "en")?
		            "Your Activation Code is :- " . $code :
		            $code . "رقم الدخول الخاص بك هو :- " ;

		        $activation_code = json_encode([
		            'code'   => $code,
		            'expiry' => Carbon::now()->addDays(1)->timestamp,
		        ]);

		        $user -> update([
		        	 'activation_code'   => $activation_code,
		        ]);

		        (new SmsController())->send($message , $user ->first()->phone);

		        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4] , "access_token" => $user -> first() ->token]);

	}

	 

	   public function updatePassword(Request $request){

           $lang = $request->input('lang');

        $rules      = [
            "password"      => "required|min:8|confirmed",
            "access_token"  => "required"
        ];

        $messages   = [
            "password.required"     => 1,
            "password.required"     => 1,
            'password.min'          => 2,
            'password.confirmed'    => 3,
            'access_token.required' => 5
        ];



		if($lang == "ar"){
			$msg = array(
 				1 => 'لابد من ادخال كلمة المرور ',
				2 => 'كلمه المرور  8 احرف ع الاقل ',
				3 => 'كلمة المرور غير متطابقه ',
 				4 => 'تم تغيير كلمة  المرور بنجاح ',
 				5 => 'توكن المستخدم غير موجود'
				
			);
			 
		}else{
			$msg = array(
 				1 => 'password field required',
				2 => 'password minimum characters is 8',
				3 => 'password not confirmed',
   				4 => 'password successfully updated'    ,
   				5=>  'user token required'
			);
			 
		}

       
        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }

        $user = User::where('user_id',$this->get_id($request,'users','user_id'))
                        -> update([
                                      
                                         'password'              =>  md5($request->input('password')),
                                         'activation_code'       => null
                                 ]);

               
 

        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);
    }



protected function getUserData($user, $lang, $action = "get"){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		return User::where("user_id", $user)
				   ->join('city', 'users.city_id', '=', 'city.city_id')
				   ->select('users.user_id',
				    'users.full_name', 
				    'users.profile_pic', 
				     DB::raw("CONCAT('".env('APP_URL')."','/public/userProfileImages/',users.profile_pic) AS profile_pic"),
				    'users.status', 
				    'users.phone',
				   	'users.country_code', 
				   	'users.email', 
				   	'users.longitude', 
				   	'users.latitude', 
				   	'users.city_id',
				   	 'users.country_id', 
				   	 'users.token AS access_token',
				   	 $city_col)
				   ->first();
	}




public function getProfileData(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$country_col = "country_ar_name AS country_name";
			$city_col    = "city_ar_name AS city_name";
			$msg = array(
				0 => '',
				1 => 'توكن المستخدم مطلوب',
 				2 => 'لا يوجد بيانات',
 				3 => 'المستخدم غير موجود '
			);
		}else{
			$country_col = "country_en_name AS country_name";
			$city_col    = "city_en_name AS city_name";
			$msg = array(
				0 => '',
				1 => 'access_token is required',
 				2 => 'There is no data',
 				3 => 'user not found '
			);
		}

		$messages = array(
			'required' => 1, 
 		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required'
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

			
		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		        }

			$userData    = User::where('user_id', $userId)
									->select("user_id","full_name AS user_name", 'phone','country_code', 'email', 
										 DB::raw("CONCAT('".env('APP_URL')."','/public/userProfileImages/',users.profile_pic) AS profile_pic"), 
										 'country_id',
										 'city_id',
										  'created_at'
										)
			 							->first();


 			$userCountry = $userData->country_id;
			$userCity    = $userData->city_id;

			$countries       = DB::table('country')->where('publish', 1)->select('country_id', $country_col, DB::raw('IF(country_id = '.$userCountry.', true, false) AS chosen'), 'country_code')->get();

			$cities          = DB::table('city')->select('city_id', $city_col, DB::raw('IF(city_id = '.$userCity.', 1, 0) AS chosen'))->get();

			return response()->json([
										'status'       => true, 
										'errNum'       => 0, 
										'msg' 		   => $msg[0],
										'userData'     => $userData,
										'countries'    => $countries,
										'cities'       => $cities
									]);
		
	}

public function UpdateProfile(Request $request){

 
		$lang = $request->input('lang');

		if($lang == "ar"){
			$msg = array(
				0 => 'تم تعديل البيانات بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'الدولة غير موجودة ',
				3 => ' المدينة  غير موجودة',
				4 => 'المستخدم غير موجود ',
				5 => 'فشلت العمليه من فضلاك حاول لاحقا',
				6 => ' رقم الجوال لابد ان يكون ارقام ',
				7 =>  'صوره الملف الشخصي غير  صالحة ',
				8 => 'صيغه الهاتف غير صحيحة '

 			);

		}else{
			$msg = array(
				0 => 'Updated successfully',
				1 => 'All fields are required',
 				2 => 'Country doesn\'t exists',
				3 => 'country doesn\'t exists',
 				4 => 'user Not Found ',
				5 => 'Failed to update, please try again later',
				6 => ' phone number must be numeric',
				7 =>  'profile picture not valid',
				8 => 'phone number format invalid'

 
			);
		}

		$messages = array(

			'required'                  => 1,
 			'country_id.exists'         => 2,
 			'city_id.exists'            => 3,
 			'phone.numeric'             => 6,
 		    'mimes'                     => 7,
 		    'regex'                     => 8,
 

		);

		$rules=[

			'access_token'     =>  'required',
 			'full_name'        => 'required',
 			'country_id'       => 'required|exists:country,country_id',
            'city_id'         => 'required|exists:city,city_id',

		];

	
	      $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
		        }


      $user = DB::table("users") ->where('user_id',$userId);
       

       $input = $request->only('full_name' , 'phone', 'city_id','country_id');

         
        $input['country_code'] =$this -> checkCountryCodeFormate($request->input('country_code'));
 
        if($input['phone'] != $user ->first()->  phone){

            $rules['phone']        = array('required','numeric','regex:/^(05|5)([0-9]{8})$/','unique:users,phone');
            $rules['country_code'] = "required";
            

        }else{

             $rules['phone'] = array('required','numeric','regex:/^(05|5)([0-9]{8})$/');
             $rules['country_code'] = "required";

        }


        if($request -> profile_pic){
 

            $rules['profile_pic'] = "mimes:jpeg,png,jpg";

        } 
 

        $validator = Validator::make($request->all(), $rules ,$messages);

		if($validator->fails()){
			 $error = $validator->errors() ->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

         if($input['phone'] != $user ->first() ->  phone){

            $code = $this -> generate_random_number(4);

            $input['activation_code'] = json_encode([
                'code'   => $code,
                'expiry' => Carbon::now()->addDays(1)->timestamp,
            ]);

            $input['status'] = "0";

            $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                $code . "رقم الدخول الخاص بك هو :- " ;

            (new SmsController())->send($message , $user ->first()-> phone);

            $isPhoneChanged = true;
        }else{
            $isPhoneChanged = false;
        }
 	  
  

          $nameOfImage = "avatar_ic.png";

        if($request-> profile_pic ){


            $image  = $request -> profile_pic ;

 
            if($user ->first() -> profile_pic != null && $provider ->first() -> profile_pic != ""){
                    
		                //delete the previous image from storage 
		              if(Storage::disk('users')->exists($user ->first()  -> profile_pic))
		               {
		                     
		                     Storage::disk('users')->delete($user ->first()  -> profile_pic);

		               }
 
                         //save new image   
                    $image ->store('/','users');
                                       
                   $nameOfImage = $image ->hashName();
  
            }

            

                   $input['profile_pic'] =  $nameOfImage;


 
    }  
       
            $user -> update($input);


          $getUser  =  $user -> first();

				$providerData = array(
					'id'              => $getUser->user_id,
					'full_name'       => $getUser->full_name,
  					'phone' 		  => $getUser->phone,
					'country_code'    => $getUser->country_code,
  					'access_token'    => $getUser->token,
                     'status'         => $getUser->status, 
 					'country_id'      => $getUser->country_id,
					'city_id'         => $getUser->city_id,
  					'profile_pic'     => env('APP_URL').'/public/userProfileImages/'.$getUser->profile_pic ,
 
 					'created_at'      => date('Y-m-d h:i:s', strtotime($getUser->created_at))

				);


               //isPhoneChanged to notify mobile  app developers to redirect to activate phone number page 
  
           return response()->json([

           	     'status' => true, 
           	     'errNum' => 0, 
           	     'msg' => $msg[0] ,
           	     'user' => $providerData,
           	     'isPhoneChanged' => $isPhoneChanged
 
           	 ]);
 
  	}


   public function mainCats(Request $request){

            $lang = $request->input('lang');
         
         	if($lang == "ar"){
			 

			$cat_col = "categories.cat_ar_name AS cat_name";
			
		}else{
			 

			$cat_col = "categories.cat_en_name AS cat_name";
		}

		   
		$maincategory = DB::table('categories') 
						    -> where('categories.publish',1)
 						    ->select(    
 						                'categories.cat_id'	,
								    	'categories.cat_img', 
								    	$cat_col,
								        DB::raw("CONCAT('".env('APP_URL')."','/public/categoriesImages/',categories.cat_img) AS cat_image")
						            )
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'maincat' => $maincategory]);

   }


 public function get_nearest_providers_inside_main_sub_categories(Request $request){
         

         $lang = $request->input('lang');
          
         //0 filter by distance   //1 filter by rate   //2 filter by none
        if($lang == "ar"){
			 

			 $name = 'ar';

			  $msg  = [
			            1  => 'جميع الحقول مطلوبة ',
			            2  => 'التصنيف غير موجود ',
			            3  => 'تمت العملية بنجاح ',
			            4  => 'لابد من ادخال النوع بين  0 , 1 '
			        ];

			
		}else{
			 

			 $name = 'en';

	  $msg        = [
		               1  => 'all fields required',
		               2  => 'category id doesn\'t exists',
		               3  => 'done successfully',
		               4  => 'must select type fron 0,1'
                 ];
		}

       

        $rules      = [
            "cat_id" => "required|exists:categories,cat_id",
            "type"   => "required|in:0,1"

        ];
        $messages   = [
            "required"   => 1,
            "exists"     => 2,
            "in"         => 4
        ];
       
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }

        $type = $request->input("type");

       $pagianted_providers =  DB::table("categories")
                                ->join("providers" , "providers.category_id" , "categories.cat_id")   
                                 ->where("categories.cat_id" , $request->input("cat_id"))
                                 ->where("providers.publish" , 1)
                                ->select(
                                    "providers.provider_id",
                                    "providers.store_name AS store_name",
                                    "providers.provider_rate",
                                    "providers.membership_id",
                                    "providers.latitude",
                                    "providers.longitude",
                                    "providers.token AS access_token",
                                    DB::raw("CONCAT('". env('APP_URL') ."','/public/providerProfileImages/',providers.profile_pic) AS image_url")
                                )
                                ->groupBy("providers.provider_id")
                                ->paginate(10);

        (new HomeController())->filter_providers($request,$name,$pagianted_providers ,$type);
 
        if($type == 0){
            // filter based on distance by nearest
              $providers = $pagianted_providers->sortBy(function($item){
                return $item->distance;
            })->values();

        }else{
            // filter by rate
             $providers = $pagianted_providers->sortByDesc(function($item){
                return $item->averageRate;
            })->values();
        }

              //used to make pagination from collection 

        $providers = new LengthAwarePaginator(
                                $providers,
                                $pagianted_providers->total(),
                                $pagianted_providers->perPage(),
                                $request->input("page"),
                                ["path" => url()->current()]

        );


        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3], "providers" => $providers]);
    }



 
	public function prepareProviderPage(Request $request){
		 
		 $lang       = $request->input('lang');

		if($lang == "ar"){
			$msg = array(
				0 => 'تم جلب البيانات بنجاح ',
				1 => 'لابد من رقم المتجر ',
				2 => 'المتجر غير موجود ',
				3 => 'المستخدم غير موجود ', 
				4 => 'توكن المستخدم مطلوب في حاله تم تمريرة '
 			);
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'provider_id doesn\'t exists',
				2 =>  'provider not exists',
				3 => 'user not found ',
				4 => 'user access token required when  it pass' 

 			);
		}
		$messages = array(
 			'provider_id.required' => 1,
 			'provider_id.exists'   => 2,
 			'access_token.required' =>4, 
 		);
		$validator = Validator::make($request->all(), [
 			'provider_id'   => 'required|numeric|exists:providers,provider_id',

 		], $messages);

         $providerId = $request -> provider_id; 
 
         $userId=0;  // return all products 
            
         if($request -> has('access_token'))
         {

         	   $rules['access_token']  ="required";

		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		        }
        }


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}        
 		 

 	    $rates = DB::table('providers_rates')
                    ->where('providers_rates.provider_id' , $providerId)
                    ->select(
                        DB::raw("IFNULL(COUNT(providers_rates.id),0)  AS number_of_rates"),
                        DB::raw("IFNULL(COUNT(providers_rates.rates),0) AS sum_of_rates")
                     )
                    ->first();

                $numberOfRates = $rates->number_of_rates;
                $sumRate   = $rates->sum_of_rates;
                 if($numberOfRates != 0 && $numberOfRates != null){
                    $totalAverage  = $sumRate/$numberOfRates;
                }else{
                    $totalAverage = 0;
                }
			 
			//get provider data 
			$provider = Providers::where('provider_id',$providerId)
								  ->select('provider_id',
								            'store_name', 
								            'membership_id',
								            'delivery_price',								  	       
								  	       DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/',providers.profile_pic) AS profile_pic"),
 								  	        DB::raw(" '".$totalAverage."' AS provider_rate")

 								  	    )
								   ->first();

 			  
			//get provider categories
			$providerCategories = DB::table('categories_stores')
			                               -> where('categories_stores.provider_id', $providerId)
											->select('categories_stores.id AS cat_id','categories_stores.store_cat_ar_name', 'categories_stores.store_cat_en_name'

										         )
											->get();


			$catId  = $request->input('cat_id');
				 
			//get current provider and category products
			if($catId != 0){
				$products = Product::where('products.provider_id', $providerId)
							         ->where('products.category_id', $catId)
							         ->where('products.publish', 1)
							         ->select('products.id', 'products.title', 'products.price', 
							           'products.likes_count', 'products.product_rate', 
							         		   DB::raw('IF ((SELECT count(id) FROM product_likes WHERE product_likes.user_id = '.$userId.' AND product_likes.product_id = products.id) > 0, 1, 0) as isFavorit'));

							    $numOfProducts = $products -> count() ;
							    $products      = $products -> paginate(10);

                               $provider -> numOfProducts = $numOfProducts;
					        foreach($products as $product){
					            $data = DB::table("product_images")
					                         ->where("product_images.product_id" , $product-> id)
					                        ->select(
					                               DB::raw("CONCAT('".env('APP_URL')."','/public/products/',product_images.image) AS product_image")

					                            )
					                        ->first();
					            if($data){
					                $product ->product_image = $data->product_image;   
					            }else{
					                $meal->product_image = "";
					            }

					        }


			}else{
				 $products = Product::where('products.provider_id', $providerId)
							         ->where('products.publish', 1)
 							         ->select('products.id', 'products.title',
 							         	 'products.price',
							           'products.likes_count', 'products.product_rate', 
							         		     DB::raw('IF ((SELECT count(id) FROM product_likes WHERE product_likes.user_id = '.$userId.' AND product_likes.product_id = products.id) > 0, 1, 0) as isFavorit'));



							    $numOfProducts = $products -> count() ;
							    $products      = $products -> paginate(10);

                               $provider -> numOfProducts = $numOfProducts;
							          


					        foreach($products as $product){
					            $data = DB::table("product_images")
					                         ->where("product_images.product_id" , $product-> id)
					                        ->select(
					                               DB::raw("CONCAT('".env('APP_URL')."','/public/products/',product_images.image) AS product_image")

					                            )
					                        ->first();
					            if($data){
					                $product ->product_image = $data->product_image;   
					            }else{
					                $product->product_image = "";
					            }

					        }



			}

			 
			return response()->json(['status' => true, 'errNum' => 0,'provider' => $provider, 'providerCategories' => $providerCategories, 'products' => $products,  'msg' => $msg[0]]);
		 
	}

 
	
	//method to get  product details with product id
	public function getProductDetails(Request $request){
	    
	    
 	    $productId = $request->input('product_id');
 		$lang   = $request->input('lang');

         
         if($lang == "ar"){
			$msg = array(
				0 => 'تم جلب البيانات بنجاح ',
				1 => 'لابد من رقم  ألمنتج  ',
				2 => ' المنتج  غير موجود ',
				3 => 'المستخدم غير موجود ', 
  			);

 			$cat_col = "store_cat_ar_name AS cat_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'product id required ',
				2 =>  'product_id doesn\'t exists',
				3 => 'user not found ',
				

 			);

 			$cat_col = "store_cat_en_name AS cat_name";
		}

		 

		$messages = array(
 			'product_id.required'   => 1,
 			'product_id.exists'     => 2,
 			'access_token.required' =>3, 
 		);

		$validator = Validator::make($request->all(), [
 			'product_id'   => 'required|numeric|exists:products,id',
 			
 		], $messages);


		 $userId=0;  // return all products 
            
         if($request -> has('access_token'))
         {

         	   $rules['access_token']  ="required";

		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		        }
        }


        if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}        

		 
			//get product details
			$productDetails = Product::where('products.id', $productId)
								->join('providers', 'products.provider_id', '=', 'providers.provider_id')
								->join('categories_stores', 'products.category_id', '=', 'categories_stores.id')
								->select('products.id AS product_id', 'products.title', 'products.description','products.price',
									      'products.likes_count', 'providers.store_name AS store_name','providers.membership_id', 'providers.provider_id AS provider_id', $cat_col,
									       DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/',providers.profile_pic) AS profile_pic"),
									       'providers.latitude',
									       'providers.longitude'
									    )
								->first();


				$rates = DB::table('products_rates')
                    ->where('products_rates.product_id' , $productId)
                    ->select(
                        DB::raw("COUNT(products_rates.id) AS number_of_rates"),
                        DB::raw("SUM(products_rates.rates) AS sum_of_rates")
                     )
                    ->first();

                $numberOfRates = $rates->number_of_rates;
                $sumRate   = $rates->sum_of_rates;
                 if($numberOfRates != 0 && $numberOfRates != null){
                    $totalAverage  = $sumRate/$numberOfRates;
                }else{
                    $totalAverage = 0;
                }

                 // product average rat 
          $productDetails -> totalAverageRate = $totalAverage;


 					            $data = DB::table("product_images")
					                         ->where("product_images.product_id" , $productDetails -> product_id)
					                        ->select(
					                               DB::raw("CONCAT('".env('APP_URL')."','/public/products/',product_images.image) AS product_image")
					                            )
					                        ->first();
					            if($data){
					                $productDetails ->product_image = $data->product_image;   
					            }else{
					                $productDetails->product_image = "";
					            }

					        
 
			//get product images
		 	$productImages = DB::table('product_images')
						  ->where('products.id', $productId)
						  ->join('products', 'products.id', '=', 'product_images.product_id')
						  ->select('product_images.id', 
						  	DB::raw("CONCAT('".env('APP_URL')."','/public/products/',product_images.image) AS product_image")
						   )
						  ->get();


			//get product comments
			$comments = DB::table('product_comments')
						  ->where('products.id', $productId)
						  ->join('products', 'product_comments.product_id', '=', 'products.id')
						  ->join('users', 'product_comments.user_id', '=', 'users.user_id')
						  ->select('users.full_name',
						  	DB::raw("CONCAT('".env('APP_URL')."','/public/userProfileImages/',users.profile_pic) AS user_profile_pic"),
						    'users.user_id', 'product_comments.comment', 'product_comments.id')
						  ->get();
			$count_comment = $comments->count();
 
           
           //get prodcut options

			$options = DB::table('product_options') -> where('product_id',$productId) -> select('id','name','price') -> get();
			//get product sizes 
			$sizes = DB::table('product_sizes') -> where('product_id',$productId)-> select('id','name','price') -> get();
			//get product colors 
			$colors = DB::table('product_colors') -> where('product_id',$productId)-> select('id','name','price') -> get();

			//get user rate
			if(!empty($userId) && $userId != "0" && $userId != 0){

		 	$getUserRate = DB::table('products_rates')->where('product_id', $productId)
													   ->where('user_id', $userId)
													   ->select('rates')->first();
				if($getUserRate != NULL){
					if(!empty($getUserRate->rates)){
						$userRate = $getUserRate->rates;
					}else{
						$userRate = 0;
					}

				}else{
					$userRate = 0;
				}
			}else{
				$userRate = 0;
			}

             

             if(isset($comments) && $comments -> count() > 0){

             	foreach($comments as $comment)
             	{
                          
                          $comment -> userRate = $userRate;

             	}
             }

			 

			return response()->json(['status' => true, 'errNum' => 0,'product' => $productDetails, 'productImages' => $productImages, 'comments' => $comments,'comments_count' => $count_comment,'options' => $options ,'sizes' => $sizes,'colors' => $colors,'msg' => $msg[0]]);
		
	}



	public function like_product(Request $request){
 
 
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه المنتج للمفضله بنجاح ',
				1 => 'كل الحقول مطلوبه', 
				2 => 'كل الحقول يجب ان تكون ارقام',
				3 => 'النوع يجب ان يكون إما 1 او 2',
				4 => 'رفقم المستخدم خطأ',
				5 => 'رقم المنتج خطأ',
				6 => 'حقل المفضله مطلوب ',
 				7 => 'فشلت العملية من فضلك حاول فى وقت لاحق',
                8 => 'لابد من تسجيل الدخول اولا ',
 				9 => 'المستخدم غير موجود ',
 				10 => 'تمت الاضافه الي المفضله من قبل ',
 				11 => 'تم الحذف من المفضله من قبل ',
 				12 => 'تم حذف المنتج من المفضله ',
 
			);
		}else{
			$msg = array(
				0 => 'Product Add To Favourit successfully',
				1 => 'user access_token required', 
				2 => 'product id required',
				3 => 'product doesn\'t exists',
				4 => 'product id must be numeric',
				5 => 'Type must be 0 to dislike or 1 to like',
				6 => 'Like Field requires',
 				7 => 'Process failed, please try again later',
 				8 => 'Must be Logined first',
 				9 => 'User not found',
 				10 => 'You like this product before',
 				11 => 'You dislike this product before',
 				12 => 'Product remove from favourit successfully',


			);
		}
		
		$messages = array(
			'access_token.required'		 => 1, 
			'product_id.required'		 => 2, 
			'product_id.exists'          => 3,
			'product_id.numeric' 		 => 4,
			'like.in'  		             => 5,
			'like.required'  		     => 6,

		);



 
		$validator = Validator::make($request->all(), [
			'product_id'      => 'required|numeric|exists:products,id',
			'like'            => 'required|in:0,1'
		], $messages);


		if($validator->fails()){
			$errors = $validator->errors();
			$error  = $errors->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 



 
        $userId=0;  // return all products 
            
         if($request -> has('access_token'))
         {

         	   $rules['access_token']  ="required";

		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
		        }
        }


            if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
		        }
 

             $likeBefore = $this -> userLikeProductBefore($userId ,$request->input('product_id'));

			try {

				$data['like']    = $request->input('like');
				$data['product'] = $request->input('product_id');
				$data['user']    = $userId;

  
 					if($data['like'] == 1 || $data['like'] == "1"){

						  if($likeBefore){
						                    
			                 return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);	
			              }else{
	                            
	                            //insert data 
							DB::table('product_likes')->insert([
								'product_id' => $data['product'],
								'user_id'    => $data['user']
							]);

							//update product table 
							Product::where('id', $data['product'])->increment('likes_count', 1);

							 return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

			              }
							

					}else{
 
					            
			            if(!$likeBefore){
			                    
			                 return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);	
			            }else{

			            	 //Dislike 
							DB::table('product_likes')->where('product_id', $data['product'])->delete();
	                        
							//update product table 
							Product::where('id', $data['product'])->where('likes_count','>',0) -> decrement('likes_count', 1);

							 return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[12]]);
			            }
 
					}
					
				 

				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);	
			}
		 
	}



   public function userLikeProductBefore($userId , $product_id){

             
           $status = DB::table('product_likes') -> where(['user_id' => $userId , 'product_id' => $product_id ]) -> first();


           if($status)
           {

           	   return true; 
           }
  
           return false;
   }

	 
	public function getUserFavorites(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'رقم المستخدم مطلوب', 
				2 => 'لا يوجد بيانات بعد',
				3 => 'هذا المستخدم غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'access_token is required', 
				2 => 'There is no data yet!!',
				3 => 'the user doesn\'t exists',
			);
		}

		$messages = array(
			'access_token.required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',

		], $messages);

		if($validator->fails()){
			$errors = $validator->errors();
			$error  = $errors->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		       $userId=0;  // return all products 
              
		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		        }

         
  				$data = DB::table('product_likes')->where('product_likes.user_id',$userId)
				                               ->join('products', 'product_likes.product_id', '=', 'products.id')
				                               ->join('providers', 'products.provider_id', '=', 'providers.provider_id')
				                               ->select('products.id AS product_id','products.title', 'products.likes_count','products.product_rate', 'providers.store_name AS full_name')
				                               ->orderBy('product_likes.id', 'DESC')
				                               ->paginate(10);


 
			if(isset($data) && $data -> count()  > 0 ){

                     foreach ($data as $key => $product) {
                         

				            $images = DB::table("product_images")
			                         ->where("product_images.product_id" , $product -> product_id)
			                        ->select(
			                               DB::raw("CONCAT('".env('APP_URL')."','/public/products/',product_images.image) AS product_image")
			                            )
			                        ->first();
					            if($data){
					                $product ->product_image = $images->product_image;   
					            }else{
					                $product->product_image = "";
					            }


                     }

				return response()->json(['status' => true, 'errNum' => 0, 'favourits'=>$data, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
			}
		 
	}


  

	//method to add comment  and rate product
	public function addComment(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم إضافة التعليق',
				1 => 'التعليق والمستخدم والوجبه حقول مطلوبه',
				2 => 'التقييم و المستخدم والوجبه يجب ان يكون ارقام',
				3 => 'التعليق لا يجب ان يقل عن 3 حروف',
				4 => 'المستخدم غير صحيح', 
				5 => 'الوجبه غير صحيحة',
				6 => 'التقييم يجب ان يكون بين 1 و 5',
				7 => 'هناك خطأ ما من فضلك حاول فى وقت لاحق',
				8 => 'المستخدم غير موجود ',
				9 => 'تم تحديث التقييم بنجاح '

			);
		}else{
			$msg = array(
				0 => 'Comment added',
				1 => 'Comment field required', 
				2 => 'User access_token required', 
				3 => 'Product_id field required',
				4 => 'Rate must be 1,2,3,4,5', 
				5 => 'Comment must at lest 3 characters',
				6 => 'product doesn\'t exists', 
				7 => 'There is something wrong, please try again later',
				8 => 'User does\'t exists',
				9 => 'Your rating updatted successfully'
			);
		}

		$messages = array(
			'comment.required'        => 1, 
			'access_token.required'   => 2, 
			'product_id.required'     => 3, 
 			'rate.in' 			      => 4,
			'comment.min'             => 5,
			'product_id.exists'       => 6, 
		);

		$validator = Validator::make($request->all(),[
			'rate'            => 'sometimes|nullable|in:1,2,3,4,5',
			'comment'         => 'required|min:3',
			'access_token'    => 'required',
			'product_id'      => 'required|exists:products,id' 
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}
 
 
		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
		        }
		       
             $productRate = 0;

			try {
				$data['user_id']    = $userId;
				$data['product_id'] = $request->input('product_id');
				$data['rate'] = $request->input('rate');
				$data['comment'] = $request->input('comment');
				$productRate = 0;
				DB::transaction(function() use ($data, &$productRate){
					DB::table('product_comments')->insert([
						'product_id' => $data['product_id'],
						'comment'    => $data['comment'],
						'user_id'    => $data['user_id']
					]);
                      


                      $updated = 0;
					 
					 if($this -> UserCommentBefore($data)){
 
                      DB::table('products_rates')-> where(['product_id' => $data['product_id'], 'user_id'  => $data['user_id']]) -> update([
							'product_id' => $data['product_id'],
							'rates'      => $data['rate'] ? $data['rate'] : 0 ,
							'user_id'    => $data['user_id']
						]);

                      $updated = 1;


					 }else{

					 	DB::table('products_rates')->insert([
							'product_id' => $data['product_id'],
							'rates'      => $data['rate'] ? $data['rate'] : 0 ,
							'user_id'    => $data['user_id']
						]);

						$updated = 0;

					 }
						
						//get sum of rates and count 
						$x = DB::table('products_rates')->where('product_id', $data['product_id'])
										 ->select(DB::raw('IFNULL(SUM(rates),0) as rateSum'), DB::raw('IFNULL(COUNT(id),0) AS rateCount'))
										 ->first();
						
						if($x != NULL){
							if($x->rateCount != 0 && $x->rateCount != "0"){
								$productRate = $x->rateSum / $x->rateCount;
								$productRate = ceil($productRate);
							}else{
								$productRate = 0;
							}
						}else{
							$productRate = 0;
						}
 
						DB::table('products')->where('id', $data['product_id'])
											  ->update(['product_rate' => $productRate]);
 

				});

				if($updated = 0){

					return response()->json(['status' => true, 'errNum' => 0,'productRate' => $productRate, 'msg' => $msg[0]]);

				}

				return response()->json(['status' => true, 'errNum' => 0,'productRate' => $productRate, 'msg' => $msg[9]]);
				
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
			}
		 
	}

 
     protected function UserCommentBefore($data)
     {

     	    $commented = DB::table('products_rates')-> where(['product_id' => $data['product_id'], 'user_id'  => $data['user_id']]) -> first();

     	    if($commented)
     	    	return true;
     	    

       return false;


     }

	//add address
	public function addAdress(Request $request){
		$lang = $request->input('lang');
		
	  if($lang == "ar"){
			$msg = array(

				0 => 'تم إضافة العنوان بنجاح',
				1 => 'خانة العنوان مطلوبه',
				2 => 'ةصف العنوان  مطلوب ',
				3 => 'رقم المستخدم مطلوب',
				4 => 'الاحداثي الاول للخريطه مطلوب ',
				5 => 'الاحداثي الثاني للخريطة مطلوب ',
				6 => 'فشلت العملية من فضلك حاول مجداا ',
				7 => 'المستخدم غير موجود ',
				8 => 'رقم  الهاتف مطلوب ',
				9 => ' رقم الهاتف لابد ان يكون ارقام ',
				10 => ' كود الدولة مطلوب ',
				11 => 'صسغة هاتف غير صحيحة ',
			);
		}else{
			$msg = array(
				0 => 'Address has been added successfully',
				1 => 'Address field  is required',
				2 => 'Address description field is required',
				3 => 'access_token is required', 
				4 => 'latitude  is required ',
				5 => 'longitude is required',
				6 => 'Proccess failed please try again later',
				7 => 'User Not Found ',
				8 => 'phone number required',
				9 => 'phone number must be numeric',
				10 => 'country code required',
				11 => 'phone number format invalid'

			);
		}

		$messeges  = array(
			'address.required'       => 1, 
			'description.required'   => 2, 
			'access_token.required'  => 3, 
			'latitude.required'		 => 4,
			'longitude.required'     => 5,
			'phone.required'         => 8,
			'phone.numeric'          => 9,
			'country_code.required'  => 10,
			'phone.regex'            => 11,
 
		);
		$validator = Validator::make($request->all(), [
			'address'       => 'required',
			'description'   => 'required',
			'access_token'  => 'required',
			'latitude'      => 'required',
			'longitude'     => 'required',
			'phone'         => array('required','numeric','regex:/^(05|5)([0-9]{8})$/'),
			'country_code'  => 'required'
		], $messeges);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		        $userId     =  $this->get_id($request,'users','user_id');
		        $latitude   = $request->input('latitude');
		        $longitude  = $request->input('longitude');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
		        }

		 
			$id = DB::table('user_addresses')->insertGetId([
				      'address'      => $request->input('address'),
					  'short_desc'   => $request->input('description'),
					  'user_id'      => $userId,
					  'longitude'    => $longitude,
					  'latitude'     => $latitude,
					  'country_code' => $this -> checkCountryCodeFormate($request->input('country_code')),
					  'phone'        => $request->input('phone'),
				  ]);

			if($id){
				$addressDetail = array(
					'address'       => $request->input('address'),
					'description'   => $request->input('description'),
					'address_id'    => $id,
					'country_code'  => $this -> checkCountryCodeFormate($request->input('country_code')),
					'phone'         => $request->input('phone'),
					'longitude'     => $longitude,
					'latitude'      => $latitude
				);
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'addressDetail' => $addressDetail]);
			}else{
				return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
			}
 
	}
 

		public function checkCountryCodeFormate($str){
		     
		    	   if(mb_substr(trim($str), 0, 1) === '+'){
	                          return  $str;
	                  }
	                  
	                  return '+'.$str;	                  
		}


	//retieve user addresses
	public function getUserAddresses(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'رقم المستخدم مطلوب',
				2 => 'ألمستخدم غير موجود ',
				3 => 'لا يوجد بيانات بعد'
			);
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'access_token is required',
				2 => 'user not found',
				3 => 'There is no addresses yet'
			);
		}

		$messages = array(
			'required' => 1,
 		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		        $userId     =  $this->get_id($request,'users','user_id');

		        if($userId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		        }

			//get user addresses
			$addresses = DB::table('user_addresses')->where('user_id', $userId)
						   ->select('address_id', 'user_id', 'short_desc AS short_address_desc','country_code','phone','address','longitude', 'latitude')->get();
			if(isset($addresses)&& $addresses->count() > 0){

				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'addresses' => $addresses]);
			}else{
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		 
	}

	//delete user address 
	public function deleteUserAddress(Request $request){

		$lang = $request->input('lang');
		if($lang == 'ar'){
			$msg = array(
				0 => 'تم مسح العنوان',
				1 => 'رقم العنوان مطلوب',
				2 => 'رقم العنوان يجب ان يكون رقم',
				3 => 'رقم العنوان غير موجود',
				4 => 'فشلت العملية من فضلك حاول فى وقت لاحق'
			);
		}else{
			$msg = array(
				0 => 'Deleted successfully',
				1 => 'address_id is required',
				2 => 'address_id must be a number',
				3 => 'address_id is not exist',
				4 => 'Proccess failed, please try again later'
			);
		}

		$messages = array(
			'required' => 1,
			'numeric'  => 2,
			'exists'   => 3
		);

		$validator = Validator::make($request->all(), [
			'address_id' => 'required|numeric|exists:user_addresses,address_id'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$check = DB::table('user_addresses')->where('address_id', $request->input('address_id'))->delete();
			if($check){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}






	//method to add user order
	public function addOrder(Request $request){
	    
  
		$lang            = $request->input('lang');
		$meals           = $request->input('meals');
		$provider        = $request->input('provider_id');
		$user 	         = $request->input('user_id');
		$in_future       = $request->input('in_future');
		$address         = $request->input('address');
		$delivery_method = $request->input('delivery_method_id');
		$payment_method  = $request->input('payment_method_id');
		$delivery_time   = $request->input('delivery_time');
		$balance_flag    = $request->input('balance_flag');
		$totalQty        = 0;
		$totalPrice      = 0;
		$net 			 = 0;
		$totalValue      = 0;
		$totalDisc       = 0;
	 
		
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'لا يوجد وجبات فى الطلب',
				2 => 'خطأ فى وجبة ما',
				3 => 'خطأ فى سعر وجبة ما',
				4 => 'خطأ فى العدد من وجبة ما',
				5 => 'كل البيانات مطلوبه',
				6 => 'خانة lang يجب ان تكون واحده من (ar, en)',
				7 => 'خانة in_future يجب ان تكون واحده من (0, 1)',
				8 => 'خانة delivery_time يجب ان تكون بنستيق (Y-m-d H:i:s)',
				9 => 'حدث خطأ ما من فضلك حاول فى وقت لاحق',
				10 => 'رقم الوجبه لا يمكن ان يتكرر',
				11 => 'لا يوجد كمية كافية للعدد المطلوب فى هذه الوجبات',
				12 => 'مقدم الخدمه لا يقوم بإستلام طلبات',
				13 => 'الوجبات يجب ان تكون على شكل مصفوفه',
				14 => 'خطأ فى العنوان',
				15 => 'balance_flag يجب ان يكون 0 او 1',
				16 => '  معفوا وقت الطلب خارج مواعيد عمل التاجر  '
			);
			$push_notif_title   = "طلب جديد";
			$push_notif_message = "تم إضافة طلب جديد خاص بك";
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'There is no any meal in order',
				2 => 'There is an error in some meal',
				3 => 'There is an error in some meal price',
				4 => 'There is an error in some meal count number',
				5 => 'All data is required',
				6 => 'lang field must be one of (ar, en)',
				7 => 'in_future field must be one of (0, 1)',
				8 => 'delivery_time field must be in format (Y-m-d H:i:s)',
				9 => 'There is something wrong, please try again later',
				10 => 'meal_id can not be repeated',
				11 => 'There is no enough quantaty for these meals',
				12 => 'The provider doesn\'t receive orders',
				13 => 'Meals must be an array',
				14 => 'invalid address',
				15 => 'balance_flag must be 0 or 1',
				16 => 'The time of the request outside the merchant\'s working hour',

			);
			$push_notif_title   = "New order";
			$push_notif_message = "A new order has been added to you";
		}
 
     
        
		if(empty($meals)){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]); 
		}

		if(!is_array($meals)){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
		$mealsArr 	  = array();
        $invalidMeals = array();
		for($i = 0; $i < count($meals); $i++){
			array_push($mealsArr, $meals[$i]['meal_id']);
			if(empty($meals[$i]['meal_id'])){
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
			}

			if(empty($meals[$i]['price'])){
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}

			if(empty($meals[$i]['qty'])){
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}

			if(empty($meals[$i]['discount']) || $meals[$i]['discount'] == "0" || $meals[$i]['discount'] == ""){
				$meals[$i]['discount'] = 0;
			}

			//get meal available qty 
			$check = Meals::where('meal_id', $meals[$i]['meal_id'])
						  ->where('avail_number' , '<', $meals[$i]['qty'])
						  ->first();
			if($check != NULL){
				$invalidMeals[]['meal_id'] 		 = $check->meal_id;
				$invalidMeals[]['meal_name'] 	 = $check->meal_name;
				$invalidMeals[]['meal_qty'] 	 = $check->avail_number;
				$invalidMeals[]['requested_qty'] = $meals[$i]['qty'];
			}

			$totalQty   += $meals[$i]['qty'];
			$totalPrice += $meals[$i]['price'] * $meals[$i]['qty'];
			$totalDisc  += $meals[$i]['discount'];
			$net        += $meals[$i]['qty'] * $meals[$i]['price'];   // need to subtract the discount from the net value

		}

		$uniqueMeals = array_values(array_unique($mealsArr));
		if(count($mealsArr) != count($uniqueMeals)){
			return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
		}

		if(!empty($invalidMeals)){
			return response()->json(['status' => false, 'errNum' => 11,'msg' => $msg[11], 'invalid_meals' => $invalidMeals]);
		}

		$messages  = array(
			'required'		  => 5,
			'lang.in'         => 6,
			'in_future.in'    => 7,
			'date_format'     => 8,
			'exists'		  => 14,
			'balance_flag.in' => 15
		);

		$validator = Validator::make($request->all(), [
			'provider_id'        => 'required',
			'user_id'            => 'required',
			'in_future'          => 'required|in:0,1',
			'address'            => 'required|exists:user_addresses,address_id',
			'delivery_method_id' => 'required',
			'payment_method_id'  => 'required',
			'balance_flag' 		 => 'required|in:0,1',
			'delivery_time'      => 'required|date_format:Y-m-d H:i:s'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			//get user address data
			$userAdress = DB::table('user_addresses')->where('address_id', $address)->first();
			//chech if the provider accept orders or not
			$conditions[] = ['provider_id', '=', $request->input("provider_id")];
			$conditions[] = ['receive_orders', '=', 1];
			
			if($request->input('in_future') == 0 || $request->input('in_future') == "0"){
				$conditions[] = ['current_orders', '=', 1];
				
			 $allowedTime =	$this -> CheckWorkingHoursForProvider($provider , $delivery_time,'current_orders');
			 
			 
			 $msgTime=[];
			 
			  if($lang == "ar"){
        			$msgTime = array(
        			    
	                       0 => 'عفوا مواعيد عمل التاجر من : '  .' '.$allowedTime['providerfromTime'].  'الي :'.' '.$allowedTime['providertoTime'],

        				);
        			 
        		}else{
        			$msgTime = array(
        			 
                   	0=>  'Sorry Working Hours From: '.' '.$allowedTime['providerfromTime'].'to:' .' '.$allowedTime['providertoTime'],        			);
        		 
        		}
        		
	    if($allowedTime['status'] == false){
			     
        	  
        			      return response()->json(['status' => false, 'errNum' => 0, 'msg' => $msgTime[0]]);
			      
	     }
				 
         
			}else{
				$conditions[] = ['future_orders', '=', 1];
				
				  
			  $allowedTime = $this -> CheckWorkingHoursForProvider($provider , $delivery_time,'future_orders');
			  
			  $msgTime=[];
			  		 
			  if($lang == "ar"){
        			$msgTime = array(
        			     
        		              0 => 'عفوا مواعيد عمل التاجر من : '  .' '.$allowedTime['providerfromTime'].  'الي :'.' '.$allowedTime['providertoTime'],
        		              
                              1 => 'عفوا اقصي تاريخ لاستلام الطلبات للتاجر : '  .' '.$allowedTime['providerFutureDate'],
 
        				);
        			 
        		}else{
        			$msgTime = array(
        			 
                      	0=>  'Sorry Working Hours From: '.' '.$allowedTime['providerfromTime'].'to:' .' '.$allowedTime['providertoTime'],        		
                      	1 => 'sorry Max date For Provider is : '.' '.$allowedTime['providerFutureDate'],
                      	
                      	);
        			 
        		}
        		
					
					if($allowedTime['status'] == false && $allowedTime['type'] ='current_order'){
			       
			             return response()->json(['status' => false, 'errNum' => 0, 'msg' => $msgTime[0]]);
			      
        			 }else{
        			     
        			     return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msgTime[1]]);          
        			 }
					
					
			}
			
			$check = Providers::where($conditions)
							  ->first();
			if($check == NULL || !$check->count()){
				return response()->json(['status' => false, 'errNum' => 12,'msg' => $msg[12]]);
			}else{
				$provider_longitude      = $check->longitude;
				$provider_latitude       = $check->latitude;
				$provider_reg_id         = $check->device_reg_id;
				$provider_delivery_price = $check->delivery_price;
				$marketer_code 			 = $check->marketer_code;
				$created 				 = date('Y-m-d', strtotime($check->created_at));
			}
			$delivery_price = 0;
			$orderCode   = mt_rand();
			//get app percentage 
			$app_settings = DB::table('app_settings')->first();
			if($app_settings != NULL){
				$percentage          = $app_settings->app_percentage;
				$kilo_price          = $app_settings->kilo_price;
				$delivery_percentage = $app_settings->delivery_percentage;
				$marketer_percentage = $app_settings->marketer_percentage; 
				$initial_price       = $app_settings->initial_value_added_order_price;
				$price_outside       = $app_settings->delivery_price_outside ;
			}else{
				$percentage = 0;
				$kilo_price = 0;
				$delivery_percentage = 0;
				$marketer_percentage = 0;
                $initial_price = 0;
                $price_outside = 0;
			}

			if($delivery_method == 1){
				//calculating distance between provider and user 
				$dKilos = $this->distance($userAdress->latitude, $userAdress->longitude, $provider_latitude, $provider_longitude, false);
				$delivery_price = ROUND((($dKilos * $kilo_price) + $initial_price),2);
			}elseif($delivery_method == 4){
				$delivery_price = 0;
			}elseif($delivery_method == 2){
				$delivery_price = $provider_delivery_price;
			}else{
                $delivery_price = $price_outside;
            }
			// else if the delivery method outside add the
			$app_value          = ($net * $percentage) / 100;
			$delivery_app_value = ($delivery_price == 0) ? 0 : (($delivery_price * $delivery_percentage) / 100);
			$total_value        = $net + $delivery_price;
			$net                = $net - $app_value;
			if($marketer_code != NULL && !is_null($marketer_code) && $marketer_code != ""){
				$now = time();
				$y1 = date('y', strtotime($created));
				$y2 = date('y', $now);

				$m1 = date('m', strtotime($created));
				$m2 = date('m', $now);

				$d1 = date('d', strtotime($created));
				$d2 = date('d', $now);
				$months = (($y2 - $y1) * 12) + ($m2 - $m1) + (($d2 - $d1) / 30);
				if($months <= 1){
					$data['provider_marketer_code'] = $marketer_code;
					$provider_marketer_value = ($net * $marketer_percentage) / 100;
					$net = $net - $provider_marketer_value;
				}else{
					$data['provider_marketer_code'] = "";
					$provider_marketer_value = 0;
				}
			}else{
				$data['provider_marketer_code'] = "";
				$provider_marketer_value = 0;
			}
			//get user balance
			$getUserPoints = User::where('user_id', $user)->first();
			if($getUserPoints != NULL){
				$points = $getUserPoints->points;
			}else{
				$points = 0;
			}
			//we will set this to zero till split payement method is activated
			$split_value = 0;
			try {
				$data['totalPrice']          = $totalPrice;
				$data['totalQty']            = $totalQty;
				$data['totalDisc']           = $totalDisc;
				$data['net']			     = $net;
				$data['delivery_price']      = $delivery_price;
				$data['total_value']         = $total_value;
				$data['app_value']           = $app_value;
				$data['percentage']          = $percentage;
				$data['delivery_percentage'] = $delivery_percentage;
				$data['delivery_app_value']  = $delivery_app_value;
				$data['user'] 		         = $user;
				$data['points'] 		     = $points;
				$data['provider']            = $provider;
				$data['address']  	         = $userAdress->address;
				$data['user_longitude']      = $userAdress->longitude;
				$data['user_latitude']       = $userAdress->latitude;
				$data['delivery_time']       = $delivery_time;
				$data['payment_method']      = $payment_method;
				$data['delivery_method']     = $delivery_method;
				$data['orderCode'] 		     = $orderCode;
				$data['in_future'] 		     = $in_future;
				$data['split_value'] 	     = $split_value;
				$data['meals'] 			     = $meals;
				$data['balance_flag']        = $balance_flag;
				$userInfo = User::where('user_id', $user)->first();

				$data['phone'] = $userInfo->phone;
				$data['email'] = $userInfo->email;
				$data['marketer_percentage'] = $marketer_percentage;
				$data['provider_marketer_value'] = $provider_marketer_value;
				$id = 0;
				DB::transaction(function () use ($data, &$id) {
				    //setting order header
				    if($data['balance_flag'] == 1){
				    	if($data['points'] <= $data['total_value']){
				    		$used_points = $data['points'];
				    	}else{
				    		$used_points = $data['points'] - $data['total_value'];
				    	}
				    }else{
				    	$used_points = 0;
				    }
					$id = DB::table('orders_headers')->insertGetId([
						'total_price' 	         => $data['totalPrice'],
						'total_qty'   	         => $data['totalQty'],
						'total_value' 	         => $data['total_value'],
						'used_points' 	         => $used_points,
						'net_value' 	         => $data['net'],
						'app_percentage'         => $data['percentage'],
						'app_value' 	         => $data['app_value'],
						'delivery_price'         => $data['delivery_price'],
						'total_discount'         => $data['totalDisc'],
						'user_id'                => $data['user'],
						'provider_id'            => $data['provider'],
						'address'                => $data['address'],
						'user_latitude'          => $data['user_latitude'],
						'user_longitude'         => $data['user_longitude'],
						'user_phone'             => $data['phone'],
						'user_email'             => $data['email'],
						'expected_delivery_time' => $data['delivery_time'],
						'payment_type'           => $data['payment_method'],
						'delivery_method'        => $data['delivery_method'],
						'order_code' 			 => $data['orderCode'],
						'in_future' 			 => $data['in_future'],
						'split_value' 			 => $data['split_value'],
						'delivery_app_value' 	 => $data['delivery_app_value'],
						'delivery_app_percentage'=> $data['delivery_percentage'],
						'marketer_percentage'    => $data['marketer_percentage'], 
						'marketer_value'         => $data['provider_marketer_value'],
						'provider_marketer_code' => $data['provider_marketer_code']
					]);
					$serial = 1;
					$mealArr = $data['meals'];
					for($i = 0; $i < count($mealArr); $i++){
						DB::table('order_details')->insert([
							'order_id'   => $id, 
							'order_code' => $data['orderCode'], 
							'meal_id'    => $mealArr[$i]['meal_id'],
							'meal_price' => $mealArr[$i]['price'],
							'qty'        => $mealArr[$i]['qty'],
							'discount'   => $mealArr[$i]['discount'],
							'serial'     => $serial
						]);

						Meals::where('meal_id', $mealArr[$i]['meal_id'])
							 ->where(DB::raw('avail_number - '.$mealArr[$i]['qty']), ">=", 0)
							 ->update(['avail_number' => DB::raw('avail_number - '.$mealArr[$i]['qty'])]);

						$serial++;
					}

					//removing user balance 
					if($data['balance_flag'] == 1){
						User::where('user_id', $data['user'])->update(['points' => $used_points]);
					}
				});

				$notif_data = array();
				$notif_data['title']      = $push_notif_title;
			    $notif_data['message']    = $push_notif_message;
			    $notif_data['order_id'] 	      = $id;
			    $notif_data['notif_type'] = 'order';
			    $provider_token = Providers::where('provider_id', $data['provider'])->first();
				if($provider_token != NULL){
			    	$push_notif = $this->singleSend($provider_token->device_reg_id, $notif_data, $this->provider_key);
			    }
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0],'order_code' => $orderCode, 'order_id' => $id]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
			}
		}
	}
	
 
	public function  CheckWorkingHoursForProvider($providerId , $delivery_time,$type){
	    
	    
	    
	    $result= array();
	      
	      $provider = Providers::where('provider_id',$providerId) -> first();
	         
          $provider_future_date = $provider -> avail_date;
          
          $delivery_date_time        = DateTime::createFromFormat("Y-m-d H:i:s", $delivery_time);
             
          $delivery_timenew = $delivery_date_time ->format("H-i-s");
          $delivery_date    = $delivery_date_time ->format("Y-m-d");
           
          $start = $provider -> 	allowed_from_time;
	      $end   = $provider -> 	allowed_to_time;
	      
            if ( $start == null ) $start = '09:00';
            if ( $end == null )   $end   = '23:30';
             
              $result['providerfromTime']      = $start;
              $result['providertoTime']        = $end;
              $result['providerFutureDate']    = $provider_future_date;
              $result['type']                  = 'current_order';
              $result['status']                = true;
              
             
             if($type == 'future_order')
             {
                 
                        if($delivery_date  <=  $provider_future_date ){
                    
                                     $result['status']            = true;
                                     $result['type']             ='future_order';
                                    
                                 return $result;
                                 
                             }else{
                                 
                                 $result['status']            = false;
                                  $result['type']             ='future_order';
                                 
                                 
                                 return $result;
                             }
         
             }
            
         
           
             if($start <=  $delivery_timenew  && $delivery_timenew  <= $end ){
                    
                    $result['status']            = true;
                    
                 return $result;
             }
         
	         
	          $result['status']  = false;
                    
                return $result;
                 
                 
 	    
	}
	

	public function addVisitorOrder(Request $request){
		$lang                 = $request->input('lang');
		$meals                = $request->input('meals');
		$provider             = $request->input('provider_id');
		$in_future            = $request->input('in_future');
		$address              = $request->input('address');
		$delivery_method      = $request->input('delivery_method_id');
		$payment_method       = $request->input('payment_method_id');
		$delivery_time        = $request->input('delivery_time');
		$visitor_phone        = $request->input('visitor_phone');
		$visitor_country_code = $request->input('visitor_country_code');
		$visitor_address      = $request->input('visitor_address');
		$visitor_address_lat  = $request->input('visitor_address_lat');
		$visitor_address_long = $request->input('visitor_address_long');
		$visitor_email	      = $request->input('visitor_email');
		$visitor_id 		  = $request->input('visitor_id');
		$totalQty             = 0;
		$totalPrice           = 0;
		$net 			      = 0;
		$totalValue           = 0;
		$totalDisc            = 0;

		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'لا يوجد وجبات فى الطلب',
				2 => 'خطأ فى وجبة ما',
				3 => 'خطأ فى سعر وجبة ما',
				4 => 'خطأ فى العدد من وجبة ما',
				5 => 'كل البيانات مطلوبه',
				6 => 'خانة lang يجب ان تكون واحده من (ar, en)',
				7 => 'خانة in_future يجب ان تكون واحده من (0, 1)',
				8 => 'خانة delivery_time يجب ان تكون بنستيق (Y-m-d H:i:s)',
				9 => 'حدث خطأ ما من فضلك حاول فى وقت لاحق',
				10 => 'رقم الوجبه لا يمكن ان يتكرر',
				11 => 'لا يوجد كمية كافية للعدد المطلوب فى هذه الوجبات',
				12 => 'مقدم الخدمه لا يقوم بإستلام طلبات',
				13 => 'الوجبات يجب ان تكون على شكل مصفوفه',
				14 => 'خطأ فى العنوان',
				15 => 'خطأ فى صيغة البريد الإلكترونى',
				16 => 'هذا البريد الإلكترونى مستخدم من قبل',
				17 => 'رقم الجوال مستخدم من قبل'
			);
			$push_notif_title   = "طلب جديد";
			$push_notif_message = "تم إضافة طلب جديد خاص بك";
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'There is no any meal in order',
				2 => 'There is an error in some meal',
				3 => 'There is an error in some meal price',
				4 => 'There is an error in some meal count number',
				5 => 'All data is required',
				6 => 'lang field must be one of (ar, en)',
				7 => 'in_future field must be one of (0, 1)',
				8 => 'delivery_time field must be in format (Y-m-d H:i:s)',
				9 => 'There is something wrong, please try again later',
				10 => 'meal_id can not be repeated',
				11 => 'There is no enough quantaty for these meals',
				12 => 'The provider doesn\'t receive orders',
				13 => 'Meals must be an array',
				14 => 'invalid address',
				15 => 'Invalid e-mail format',
				16 => 'This e-mail is used before',
				17 => 'This phone number is used before'
			);
			$push_notif_title   = "New order";
			$push_notif_message = "A new order has been added to you";
		}

		if(empty($meals)){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}

		if(!is_array($meals)){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
		$mealsArr 	  = array();
		$invalidMeals = array();
		for($i = 0; $i < count($meals); $i++){
			array_push($mealsArr, $meals[$i]['meal_id']);
			if(empty($meals[$i]['meal_id'])){
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
			}

			if(empty($meals[$i]['price'])){
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}

			if(empty($meals[$i]['qty'])){
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}

			if(empty($meals[$i]['discount']) || $meals[$i]['discount'] == "0" || $meals[$i]['discount'] == ""){
				$meals[$i]['discount'] = 0;
			}

			//get meal available qty 
			$check = Meals::where('meal_id', $meals[$i]['meal_id'])
						  ->where('avail_number' , '<', $meals[$i]['qty'])
						  ->first();
			if($check != NULL){
				$invalidMeals[]['meal_id'] 		 = $check->meal_id;
				$invalidMeals[]['meal_name'] 	 = $check->meal_name;
				$invalidMeals[]['meal_qty'] 	 = $check->avail_number;
				$invalidMeals[]['requested_qty'] = $meals[$i]['qty'];
			}

			$totalQty   += $meals[$i]['qty'];
			$totalPrice += $meals[$i]['price'];
			$totalDisc  += $meals[$i]['discount'];
			$net        += $meals[$i]['qty'] * $meals[$i]['price'];
		}

		

		$uniqueMeals = array_values(array_unique($mealsArr));

		if(count($mealsArr) != count($uniqueMeals)){
			return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
		}

		if(!empty($invalidMeals)){
			return response()->json(['status' => false, 'errNum' => 11,'msg' => $msg[11], 'invalid_meals' => $invalidMeals]);
		}

		$messages  = array(
			'required'		 	   => 5,
			'lang.in'        	   => 6,
			'in_future.in'   	   => 7,
			'date_format'    	   => 8,
			'exists'		 	   => 14,
			'email'          	   => 15,
			'visitor_email.unique' => 16,
			'visitor_phone.unique' => 17
		);

		$validator = Validator::make($request->all(), [
			'provider_id'          => 'required',
			'visitor_email'        => 'required|email|unique:users,email',
			'visitor_phone'        => 'required|unique:users,phone',
			'visitor_country_code' => 'required',
			'visitor_address'      => 'required',
			'visitor_address_lat'  => 'required',
			'visitor_address_long' => 'required',
			'in_future'            => 'required|in:0,1',
			'delivery_method_id'   => 'required',
			'payment_method_id'    => 'required',
			'delivery_time'        => 'required|date_format:Y-m-d H:i:s'
		], $messages);

		$flag = 1;
		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			if(!empty($visitor_id) && $visitor_id != 0 && $visitor_id != "0"){
				if($error == 16 || $error == 17){
					//get user phone and email
					$visitorData = DB::table('users')->where('user_id', $visitor_id)->select('email', 'phone')->first();
					if($visitorData != NULL){
						if($visitor_email == $visitorData->email && $visitor_phone == $visitorData->phone){
							$flag = 2;
						}else{
							return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
						}
					}else{
						return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
					}
				}else{
					return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
				}
			}else{
				return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
			}
		}

		//get user address data
		// $userAdress = DB::table('user_addresses')->where('address_id', $address)->first();
		//chech if the provider accept orders or not
		$conditions[] = ['provider_id', '=', $request->input("provider_id")];
		$conditions[] = ['receive_orders', '=', 1];
		if($request->input('in_future') == 0 || $request->input('in_future') == "0"){
			$conditions[] = ['current_orders', '=', 1];
		}else{
			$conditions[] = ['future_orders', '=', 1];
		}
		$check = Providers::where($conditions)
						  ->first();
		if($check == NULL || !$check->count()){
			return response()->json(['status' => false, 'errNum' => 12,'msg' => $msg[12]]);
		}else{
			$provider_longitude      = $check->longitude;
			$provider_latitude       = $check->latitude;
			$provider_reg_id         = $check->device_reg_id;
			$provider_delivery_price = $check->delivery_price;
			$marketer_code 			 = $check->marketer_code;
			$created 				 = date('Y-m-d', strtotime($check->created_at));
		}
		$delivery_price = 0;
		$orderCode   = mt_rand();
		//get app percentage 
		$app_settings = DB::table('app_settings')->first();
		if($app_settings != NULL){
			$percentage          = $app_settings->app_percentage;
			$kilo_price          = $app_settings->kilo_price;
			$delivery_percentage = $app_settings->delivery_percentage;
			$marketer_percentage = $app_settings->marketer_percentage; 
		}else{
			$percentage = 0;
			$kilo_price = 0;
			$delivery_percentage = 0;
			$marketer_percentage = 0;
		}

		if($delivery_method == 1){
			//calculating distance between provider and user 
			$dKilos = $this->distance($visitor_address_lat, $visitor_address_long, $provider_latitude, $provider_longitude, false);
			$delivery_price = ROUND(($dKilos * $kilo_price),2);
		}elseif($delivery_method == 5){
			$delivery_price = 0;
		}else{
			$delivery_price = $provider_delivery_price;
		}
		$app_value          = ($net * $percentage) / 100;
		$delivery_app_value = ($delivery_price == 0) ? 0 : (($delivery_price * $delivery_percentage) / 100);
		$total_value        = $net + $delivery_price;
		$net                = $net - $app_value;  
		if($marketer_code != NULL && !is_null($marketer_code) && $marketer_code != ""){
			$now = time();
			$y1 = date('y', strtotime($created));
			$y2 = date('y', $now);

			$m1 = date('m', strtotime($created));
			$m2 = date('m', $now);

			$d1 = date('d', strtotime($created));
			$d2 = date('d', $now);
			$months = (($y2 - $y1) * 12) + ($m2 - $m1) + (($d2 - $d1) / 30);
			if($months <= 1){
				$data['provider_marketer_code'] = $marketer_code;
				$provider_marketer_value = ($net * $marketer_percentage) / 100;
				$net = $net - $provider_marketer_value;
			}else{
				$data['provider_marketer_code'] = "";
				$provider_marketer_value = 0;
			}
		}else{
			$data['provider_marketer_code'] = "";
			$provider_marketer_value = 0;
		}
		//we will set this to zero till split payement method is activated
		$split_value = 0;
		try {
			$data['totalPrice']          = $totalPrice;
			$data['totalQty']            = $totalQty;
			$data['totalDisc']           = $totalDisc;
			$data['net']			     = $net;
			$data['delivery_price']      = $delivery_price;
			$data['total_value']         = $total_value;
			$data['app_value']           = $app_value;
			$data['percentage']          = $percentage;
			$data['delivery_percentage'] = $delivery_percentage;
			$data['delivery_app_value']  = $delivery_app_value;
			$data['provider']            = $provider;
			$data['delivery_time']       = $delivery_time;
			$data['payment_method']      = $payment_method;
			$data['delivery_method']     = $delivery_method;
			$data['orderCode'] 		     = $orderCode;
			$data['in_future'] 		     = $in_future;
			$data['split_value'] 	     = $split_value;
			$data['meals'] 			     = $meals;
			$data['visitor_address']     = $visitor_address;
			$data['visitor_longitude']   = $visitor_address_long;
			$data['visitor_latitude']    = $visitor_address_lat;
			$data['visitor_country_code']= $visitor_country_code;
			$data['visitor_phone']       = $visitor_phone;
			$data['visitor_email']       = $visitor_email;
			$data['marketer_percentage'] = $marketer_percentage;
			$data['provider_marketer_value'] = $provider_marketer_value;
			$data['lang']                    = $lang;
			$data['flag']                    = $flag;
			$data['visitor_id']              = $visitor_id;
			$id = 0;
			$visitor_id = 0;
			$visitor_name = "";
			DB::transaction(function () use ($data, &$id, &$visitor_id, &$visitor_name) {
				$invitation_code = str_random(7);
				if($data['flag'] == 1){
					if($data['lang'] == 'ar'){
						$default_name = 'زائر-'.time();
					}else{
						$default_name = 'visitor-'.time();
					}
				}else{
					$default_name = "";
				}
				

				if($data['flag'] == 1){
					//adding visitor data to users table 
					$user = DB::table('users')->insertGetId([
						'full_name'    => $default_name,
						'email'        => $data['visitor_email'],
						'phone'        => $data['visitor_phone'],
						'country_code' => $data['visitor_country_code'],
						'invitation_code' => $invitation_code,
						'type' 			  => 2,
						'status' 		  => 1,
						'profile_pic'     => url('userProfileImages/avatar_ic.png')
					]);
				}else{
					$user = $data['visitor_id'];
				}

				if($data['flag'] == 1){
					//adding user address
					DB::table('user_addresses')->insert([
						'user_id' => $user,
						'address' => $data['visitor_address'],
						'short_desc' => substr($data['visitor_address'], 0, 5),
						'longitude'     => $data['visitor_longitude'],
						'latitude'      => $data['visitor_latitude'],
					]);
				}
			    //setting order header
				$id = DB::table('orders_headers')->insertGetId([
					'total_price' 	 => $data['totalPrice'],
					'total_qty'   	 => $data['totalQty'],
					'total_value' 	 => $data['total_value'],
					'net_value' 	 => $data['net'],
					'app_percentage' => $data['percentage'],
					'app_value' 	 => $data['app_value'],
					'delivery_price' => $data['delivery_price'],
					'total_discount' => $data['totalDisc'],
					'user_id'        => $user,
					'provider_id'    => $data['provider'],
					'address'        => $data['visitor_address'],
					'user_latitude'  => $data['visitor_latitude'],
					'user_longitude' => $data['visitor_longitude'],
					'user_phone'     => $data['visitor_phone'],
					'user_email'     => $data['visitor_email'],
					'expected_delivery_time' => $data['delivery_time'],
					'payment_type'           => $data['payment_method'],
					'delivery_method'        => $data['delivery_method'],
					'order_code' 			 => $data['orderCode'],
					'in_future' 			 => $data['in_future'],
					'split_value' 			 => $data['split_value'],
					'delivery_app_value' 	 => $data['delivery_app_value'],
					'delivery_app_percentage'=> $data['delivery_percentage'],
					'marketer_percentage'    => $data['marketer_percentage'], 
					'marketer_value'         => $data['provider_marketer_value'],
					'provider_marketer_code' => $data['provider_marketer_code']
				]);
				$serial = 1;
				$mealArr = $data['meals'];
				for($i = 0; $i < count($mealArr); $i++){
					DB::table('order_details')->insert([
						'order_id'   => $id, 
						'order_code' => $data['orderCode'], 
						'meal_id'    => $mealArr[$i]['meal_id'],
						'meal_price' => $mealArr[$i]['price'],
						'qty'        => $mealArr[$i]['qty'],
						'discount'   => $mealArr[$i]['discount'],
						'serial'     => $serial
					]);

					Meals::where('meal_id', $mealArr[$i]['meal_id'])
						 ->where(DB::raw('avail_number - '.$mealArr[$i]['qty']), ">=", 0)
						 ->update(['avail_number' => DB::raw('avail_number - '.$mealArr[$i]['qty'])]);

					$serial++;
				}
				$visitor_id = $user;
				$visitor_name = $default_name;
			});

			$notif_data = array();
			$notif_data['title']      = $push_notif_title;
		    $notif_data['message']    = $push_notif_message;
		    $notif_data['order_id'] 	      = $id;
		    $notif_data['notif_type'] = 'order';
		    $provider_token = Providers::where('provider_id', $data['provider'])->first();
		    if($provider_token != NULL){
		    	$push_notif = $this->singleSend($provider_token->device_reg_id, $notif_data, $this->provider_key);
		    }
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0],'order_code' => $orderCode, 'order_id' => $id, 'visitor_id'=>$visitor_id, 'visitor_pic' => url('userProfileImages/avatar_ic.png'), 'visitor_name' => $visitor_name]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
		}
		
	}

	public function getUserOrders(Request $request){
		$user = $request->input('user_id');
		$lang = $request->input('lang');
		$messages = array(
			'required' => 2,
			'numeric'  => 3,
			'exists'   => 4
		);

		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات بعد',
				2 => 'رقم المستخدم مطلوب',
				3 => 'رقم المستخدم يجب ان يكون رقم',
				4 => 'رقم المستخدم غير موجود'
			);
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no orders yet!!',
				2 => 'user_id is required',
				3 => 'user_id must be a number',
				4 => 'user_id is not exist'
			);
		}
		$validator = Validator::make($request->all(), [
			'user_id' => 'required|numeric|exists:users,user_id'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$counter = 0;
			//get current users order
			$today = date('Y-m-d');
			if($lang == "ar"){
				$status_col = 'order_status.ar_desc AS order_status';
			}else{
				$status_col = 'order_status.en_desc AS order_status';
			}

//			$CurrentOrders = DB::table('orders_headers')
//							    ->where('orders_headers.user_id', $user)
//							    ->whereNotIn('orders_headers.status_id', [4,5,6,7,9])
//							    ->where(DB::raw('DATE(orders_headers.expected_delivery_time)'), '<=',$today)
//								->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
//								->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
//                                //->join("complains" , "complains.order_id" , "orders_headers.order_id")
//								->select('orders_headers.order_id', 'orders_headers.order_code', 'orders_headers.total_value',
//									     'providers.provider_id', 'orders_headers.delivery_id','providers.brand_name AS provider', 'providers.provider_rate', 'providers.profile_pic',
//										 $status_col, DB::raw('IFNULL(orders_headers.delivered_at, "") AS delivered_at'), 'orders_headers.created_at', DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.created_at) AS created_time'))
//								->orderBy('orders_headers.order_id', 'DESC')
//								->get();
         	$CurrentOrders = DB::table('orders_headers')
							    ->where('orders_headers.user_id', $user)
							    ->whereNotIn('orders_headers.status_id', [4,5,6,7,9])
							    ->where(DB::raw('DATE(orders_headers.expected_delivery_time)'), '<=',$today)
								->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
								->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
                                //->join("complains" , "complains.order_id" , "orders_headers.order_id")
								->select('orders_headers.order_id', 'orders_headers.order_code', 'orders_headers.total_value',
									     'providers.provider_id', 'orders_headers.delivery_id','providers.brand_name AS provider', 'providers.provider_rate', 'providers.profile_pic',
										 $status_col,'orders_headers.expected_delivery_time AS delivered_at' , DB::raw('DATE(orders_headers.expected_delivery_time) AS delivered_date') , DB::raw('TIME(orders_headers.expected_delivery_time) AS delivered_time') , 'orders_headers.created_at', DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.created_at) AS created_time'))
								->orderBy('orders_headers.order_id', 'DESC')
								->get();

			//$CurrentOrders["is_user_complain_provider"] = flase;
			if($CurrentOrders->count()){
				$counter++;
			}

            if($CurrentOrders !== null){

                for($i = 0 ; $i <= count($CurrentOrders) -1 ; $i++){
                    $CurrentOrders[$i]->is_user_complain_provider = false;
                }
            }

			//get future users order
			$futureOrders  = DB::table('orders_headers')
			 				    ->where('orders_headers.user_id', $user)
							    ->whereNotIn('orders_headers.status_id', [4,5,6,7,9])
							    ->where(DB::raw('DATE(orders_headers.expected_delivery_time)'), '>', $today)
								->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
								->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
								->select('orders_headers.order_id', 'orders_headers.order_code', 'orders_headers.total_value',
									     'providers.provider_id','orders_headers.delivery_id','providers.brand_name AS provider', 'providers.provider_rate', 'providers.profile_pic',
										 $status_col, 'orders_headers.expected_delivery_time AS delivered_at' , DB::raw('DATE(orders_headers.expected_delivery_time) AS delivered_date') , DB::raw('TIME(orders_headers.expected_delivery_time) AS delivered_time'), 'orders_headers.created_at', DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.created_at) AS created_time'))
								->orderBy('orders_headers.order_id', 'DESC')
								->get();
			if($futureOrders->count()){
				$counter++;
			}

            if($futureOrders !== null){

                for($i = 0 ; $i <= count($futureOrders) -1 ; $i++){
                    $futureOrders[$i]->is_user_complain_provider = false;
                }
            }
			//get Finished orders
			$finishedOrders = DB::table('orders_headers')
			 				    ->where('orders_headers.user_id', $user)
							    ->whereIn('orders_headers.status_id', [4,5,6,7,9])
								->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
								->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
								->select('orders_headers.order_id', 'orders_headers.order_code', 'orders_headers.total_value',
									     'providers.provider_id','orders_headers.delivery_id','providers.full_name AS provider', 'providers.provider_rate', 'providers.profile_pic',
										 $status_col, DB::raw('IFNULL(orders_headers.delivered_at, "") AS delivered_at') , DB::raw('DATE(orders_headers.delivered_at) AS delivered_date') , DB::raw('TIME(orders_headers.delivered_at) AS delivered_time') , 'orders_headers.created_at', DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.created_at) AS created_time'))
								->orderBy('orders_headers.order_id', 'DESC')
								->get();
			if($finishedOrders->count()){
				$counter++;
			}

            if($finishedOrders !== null){

                for($i = 0 ; $i <= count($finishedOrders) -1 ; $i++){
                    $order_id_order_list = $finishedOrders[$i]->order_id;
                    $provider_order_list = $finishedOrders[$i]->provider_id;
                    $order_delivered_at = $finishedOrders[$i]->delivered_at;

                    $complain = DB::table("complains")
                                ->where("order_id" , $order_id_order_list)
                                ->where("provider_id" , $provider_order_list)
                                ->where("app" , "user")
                                ->where("user_id" , $request->input("user_id"))
                                ->first();
                    if($complain !== null){
                        $finishedOrders[$i]->is_user_complain_provider = true;
                    }else{
                        $finishedOrders[$i]->is_user_complain_provider = false;
                    }

                    if($order_delivered_at == null){
                        $finishedOrders[$i]->delivered_date = "";
                        $finishedOrders[$i]->delivered_time = "";
                    }
                }
            }

			if($counter > 0){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'currentOrders' => $CurrentOrders, 'futureOrders' => $futureOrders, 'finishedOrders' => $finishedOrders]);
			}else{
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}
		}
	}



	public function repareLikesAndFollowers(Request $request){
		$type = $request->input('type');
		$pass = $request->input('pass');

		if($pass == "Moh@mmed"){
			//1 for likes && 2 for followers
			if($type == 1 || $type == "1"){
				$meals = Meals::all();
				if($meals->count()){
					foreach($meals AS $meal){
						$likesCount = DB::table('meal_likes')->where('meal_id', $meal->meal_id)->count();

						if($likesCount){
							$count = $likesCount;
						}else{
							$count = 0;
						}
						Meals::where('meal_id', $meal->meal_id)->update(['likes_count' => $count]);
					}
				}

				return response()->json(['status' => true]);
			}elseif($type == 2 || $type == "2"){
				$providers = Providers::all();
				if($providers->count()){
					foreach($providers AS $provider){
						$followersCount = DB::table('providers_followers')->where('provider_id', $provider->provider_id)->count();
						if($followersCount){
							$count = $followersCount;
						}else{
							$count = 0;
						}

						DB::table('providers')->where('provider_id', $provider->provider_id)->update(['followers_count' => $count]);
					}
				}

				return response()->json(['status' => true]);
			}else{
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => 'Type must be 1 or 2']);
			}
		}else{
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => 'You are not allowed to access this API']);
		}
	}

	

	public function delivery_evaluate(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التقييم بنجاح',
				1 => 'كل التقييم مطلوب',
				2 => 'يجب ان ينحصر قيمة التقييم بين ال 1 و ال 5',
				3 => 'فشل التقييم من فضلك حاول فى وقت لاحق',
				4 => 'خانة التعليق لا يجب ان تزيد عن 140 حرف'
			);
		}else{
			$msg = array(
				0 => 'Evaluated successfully',
				1 => 'All the evaluation fileds are required',
				2 => 'Evaluate values must be between 1 and 5',
				3 => 'Failed to evaluate, please try again later',
				4 => 'Comment can not be more than 140 characters'
			);
		}

		$messages = array(
			'required' => 1,
			'in'       => 1,
			'max'      => 4
		);

		$validator = Validator::make($request->all(), [
			'arrival'  => 'required|in:1,2,3,4,5',
			'inTime'   => 'required|in:1,2,3,4,5',
			'attitude' => 'required|in:1,2,3,4,5',
			'comment'  => 'max:140',
			'user_id'  => 'required',
			'order_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			//get delivery 
			$delivery = DB::table('orders_headers')->where('order_id', $request->input('order_id'))->select('delivery_id')->first();
			if($delivery != NULL){
				if($delivery->delivery_id != 0 && $delivery->delivery_id != "0" && !is_null($delivery->delivery_id) && !empty($delivery->delivery_id)){
					$data['delivery_id'] = $delivery->delivery_id;
				}else{
					return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
				}
			}else{
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}

			$data['arrival']  = $request->input('arrival');
			$data['inTime']   = $request->input('inTime');
			$data['attitude'] = $request->input('attitude');
			$data['comment']  = $request->input('comment');
			$data['orderId']  = $request->input('order_id');
			$data['userId']    = $request->input('user_id');

			$sumOfDelivery = $data['arrival'] + $data['inTime'] + $data['attitude'];

			$data['deliveryAverage'] = $sumOfDelivery / 3;

			try {
					DB::transaction(function() use ($data){
						DB::table('delivery_evaluation')->insert([
							'delivery_arrival' => $data['arrival'],
							'delivery_in_time' => $data['inTime'],
							'delivery_attitude'=> $data['attitude'],
							'comment' 		   => $data['comment'],
							'user_id'          => $data['userId'],
							'order_id'         => $data['orderId'],
							'delivery_id'      => $data['delivery_id']
						]);

						
						DB::table('deliveries_rates')->insert([
							'order_id'    => $data['orderId'],
							'delivery_id' => $data['delivery_id'],
							'user_id'     => $data['userId'],
							'rates'       => $data['deliveryAverage']
						]);

						//get sum and count for the provider
						$getData = DB::table('deliveries_rates')
						             ->where('delivery_id', $data['delivery_id'])
						             ->select(DB::raw('count(id) AS c'), DB::raw('sum(rates) as s'))
						             ->first();
						if($getData != NULL){
							$sum   = $getData->s;
							$count = $getData->c;
							if($count != 0){
								$overAllRate = (int)$sum / (int)$count;
								DB::table('deliveries')->where('delivery_id', $data['delivery_id'])->update(['delivery_rate' => $overAllRate]);
							}
						}

					});
					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
				}	
		}
	}

	//method to add attachment
	public function addAttach(Request $request){
		$data      = $request->input('image_str');
		$image_ext = $request->input('image_ext');
		$attachId  = $request->input('attach_no');
		$lang      = $request->input('lang');
		if(empty($attachId) || $attachId == "" || $attachId == NULL || $attachId == "0"){
			$attachId = 0;
		}
		if($lang == "ar"){
			$msg = array(
				0 => 'تم إضافة المرفق',
				1 => 'يجب رفع الصوره',
				2 => 'يجب تحديد نوع الصوره',
				3 => 'رقم المرفق يجب ان يكون رقم',
				4 => 'فشل إضافة المرفق من فضلك حاول فى وقت لاحق',
				5 => 'فشل فى إضافة المرفق',
				6 => 'رقم المرفق مطلوب'
			);
		}else{
			$msg = array(
				0 => 'uploaded successfully',
				1 => 'Image is required',
				2 => 'image_ext is required',
				3 => 'attach_no must be number',
				4 => 'Failed to upload please try again later',
				5 => 'Failed to add the file',
				6 => 'attach_no is required'
			);
		}

		$messages  = array(
			'image_str.required' => 1,
			'image_ext.required' => 2, 
			'attach_no.numeric'  => 3,
			'attach_no.required' => 6
		);

		$validator = Validator::make($request->all(), [
			'image_str' => 'required',
			'image_ext' => 'required',
			'attach_no' => 'required|numeric',
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$image = $this->saveImage($data, $image_ext, 'complainsImages/');
			if($image == ""){
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}else{
				$imageName = explode("/", $image)[1];
				$image = url($image);
			}

			//get attach_no
			if($attachId == 0){
				$attach = DB::table('attachments')->select(DB::raw("(SELECT (IFNULL(MAX(attach_id),0) + 1) FROM attachments) AS newAttachId"))->first()->newAttachId;
			}else{
				$attach = $attachId;
			}

			$id = DB::table('attachments')->insertGetId([
				'attach_id' => $attach,
				'attach_name' => $imageName,
				'attach_path' => $image
			]);

			if($id){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'file_id' => $id, 'attach_no' => $attach]);
			}else{
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}
		}
	}

	//method to delete the attachment 
	public function deleteAttach(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم حذف المرفق',
				1 => 'رقم الملف مطلوب',
				2 => 'رقم الملف يجب ان يكون رقم',
				3 => 'رقم المرفقات مطلوب',
				4 => 'رقم المرفقات يجب ان يكون رقم',
				5 => 'فشل فى حذف الملف من فضلك حاول فى وقت لاحق'
			);
		}else{
			$msg = array(
				0 => 'Removed successfully',
				1 => 'file_id is required',
				2 => 'file_id must be a number',
				3 => 'attach_no is required',
				4 => 'attach_no must be a number',
				5 => 'Failed to remove the file, please try again later'
			);
		}

		$messages  = array(
			'file_id.required'   => 1,
			'file_id.numeric'    => 2,
			'attach_no.required' => 3,
			'attach_no.numeric'  => 4
		);

		$validator = Validator::make($request->all(), [
			'file_id'   => 'required|numeric',
			'attach_no' => 'required|numeric'
		],$messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$data['file_id']   = $request->input('file_id');
			$data['attach_no'] = $request->input('attach_no');
			$newAttachNo = $request->input('attach_no');
			DB::transaction(function() use ($data, &$newAttachNo){
				DB::table('attachments')->where('id', $data['file_id'])->delete();
				$check = DB::table('attachments')->where('attach_id', $data['attach_no'])->get();
				if(!$check->count()){
					$newAttachNo = 0;
				}
			});

			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'attach_no' => $newAttachNo]);
		}
	}

	//method to add complain
	// public function addComplain(Request $request){
	// 	$lang = $request->input('lang');
	// 	if($lang == "ar"){
	// 		$msg = array(
	// 			0 => 'تم عمل الشكوى بنجاح',
	// 			1 => 'خطأ فى البيانات المرسله',
	// 			2 => 'فشلت إضافة الشكوى من فضلك حاول فى وقت لاحق',
	// 		);
	// 	}else{
	// 		$msg = array(
	// 			0 => 'Process done successfully',
	// 			1 => 'Invalid parameters',
	// 			2 => 'Process failed, please try again later'
	// 		);
	// 	}

	// 	$messages = array(
	// 		'required' => 1,
	// 		'numeric'  => 1
	// 	);

	// 	$validator = Validator::make($request->all(), [
	// 		'provider_id' => 'required|numeric|exists:providers,provider_id',
	// 		'order_id'    => 'required|numeric|exists:orders_headers,order_id',
	// 		'attach_no'   => 'sometimes|nullable|numeric',
	// 		'complain'    => 'required',
	// 		'user_id'     => 'required|numeric|exists:users,user_id'
	// 	], $messages);

	// 	if($validator->fails()){
	// 		$error = $validator->errors()->first();
	// 		return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
	// 	}else{
	// 		DB::table('complains')->insert([
	// 			'user_id'     => $request->input('user_id'),
	// 			'provider_id' => $request->input('provider_id'),
	// 			'order_id'    => $request->input('order_id'),
	// 			'complain'    => $request->input('complain'),
	// 			'attach_no'   => $request->input('attach_no')
	// 		]);

	// 		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
	// 	}
	// }

	public function addComplain(Request $request){
		$lang = $request->input('lang');
		$attachId = 0;
		if($lang == "ar"){
			$msg = array(
				0 => 'تم عمل الشكوى بنجاح',
				1 => 'خطأ فى البيانات المرسله',
				2 => 'فشلت إضافة الشكوى من فضلك حاول فى وقت لاحق',
				3 => 'عدد الملفات المرسله يجب ان يساوى عدد الإمتدادات المرسله',
				4 => 'فشل فى رفع المفلات من فضلك حاول فى وقت لاحق',
				5 => 'الصور المرسله يجب ان تكون فى مصفوفه',
                6 => 'لديك شكوى سابقة على هذا الطلب'
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'Invalid parameters',
				2 => 'Process failed, please try again later',
				3 => 'count of files must be equal to count of extensions',
				4 => 'Failed to upload files, please try again later',
				5 => 'Files suppose to be an array, string received',
                6 => 'you already have previouse complain'
			);
		}

		$messages = array(
			'required' => 1,
			'numeric'  => 1,
			'exists'   => 1,
			'in'	   => 1
		);

		$validator = Validator::make($request->all(), [
			'subject_id' => 'required|numeric',
			'order_id'   => 'required|numeric|exists:orders_headers,order_id',
			'complain'   => 'required',
			'actor_id'   => 'required|numeric',
			'complaint'	 => 'required|in:provider,delivery',
			'app' 		 => 'required|in:user,provider'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			if($request->input('subject_id') == 0 || $request->input('subject_id') == "0"){
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}
			$files      = $request->input('files');
			$extentions = $request->input('extentions');
			$types      = $request->input('types');

			if(!empty($files)){
				if(is_array($files)){
					if(count($files) != count($extentions)){
						return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
					}

					if(count($files) != count($types)){
						return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
					}
					$counter = 0;
					foreach($files AS $value){
						if($types[$counter] == "image"){
							$file = $this->saveImage($value, $extentions[$counter], 'complainsImages/');
						}else{
							$file = $this->saveVideo($value, 'complainsImages/');
						}

						if($file == ""){
							return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
						}else{
							$fileName = explode("/", $file)[1];
							$file = url($file);
						}

						//get attach_no
						if($attachId == 0){
							$attachId = DB::table('attachments')->select(DB::raw("(SELECT (IFNULL(MAX(attach_id),0) + 1) FROM attachments) AS newAttachId"))->first()->newAttachId;
						}

						$check = DB::table('attachments')->insert([
							'attach_id'   => $attachId,
							'attach_name' => $fileName,
							'attach_path' => $file
						]);
						$counter++;
					}
				}else{					
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
				}
			}
			$inserts = array();
			if($request->input('complaint') == 'provider'){
				$inserts['provider_id'] = $request->input('subject_id'); 
			}else{
				$inserts['delivery_id'] = $request->input('subject_id');
			}
			if($request->input('app') == 'user'){
				$inserts['user_id'] = $request->input('actor_id');
			}else{
				$inserts['provider_id'] = $request->input('actor_id');
			}

			$inserts['app']       = $request->input('app');
			$inserts['order_id']  = $request->input('order_id');
			$inserts['complain']  = $request->input('complain');
			$inserts['attach_no'] = $attachId;
			$complaint = $request->input('complaint');
			$subject   = $request->input('subject_id');

			// check if there is previouse complains
            if($request->input('app') == "user"){
                // user want to complain
                if($request->input('complaint') == "provider"){
                    // user want to complain provider
                    $check_complain = DB::table("complains")
                                        ->where("app" , "user")
                                        ->where("provider_id" , $request->input("subject_id"))
                                        ->where("order_id" , $request->input("order_id"))
                                        ->where("user_id" , $request->input("actor_id"))
                                        ->first();
                    if($check_complain !== null){
                        return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
                    }

                }else{
                    // user want to compalin delivery
                    $check_complain = DB::table("complains")
                        ->where("app" , "user")
                        ->where("delivery_id" , $request->input("subject_id"))
                        ->where("order_id" , $request->input("order_id"))
                        ->where("user_id" , $request->input("actor_id"))
                        ->first();
                    if($check_complain !== null){
                        return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
                    }
                }
            }else{
                // provider want to complain delivery
                $check_complain = DB::table("complains")
                    ->where("app" , "provider")
                    ->where("delivery_id" , $request->input("subject_id"))
                    ->where("order_id" , $request->input("order_id"))
                    ->where("provider_id" , $request->input("actor_id"))
                    ->first();
                if($check_complain !== null){
                    return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
                }
            }
			DB::transaction(function() use($inserts, $complaint, $subject){
                $check = DB::table('complains')->insert($inserts);
                //get order details
                $orderDetail = DB::table('orders_headers')->where('order_id',$inserts['order_id'])->first();
                if($orderDetail != NULL){
                    if($complaint == 'provider'){
                        $money = $orderDetail->net_value;
                        $updated_col = 'provider_complain_flag';
                        $balance_type = 'provider';
                    }else{
                        $money = $orderDetail->delivery_price;
                        $updated_col = 'delivery_complain_flag';
                        $balance_type = 'delivery';
                    }
                    $updates[$updated_col] = 1;
                    DB::table('orders_headers')->where('order_id', $inserts['order_id'])->update($updates);
                    DB::table('balances')->where('actor_id', $subject)->where('type', $balance_type)->update([
                        'forbidden_balance' => DB::raw('forbidden_balance + '.$money)
                    ]);
                }

            });
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		}
	}

	public function fetchTimeLine(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'خطأ فى البيانات المرسله',
				2 => 'لا يوجد بيانات'
			);
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'Invalid parameters',
				2 => 'There is no any data'
			);
		}

		$messages = array(
			'required' => 1,
			'numeric'  => 1,
			'exists'   => 1,
		);

		$validator = Validator::make($request->all(), [
			'user_id' => 'required|numeric|exists:users,user_id',
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			if($lang == "ar"){
				$desc_col = "notifications.notif_ar_desc AS notif_desc";
				$cat_col  = "categories.cat_ar_name AS cat_name";
			}else{
				$desc_col = "notifications.notif_en_desc AS notif_desc";
				$cat_col  = "categories.cat_en_name AS cat_name";
			}
			$getTimeLines = DB::table('notifications')
			                  ->where('notifications.user_id', $request->input('user_id'))
			                  ->where('notifications.status', 1)
			                  ->join('providers', 'notifications.provider_id', '=', 'providers.provider_id')
			                  ->join('meals', 'notifications.meal_id', '=', 'meals.meal_id')
			                  ->join('categories', 'meals.cat_id', '=', 'categories.cat_id')
			                  ->distinct()
			                  ->select('meals.meal_id','notifications.created_at', $desc_col, 'meals.main_image AS meal_main_img', 
			                  		   'meals.meal_rtp', 'meals.meal_msrp',
			                  		   'providers.profile_pic AS provider_image', 'providers.brand_name AS provider_name', 
			                  		   'meals.meal_name', 'meals.meal_rate', 'categories.cat_img', 'categories.cat_id', $cat_col)
			                  ->orderBy('created_at', 'DESC')
			                  ->paginate(10);
			if($getTimeLines->count()){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'notifications' => $getTimeLines]);
			}else{
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
			}
		}
	}



	public function search(Request $request){
	    
 		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد نتائج لبحثك',
				2 => 'يجب ان تختار التاريخ عند البحث بالوقت',
				3 => 'يجب إرسال تفاصيل موقع المستخدم',
				4 => 'المسافه يجب ان تكون بالأرقام',
				5 => 'الوقت يجب ان يكون فى تنسيق h:i (09:05)',
				6 => 'التاريخ يجب ان يكون فى تنسيق yyyy-mm-dd',
				7 => 'يجب إختيار المدينة المراد البحث بها',
				8 => 'type يجب ان يكون provider او meal'
			);
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no result for your search',
				2 => 'You must determine the date if you search with time',
				3 => 'user longitude and latitude is required',
				4 => 'distance must be in number',
				5 => 'time must be in format H:i ex:- (09:05)',
				6 => 'date must be in format yyyy-mm-dd',
				7 => 'Please select city',
				8 => 'type attribute must be provider or meal'
			);
		}

		$messages = array(
			'required' => 7,
			'numeric'  => 4,
			'time.date_format' => 5,
			'date.date_format' => 6,
			'required_with'    => 3,
			'in' 			   => 8
		);

		$validator = Validator::make($request->all(),[
			'distance'       => 'nullable|numeric',
			// 'time'   		 => 'sometimes|nullable|date_format:H:i',
			'date'   		 => 'sometimes|nullable|date_format:Y-m-d',
			'city'           => 'required',
			'user_longitude'      => 'required_with:distance',
			'user_latitude'       =>'required_with:distance'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		$name     	 = $request->input('name');
		$city     	 = $request->input('city');
		$cat      	 = $request->input('cat_id');
		$delivery 	 = $request->input('delivery_id');
		$meals_count = $request->input('meals_count');
		$date        = $request->input('date');
		// $time 		 = $request->input('time');
		$distance    = $request->input('distance');
		$user_long   = $request->input('user_longitude');
		$user_lat    = $request->input('user_latitude');
		$type 		 = $request->input('type');

		// if(!empty($time) && empty($date)){
		// 	return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		// }

		// if(!empty($distance) && (empty($user_long) || empty($user_lat))){
		// 	return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		// }

		// $virtualTble = "SELECT meals.meal_id AS id, meals.meal_name AS name, CONCAT(',',meals.cat_id,',') AS cat, '' AS mealsCount, '' AS avail_date, '' AS avail_time, '' AS city, '' AS delivery, '' AS `long`, '' AS `lat`, meals.meal_rate AS rate, meals.main_image AS img, 'meal' AS type FROM `meals`
		// 				UNION
		// 				SELECT providers.provider_id AS id, providers.brand_name AS name, (SELECT CONCAT(',', GROUP_CONCAT(providers_categories.cat_id), ',') FROM providers_categories WHERE providers_categories.provider_id = providers.provider_id) AS cat, (SELECT COUNT(meal_id) FROM meals WHERE meals.provider_id = providers.provider_id) AS mealsCount,DATE(providers.avail_date) AS avail_date, time(providers.avail_date) AS avail_time , providers.city_id AS city, (SELECT CONCAT(',', GROUP_CONCAT(providers_delivery_methods.delivery_method), ',') FROM providers_delivery_methods WHERE providers_delivery_methods.provider_id = providers.provider_id) AS delivery, providers.longitude AS `long`, providers.latitude AS `lat`, providers.provider_rate AS rate,providers.profile_pic as img, 'provider' AS type FROM providers";

		if(!empty($type) && $type == 'provider'){
			$virtualTble = "SELECT providers.provider_id AS id, providers.brand_name AS name, (SELECT CONCAT(',', GROUP_CONCAT(providers_categories.cat_id), ',') FROM providers_categories WHERE providers_categories.provider_id = providers.provider_id) AS cat, '' AS mealsCount,DATE(providers.avail_date) AS avail_date, providers.allowed_from_time AS from_time, providers.allowed_to_time AS to_time, providers.city_id AS city, (SELECT CONCAT(',', GROUP_CONCAT(providers_delivery_methods.delivery_method), ',') FROM providers_delivery_methods WHERE providers_delivery_methods.provider_id = providers.provider_id) AS delivery, providers.longitude AS `long`, providers.latitude AS `lat`, providers.provider_rate AS rate,providers.profile_pic as img, -1 AS likes_count, providers.followers_count AS followers_count, 'provider' AS type, 0.00 AS price FROM providers";
		}elseif(!empty($type) && $type == 'meal'){
			$virtualTble = "SELECT meals.meal_id AS id, meals.meal_name AS name, CONCAT(',',meals.cat_id,',') AS cat, meals.allowed_number AS mealsCount, DATE(providers.avail_date) AS avail_date, providers.allowed_from_time AS from_time, providers.allowed_to_time AS to_time, (SELECT providers.city_id FROM providers WHERE providers.provider_id = meals.provider_id) AS city, (SELECT CONCAT(',', GROUP_CONCAT(delivery_method) ,',') FROM providers_delivery_methods WHERE providers_delivery_methods.provider_id = providers.provider_id) AS delivery, providers.longitude AS `long`, providers.latitude AS `lat`, meals.meal_rate AS rate, meals.main_image AS img, meals.likes_count AS likes_count, -1 AS followers_count, 'meal' AS type, CAST(meals.meal_rtp as decimal(10,2)) AS price FROM `meals` JOIN providers ON meals.provider_id = providers.provider_id";
		}else{
			$virtualTble = "SELECT meals.meal_id AS id, meals.meal_name AS name, CONCAT(',',meals.cat_id,',') AS cat, meals.allowed_number AS mealsCount, DATE(providers.avail_date) AS avail_date, providers.allowed_from_time AS from_time, providers.allowed_to_time AS to_time, (SELECT providers.city_id FROM providers WHERE providers.provider_id = meals.provider_id) AS city, '' AS delivery, providers.longitude AS `long`, providers.latitude AS `lat`, meals.meal_rate AS rate, meals.main_image AS img, meals.likes_count AS likes_count, -1 AS followers_count, 'meal' AS type, CAST(meals.meal_rtp as decimal(10,2)) AS price FROM `meals` JOIN providers ON meals.provider_id = providers.provider_id
							UNION
							SELECT providers.provider_id AS id, providers.brand_name AS name, (SELECT CONCAT(',', GROUP_CONCAT(providers_categories.cat_id), ',') FROM providers_categories WHERE providers_categories.provider_id = providers.provider_id) AS cat, '' AS mealsCount,DATE(providers.avail_date) AS avail_date, providers.allowed_from_time AS from_time, providers.allowed_to_time AS to_time, providers.city_id AS city, (SELECT CONCAT(',', GROUP_CONCAT(providers_delivery_methods.delivery_method), ',') FROM providers_delivery_methods WHERE providers_delivery_methods.provider_id = providers.provider_id) AS delivery, providers.longitude AS `long`, providers.latitude AS `lat`, providers.provider_rate AS rate,providers.profile_pic as img, -1 AS likes_count, providers.followers_count AS followers_count, 'provider' AS type, 0.00 AS price FROM providers";
		}

		 //var_dump($virtualTble);
		 //var_dump($request->all());
		//die();
		
		$conditions = array();

		if(!empty($name) && $name !== 0 && $name !== 0.0 && $name !== "0.0"){
			array_push($conditions, ['tble.name', 'like', '%'.$name.'%']);
		}

		if(!empty($city) && $city !== 0 && $city !== "0" && $city !== 0.0 && $city !== "0.0"){
			array_push($conditions, ['tble.city', '=', $city]);
		}

		if(!empty($cat) && $cat !== 0 && $cat !== "0" && $cat !== 0.0 && $cat !== "0.0"){
			array_push($conditions, ['tble.cat', 'like', '%'.$cat.'%']);
		}

		if(!empty($delivery) && $delivery !== 0 && $delivery !== "0" && $delivery !== 0.0 && $delivery !== "0.0"){
			array_push($conditions, ['tble.delivery', 'like', '%'.$delivery.'%']);
		}

		if(!empty($meals_count) && $meals_count !== 0 && $meals_count !== 0.0 && $meals_count !== "0.0"){
			array_push($conditions, ['tble.mealsCount', '>=', $meals_count]);
		}

		if(!empty($date) && $date != "" && $date != NULL){
			array_push($conditions, ['tble.avail_date', '<=', $date]);
			// array_push($conditions, ['tble.type', '=', 'provider']);
		}

		// if(!empty($time) && $time != "" && $time != NULL){
		// 	array_push($conditions, ['tble.from_time' , '<=', $time]);
		// 	array_push($conditions, ['tble.to_time' , '>=', $time]);
		// }

		if(!empty($distance) && $distance !== 0 && $distance !== "0" && $distance !== 0.0 && $distance !== "0.0"){
			$distanceCond = "(3959 * acos(cos(radians($user_lat)) *  cos(radians(tble.lat)) * cos(radians(tble.long) - radians($user_long)) + sin(radians($user_lat)) * sin(radians(tble.lat))))";
			// $distanceCond = "(3959 * acos(cos(radians(tble.lat)) *  cos(radians($user_lat)) * cos(radians($user_long) - radians(tble.long)) + sin(radians(tble.lat)) * sin(radians($user_lat))))";
			array_push($conditions, [DB::raw($distanceCond), '<=', $distance]);
		}

		if(!empty($conditions)){
		 	$result = DB::table(DB::raw("(".$virtualTble.") AS tble"))
						->select('tble.id', 'tble.name', 'tble.rate', 'tble.img', 'tble.likes_count', 'tble.followers_count', 'tble.type', 'tble.price')
						->where($conditions)->get();
		}else{
			$result = DB::table(DB::raw("(".$virtualTble.") AS tble"))
						->select('tble.id', 'tble.name', 'tble.rate', 'tble.img', 'tble.likes_count', 'tble.followers_count', 'tble.type', 'tble.price')
						->get();
		}

		// if(!empty($result)){
		// 	foreach($result AS $row){
		// 		$row->price = round($row->price, 2);
		// 		// $row->price = $row->price + 0.00;
		// 	}
		// }
		
		
		
 		if(isset($result) && $result->count() > 0){
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'result' => $result]);
		}else{
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
	}

	public function getCountries(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات'
			);
			$col = "country_ar_name AS country_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data'
			);
			$col = "country_en_name AS country_name";
		}

		$countries = DB::table('country')->select('country_id', $col, 'country_code')->get();

		if($countries->count()){
			return response()->json(['status' => true, 'errNum' => 0, $msg[0], 'countries' => $countries]);
		}else{
			return response()->json(['status' => false, 'errNum' => 1, $msg[1]]);
		}
	}

	public function countryCityies(Request $request){
		$lang    = $request->input('lang');
		$country = $request->input('country_id');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
				2 => 'رقم الدوله مطلوب'
			);
			$col = "city.city_ar_name AS city_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
				2 => 'country_id is required'
			);
			$col = "city.city_en_name AS city_name";
		}

		if(empty($country) || $country == NULL){
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}else{
			$cities = DB::table('city')->where('city.country_id', $country)
									   ->join('country', 'city.country_id', '=', 'country.country_id')
									   ->select('city.city_id', $col, 'country.country_code')->get();
									   
			if($cities->count()){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'cities' => $cities]);
			}else{
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}
		}

	}

	public function cities(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات'
			);
			$city_col    = "city.city_ar_name AS city_name";
			$country_col = "country_ar_name AS country_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data'
			);
			$city_col    = "city.city_en_name AS city_name";
			$country_col = "country_en_name AS country_name";
		}
		$resultArr = array();
		$tmpArr    = array();
		//get all countries
		$countries = DB::table('country')->select('country_id', $country_col)->get();
		if($countries->count()){
			foreach($countries AS $country){
				$cities = DB::table('city')->where('city.country_id', $country->country_id)
										   ->join('country', 'city.country_id', '=', 'country.country_id')
										   ->select('city.city_id', 'city.city_abbreviation' ,$city_col, 'country.country_code')->get();
				if($cities->count()){
					$resultArr[$country->country_name] = $cities;
				}else{
					$resultArr[$country->country_name] = [];
				}
			}
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'cities' => $resultArr]);
		}else{
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
	}

	public function prepareSearch(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات'
			);
			$city_col     = "city_ar_name AS city_name";
			$delivery_col = "method_ar_name AS delivery_name";
			$cat_col	  = "cat_ar_name AS cat_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data'
			);
			$city_col    = "city_en_name AS city_name";
			$delivery_col = "method_en_name AS delivery_name"; 
			$cat_col 	  = "cat_en_name AS cat_name";
		}

		$cities = DB::table('city')->select('city_id', $city_col)->get();
		$deliveries = DB::table('delivery_methods')->select('method_id', $delivery_col)->get();
		$categories = DB::table('categories')->where('publish', 1)->select('cat_id', $cat_col)->get();
		return response()->json(['status'=>true, 'errNum' => 0, 'msg' => $msg[0], 'cities' => $cities, 'deliveries' => $deliveries, 'cats' => $categories]);
	}

	public function preparePayment(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
				2 => 'رقم المستخدم مطلوب',
				3 => 'رقم المقدم مطلوب',
				5 => 'رقم المستخدم غير موجود',
				3 => 'رقم المقدم غير موجود',
			);
			$city_col     = "city_ar_name AS city_name";
			$delivery_col = "method_ar_name AS delivery_name";
			$cat_col	  = "cat_ar_name AS cat_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
				2 => 'user_id is required',
				3 => 'provider_id is required',
				4 => 'user_id is not valid',
				5 => 'provider_id is not valid'
			);
			$city_col    = "city_en_name AS city_name";
			$delivery_col = "method_en_name AS delivery_name";
		}

		$messages = array(
			'user_id.required' => 2,
			'provider_id.required' => 3,
			'user_id.exists' => 4,
			'provider_id.exists' => 5
		);
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            "provider_id" => 'required|exists:providers'
        ], $messages);
		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}
        // get provider enable future order or not
        $provider_info = DB::table("providers")
            ->where("provider_id" , $request->input("provider_id"))
            ->select("future_orders")
            ->first();
		$deliveries =  DB::table("delivery_methods")-> join('providers_delivery_methods','providers_delivery_methods.delivery_method','=','delivery_methods.method_id') -> select('method_id',$delivery_col) ->
													   where('provider_id' , $request->input('provider_id'))->get();
													   
													   

        if($request->input("user_id") == "0"){
            return response()->json(['status'=>true, 'errNum' => 0, 'msg' => $msg[0], 'addresses' => [] , 'deliveries' => $deliveries , "is_provider_allow_future_orders" => $provider_info->future_orders]);
        }else{
            $user = DB::table("users")
                        ->where("user_id" , $request->input("user_id"))
                        ->first();
            if(!$user){
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
            }
        }
		$addresses = DB::table('user_addresses')->where('user_id', $request->input('user_id'))
					    ->select('address_id', 'user_id', 'short_desc AS short_address', 'address','longitude', 'latitude')->get();

		return response()->json(['status'=>true, 'errNum' => 0, 'msg' => $msg[0], 'addresses' => $addresses, 'deliveries' => $deliveries , "is_provider_allow_future_orders" => $provider_info->future_orders]);
	}

	public function sms(Request $request){
		$lang = $request->input('lang');
		$phone_number = $request->input('phone');
		$code = mt_rand(100000, 999999);
		if($lang == "ar"){
	    	$msg = array(
	    		0 => 'تم إرسال الكود', 
	    		1 => 'فشل إرسال الرساله',
	    		2 => 'رقم الهاتف مطلوب',
	    		3 => 'لا يوجد رسائل متبقية'
	    	);
	    	// $body = "كود التفعيل الخاص بك هو: ". $code;
	    	$body = "Your activation code is: ".$code;
	    }else{
	    	$msg = array(
	    		0 => 'Message sent successfully', 
	    		1 => 'Failed to send the message',
	    		2 => 'Phone is required',
	    		3 => 'There is no more messages'
	    	);
	    	$body = "Your activation code is: ".$code;
	    }

		if(!empty($phone_number)){
			
		    // Textlocal account details
		    // $username = "mohamed.radwan@wisyst.com";
		    // $hash     = "Unhaw589673";
		    // $username = "mradwan.dev@gmail.com";
		    // $hash     = "Mradwan1234";
		    // $username = "mohamed.radwan191@gmail.com";
		    // $hash     = "Mradwan1234";
		    // $username = "odm@al-yasser.com.sa";
		    // $hash     = "fd8534lQ3Fkmaa";
		    $username = "wisystzad@gmail.com";
		    $hash     = "Alyasser1234";
		    // Message details
		    // $numbers = array(201128265463,201094896758);
		    $sender = urlencode("Mathaq");
		    
		    $message = rawurlencode($body);

		    // $numbers = implode(",", $numbers);
		    $number = $phone_number;
            $apiKey = urlencode('ca0ucwo8dFU-PeyvfCSQYgVMrwsewylNAsVdFd2LhT');

		    // Prepare data for POST request
		    //$data = array("username" => $username, "hash" => $hash, "numbers" => $number, "sender" => $sender, "message" => $message);
		    $data = array('apikey' => $apiKey, "numbers" => $number, "sender" => $sender, "message" => $message);

		    // Send the POST request with cURL
		    $ch = curl_init("http://api.txtlocal.com/send/");
		    curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $response = utf8_decode(curl_exec($ch));
		    $response = json_decode($response);
		    curl_close($ch);
		    
		    if($response->status == 'success'){
		    	return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0] , 'code' => $code]);
		    }else{
		    	// $err = $response->errors[0]->code;
		    	// if($err == 7){
		    	// 	return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		    	// }else{
		    	// 	return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		    	// }
		    	return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		    }
		}else{
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}
	}

	// public function getFollowsAndLikes(Request $request){
	// 	$lang  = $request->input('lang');
	// 	$user  = $request->input('user_id');
	// 	$type  = $request->input('type');

	// 	if($type == 1 || $type == "1"){
	// 			$data['favorites'] = DB::table('meal_likes')->where('meal_likes.user_id', $user)
	// 			                               ->join('meals', 'meal_likes.meal_id', '=', 'meals.meal_id')
	// 			                               ->join('providers', 'meals.provider_id', '=', 'providers.provider_id')
	// 			                               ->select('meals.meal_id', 'meals.main_image', 'meals.meal_name', 'meals.likes_count','meals.meal_rate', 'providers.brand_name AS full_name')
	// 			                               ->paginate(10);
	// 	}elseif($type == "2" || $type == 2){
	// 			$data['follows'] = DB::table('providers_followers')->where('providers_followers.user_id', $user)
	// 												   ->join('providers', 'providers_followers.provider_id', '=', 'providers.provider_id')
	// 												   ->select('providers.provider_id', 'providers.followers_count', 'providers.profile_pic', 'providers.brand_name AS full_name', 'providers.provider_rate')
	// 												   ->paginate(10);
	// 	}else{
	// 		$data['favorites'] = DB::table('meal_likes')->where('meal_likes.user_id', $user)
	// 	                               ->join('meals', 'meal_likes.meal_id', '=', 'meals.meal_id')
	// 	                               ->join('providers', 'meals.provider_id', '=', 'providers.provider_id')
	// 	                               ->select('meals.meal_id', 'meals.main_image', 'meals.meal_name', 'meals.likes_count','meals.meal_rate', 'providers.brand_name AS full_name')
	// 	                               ->paginate(10);
				                               
	// 		$data['follows'] = DB::table('providers_followers')->where('providers_followers.user_id', $user)
	// 											   ->join('providers', 'providers_followers.provider_id', '=', 'providers.provider_id')
	// 											   ->select('providers.provider_id', 'providers.followers_count', 'providers.profile_pic', 'providers.brand_name AS full_name', 'providers.provider_rate')
	// 											   ->paginate(10);
	// 	}

	// 	return response()->json(['status' => true, 'errNum' => 0, 'msg' => 'kkk' , 'dataInfo' => $data]);
	// }

	public function prepareSignUp(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات'
			);
			// $city_col     = "city.city_ar_name AS city_name";
			$col = "country_ar_name AS country_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data'
			);
			// $city_col    = "city.city_en_name AS city_name";
			$col = "country_en_name AS country_name";
		}


		// $cities = DB::table('city')
		// 			->join('country', 'city.country_id', '=', 'country.country_id')
		// 			->select('city.city_id', $city_col, 'country.country_code')->get();
		$countries = DB::table('country')->select('country_id', $col, 'country_code')->get();
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'countries' => $countries]);
	}

	public function getOrderDetails(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array();
			$payment_col = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$future       = "لاحقا";
			$current      = "عادى";
			$status_col = 'order_status.ar_desc AS order_status';
		}else{
			$msg = array();
			$payment_col = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$future       = "in future";
			$current      = "current";
			$status_col = 'order_status.en_desc AS order_status';
		}
		
		//get header
	    	$header = DB::table('orders_headers')
                    ->where('orders_headers.order_id', $request->input('order_id'))
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->join('users', 'orders_headers.user_id' ,'=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->select('orders_headers.order_code', 'orders_headers.total_value AS total', 'orders_headers.delivery_price','orders_headers.total_discount', 'orders_headers.app_value', 'orders_headers.marketer_delivery_value', 'marketer_value AS marketer_provider_value',
							 'orders_headers.address as user_address', 'orders_headers.user_longitude', 'orders_headers.user_latitude','orders_headers.user_phone', 'orders_headers.user_email', DB::raw('IFNULL(orders_headers.delivered_at, "") AS delivered_at'),
						     $payment_col, $delivery_col, 'orders_headers.delivery_method AS delivery_method_id','providers.brand_name AS provider_name', 'users.full_name AS user_name','providers.profile_pic', 'providers.address AS provider_address', 'providers.longitude AS provider_longitude', 'providers.latitude AS provider_latitude',
						     DB::raw('IF(DATE(orders_headers.expected_delivery_time) = DATE(orders_headers.created_at), "'.$current.'", "'.$future.'") AS order_type'), $status_col, 'orders_headers.status_id', DB::raw('IFNULL(DATE(orders_headers.created_at), "") AS order_date'))
					->first();
					
					
				//	dd($header);

		$details = DB::table('order_details')->where('order_details.order_id', $request->input('order_id'))
					 ->join('meals', 'order_details.meal_id', '=', 'meals.meal_id')
					 ->select(
					            'order_details.qty',
                                'meals.meal_name',
                                'meals.meal_desc',
                                'order_details.meal_price',
                                'order_details.discount'
                     )
					 ->get();
        //return response()->json(["dataa" , $details]);

		if($header != NULL){
			$status = $header->status_id;
		}else{
			$status = "";
		}

		if($status == 4 || $status == "4"){

		    // get rate of provider rate
            //$provider_rate_arr = [];
            //$delivery_rate_arr = [];
			$provider_order_rate = DB::table('provider_evaluation')
                            ->where('order_id',$request->input('order_id'))
						    ->select(DB::raw("IFNULL(((quality + autotype + packing + maturity + ask_again) / 5), 0) AS order_rate") , DB::raw("IFNULL(((comment)), 0) AS comment"))
						    ->first();
			if($provider_order_rate != NULL){
                $provider_order_rate = [
                                        "rate" => $provider_order_rate->order_rate ,
                                        "comment" => $provider_order_rate->comment
                                        ];
			}else{
                $provider_order_rate = "";
			}

			// get rate of delivery rate
            $delivery_order_rate = DB::table('delivery_evaluation')
                ->where('order_id',$request->input('order_id'))
                ->select(DB::raw("IFNULL(((delivery_arrival + delivery_in_time + delivery_attitude) / 3), 0) AS order_rate"), DB::raw("IFNULL(((comment)), 0) AS comment"))
                ->first();
            if($delivery_order_rate != NULL){
                $delivery_order_rate = [
                                        "rate" => $delivery_order_rate->order_rate,
                                        "comment" => $delivery_order_rate->comment
                                        ];
            }else{
                $delivery_order_rate = "";
            }

		}else{
            $provider_order_rate = "";
            $delivery_order_rate = "";
		}
		

		$type = $request->input('type');

		$order_status = DB::table('order_status')->whereIn('status_id', [1,2,3,4,5])
						   ->select('status_id', $status_col)->get();

		$percentage = DB::table('app_settings')->select('app_percentage')->first();
		if($percentage != NULL){
			$app_percentage = $percentage->app_percentage;
		}else{
			$app_percentage = 0;
		}

		return response()->json([
		                            'status' => true,
                                    'errNum' => 0, 'msg' =>
                                    'Retrieved successfully',
                                    'header' => $header,
                                    'details' => $details,
                                    'app_percentage' => $app_percentage,
                                    'order_status' => $order_status,
                                    'delivery_order_rate' => $delivery_order_rate,
                                    'provider_order_rate' => $provider_order_rate,

                                ]);

	}

	public function getProviderMeals(Request $request){
		$lang = $request->input('lang');
		$catId = $request->input('cat_id');
		$providerId = $request->input('provider_id');
		$userId     = $request->input('user_id');
		if(empty($catId) || $catId == NULL || $catId == ""){
			$catId = 0;
		}

		if(empty($userId) || $userId === 0 || $userId === "0"){
			$likeFlag = '0 as likeFlag';
		}else{
			$likeFlag = 'IF ((SELECT count(id) FROM meal_likes WHERE meal_likes.user_id = '.$userId.' AND meal_likes.meal_id = meals.meal_id) > 0, 1, 0) as likeFlag';
		}
		if($catId != 0){
			$meals = Meals::where('meals.provider_id', $providerId)
						         ->where('meals.cat_id', $catId)
						         ->where('meals.publish', 1)
						         ->select('meals.meal_id', 'meals.meal_name', 'meals.meal_desc','meals.meal_rtp', 'meals.meal_msrp', 'meals.main_image', 'meals.likes_count', 'meals.meal_rate', 
						         		   DB::raw($likeFlag))
						         ->paginate(10);
		}else{
			$meals = Meals::where('meals.provider_id', $providerId)
						         ->where('meals.publish', 1)
						         ->select('meals.meal_id', 'meals.meal_name', 'meals.meal_desc','meals.meal_rtp', 'meals.meal_msrp','meals.main_image', 'meals.likes_count', 'meals.meal_rate', 
						         		   DB::raw($likeFlag))
						         ->orderBy('meals.created_at', 'DESC')
						         ->paginate(10);
		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => 'Retrieved successfully', 'meals' => $meals]);
	}

	public function test(Request $request){
		$meals = $request->input('meals');
		$json = '{"key": [{"key1":1,"key2":2},{"key1":1,"key2":2}]}';

		var_dump($json);
		$x = json_decode($json);
		var_dump((array)$x);
		die();
		var_dump(json_decode($meals, true));
		var_dump($meals);
	}

	public function edit_phone(Request $request){
	 //   return response()->json($request);
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التعديل بنجاح',
				1 => 'كل البيانات مطلوبه',
				2 => 'app يجب ان يكون فى (users, providers, deliveries)',
				3 => 'فشلت العلميه من فضلك حاول فى وقت لاحق',
				4 => 'رقم الجوال مكرر'
			);
		}else{
			$msg = array(
				0 => 'Phone updated successfully',
				1 => 'All fields are required',
				2 => 'app must be in (users,providers, deliveries)',
				3 => 'Failed to update, please try again later',
				4 => 'Phone number is already exist'
			);
		}

		$messages = array(
			'required' => 1,
			'in' 	   => 2,
			'unique'   => 4
		);

		$table = $request->input('app');
		if($table == "users"){
			$condition_col = 'user_id';
		}elseif($table == "providers"){
			$condition_col = 'provider_id';
		}elseif($table == "deliveries"){
			$condition_col = 'delivery_id';
		}else{
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
		$validator = Validator::make($request->all(), [
			'actor_id'     => 'required',
			'app'          => 'required|in:users,providers,deliveries',
			'country_code' => 'required',
			'phone'        => 'required|unique:'.$table.',phone,'.$request->input('actor_id').','.$condition_col
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			//update 

			$update =  DB::table($table)->where($condition_col, $request->input('actor_id'))
							            ->update([
									 		'country_code' => '+'.$request->input('country_code'),
									 		'phone'        => $request->input('phone')
							            ]);
			if($update){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		}
	}

	public function cancel_order(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت إلغاء الطلب',
				1 => 'order_id مطلوب',
				2 => 'user_id مطلوب',
				3 => 'فشل إلغاء الطلب حاول مره اخرى',
				4 => 'لا يمكنك إلغاء إلا طلباتك',
				5 => 'عفوا لا يمكن إلغاء الطلب بعد طلب التوصيل',
				6 => 'عفوا لا يمكن إلغاء طلب منتهى'
			);
			$push_notif_title = 'إلغاء طلب';
			$push_notif_message = 'تم إلغاء طلب من قبل المستخدم';
		}else{
			$msg = array(
				0 => 'Order has been canceled',
				1 => 'order_id is required',
				2 => 'user_id is required',
				3 => 'Failed to cancel the order, try again',
				4 => 'Sorry it is not your order to cancel',
				5 => 'Sorry you can\'t cancel order with delivery man',
				6 => 'Sorry you can\'t cancel finished order'
			);
			$push_notif_title = 'Order canceled';
			$push_notif_message = 'User has been canceled order';
		}

		$messages = array(
			'order_id.required'    => 1,
			'user_id.required'     => 2
		);

		$validator = Validator::make($request->all(), [
			'order_id' => 'required', 
			'user_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		//make sure that the order is the user order
		$check = DB::table('orders_headers')->where('user_id', $request->input('user_id'))
											->where('order_id', $request->input('order_id'))
											->select('status_id', 'provider_id', 'payment_type', 'total_value')
											->first();
		if($check == NULL){
			return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
		}else{
			if($check->status_id > 2){
				if($check->status_id == 3 || $check->status_id == 8){
					$e = 5;
				}else{
					$e = 6;
				}
				return response()->json(['status' => false, 'errNum' => $e, 'msg' => $msg[$e]]);
			}else{
				$provider_id  = $check->provider_id;
				$payment_type = $check->payment_type;
				$total_value  = $check->total_value;
			}
		}

		try {
			$user_id  = $request->input('user_id');
			$order_id = $request->input('order_id');
			$status   = 9;
			DB::transaction(function() use ($status, $order_id, $user_id, $payment_type, $total_value){
				DB::table("orders_headers")->where('order_id', $order_id)->update(['status_id' => $status]);
				DB::table("order_details")->where('order_id', $order_id)->update(['status' => $status]);
				if($payment_type != 1 && $payment_type != "1"){
					User::where('user_id', $user_id)->update([
							'points' => DB::raw('points + '.$total_value)
					]);
				}
			});

			$notif_data = array();
			$notif_data['title']      = $push_notif_title;
		    $notif_data['message']    = $push_notif_message;
		    $notif_data['order_id']   = $order_id;
		    $notif_data['notif_type'] = 'cancel_order';
		    $provider_token = Providers::where('provider_id', $provider_id)->first();
		    if($provider_token != NULL){
		    	$push_notif = $this->singleSend($provider_token->device_reg_id, $notif_data, $this->provider_key);
		    }
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}
	}

	public function get_user_balance(Request $request)
	{
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'user_id مطلوب'
			);
			$canceled    = ' ريال محولة من إلغاء طلب بتاريخ ';
			$refused     = ' ريال محولة من رفض طلب بتاريخ ';
			$notanswered = ' ريال محولة من طلب لم يتم الرد عليه بتاريخ ';
			$failed      = ' ريال محولة من طلب فشل توصيله بتاريخ ';
			$else      = ' ريال محولة من مصدر غير معروف بتاريخ ';
		}else{
			$msg = array(
				0 => '',
				1 => 'user_id is required'
			);
			$canceled    = ' SR from canceled order at ';
			$refused     = ' SR from refused order at ';
			$notanswered = ' SR from not responded order at ';
			$failed      = ' SR from failed to delivered order at ';
			$else        = ' SR from unkonwn source at ';
		}

		$messages = array(
			'user_id.required'    => 1
		);

		$validator = Validator::make($request->all(), [
			'user_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		$userData = User::where('user_id', $request->input('user_id'))
						   ->select('points', 'invitation_code')->first();

		if($userData != 	NULL){
			$user_balance = $userData->points;
			$user_code    = $userData->invitation_code;
		}else{
			$user_balance = 0;
			$user_code    = "";
		}

		//get user balance details 
		$details = DB::table('orders_headers')->where('user_id', $request->input('user_id'))
											  ->whereIn('status_id', [5,6,7,9])
											  ->where('payment_type', '!=', 1)
											  ->select('total_value', DB::raw('DATE(created_at) AS day'), 'status_id AS status',
											  	DB::raw(
											  			'(CASE status_id 
											  			WHEN 5 THEN CONCAT(total_value, "'.$failed.'", DATE(created_at))
											  			WHEN 6 THEN CONCAT(total_value, "'.$refused.'", DATE(created_at))
											  			WHEN 7 THEN CONCAT(total_value, "'.$notanswered.'", DATE(created_at))
											  			WHEN 9 THEN CONCAT(total_value, "'.$canceled.'", DATE(created_at))
											  			ELSE CONCAT(total_value,"'.$else.'",DATE(created_at)) END) AS full_text'
											  		)
											  	)
											  ->get();
		$usedCredit = DB::table('orders_headers')->where('user_id', $request->input('user_id'))
												 ->whereIn('status_id', [5,6,7,9])
											  	 ->where('payment_type', '!=', 1)
												 ->sum('used_points');

		$withdrawed_balance = DB::table('withdraw_balance')->where('actor_id', $request->input('user_id'))
														   ->where('type', 'user')
														   ->where('status', 2)
														   ->sum('current_balance');
        if($withdrawed_balance == NULL || empty($withdrawed_balance)){
        	$withdrawed_balance = 0;
        }
		if($user_code != ""){
			$invitationCredits = User::where('used_invitation_code', $user_code)->sum('invitation_credits');
		}else{
			$invitationCredits = 0;
		}

		// get user bank data
        $delivery_bank = DB::table("withdraw_balance")
            ->select("*")
            ->where("actor_id" , $request->input("user_id"))
            ->where("type" , "user")
            ->get();
        if(count($delivery_bank) > 0){
            $last_entry = $delivery_bank[count($delivery_bank) -1];
            $bank_name = $last_entry->bank_name;
            $bank_phone = $last_entry->phone;
            $bank_username = $last_entry->name;
            $bank_account_num = $last_entry->account_num;
        }else{
            $bank_name = "";
            $bank_phone = "";
            $bank_username = "";
            $bank_account_num = "";
        }

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'total_balance' => $user_balance, 'balance_details' => $details, 'usedCredit' => $usedCredit, 'invitationCredits' => $invitationCredits, 'withdrawed_balance' => $withdrawed_balance, "bank_name" => $bank_name , "bank_phone" => $bank_phone,"account_num" => $bank_account_num  , "bank_username" => $bank_username]);
	}

	public function balance_withdraw(Request $request)
	{
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم إضافة الطلب بنجاح',
				1 => 'user_id مطلوب', 
				2 => 'المبلغ المراد سحبه مطلوب',
				3 => 'المبلغ المراد سحبه يجب ان يكون رقم',
				4 => 'إسم البنك مطلوب',
				5 => 'إسم صاحب الحساب مطلوب',
				6 => 'رقم الحساب مطلوب',
				7 => 'رقم الجوار مطلوب', 
				8 => 'فشلت العلمية من فضلك حاول مره اخرى',
				9 => 'هناك طلب لك ما زال معلق لا يمكنك عمل الطلب حاليا',
				10 => 'ليس لديك رصيد كافى لاتمام هذه العملية',
				11 => 'الرصيد المطلوب اقل من الحد الادنى لسحب الرصيد'
			);
		}else{
			$msg = array(
				0 => 'Added successfully',
				1 => 'user_id is required',
				2 => 'value is required',
				3 => 'value must be a nubmer',
				4 => 'Bank name is required',
				5 => 'Name is required',
				6 => 'Account number is required',
				7 => 'Phone number is required',
				8 => 'Process failed, please try again',
				9 => 'You have a pending request, you can\'t add that request',
				10 => 'You do not have enough balance to execute this process',
				11 => 'the requested balance is less than minimum balance to withdraw',
			);
		}

		$messages = array(
			'user_id.required'     => 1,
			'value.required'       => 2,
			'value.numeric'        => 3,
			'bank_name.required'   => 4,
			'name.required'   	   => 5,
			'account_num.required' => 6,
			'phone.required'       => 7,
		);

		$validator = Validator::make($request->all(), [
			'user_id'     => 'required',
			'value'       => 'required|numeric',
			'bank_name'   => 'required',
			'name'        => 'required', 
			'account_num' => 'required',
			'phone' 	  => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

        // insert bank account data into database
        $actor_bank_data = DB::table("withdraw_balance")
            ->where("actor_id" , $request->input("user_id"))
            ->where("type" , "user")
            ->first();
        // if($actor_bank_data !== null){
        //     // update bank data
        //     DB::table("withdraw_balance")
        //         ->where("actor_id" , $request->input("user_id"))
        //         ->where("type" , "user")
        //         ->update([
        //             "name" => $request->input("name"),
        //             "phone" => $request->input("phone"),
        //             "bank_name" => $request->input("bank_name"),
        //             "account_num" => $request->input("account_num"),
        //             "updated_at" =>date('Y-m-d h:i:s')
        //         ]);

        // }else{
        //     // insert bank data
        //     DB::table("withdraw_balance")
        //         ->insert([
        //             "actor_id" => $request->input("user_id"),
        //             "type" => "user",
        //             "name" => $request->input("name"),
        //             "phone" => $request->input("phone"),
        //             "bank_name" => $request->input("bank_name"),
        //             "account_num" => $request->input("account_num"),
        //             "created_at" =>date('Y-m-d h:i:s')
        //         ]);
        // }

		//check if there is a pending request 
		$check = DB::table('withdraw_balance')->where('actor_id', $request->input('user_id'))->where('type', 'user')->where('status', 1)->first();
		if($check != NULL){
			return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
		}

        // check if the user requested blance is avaliable
        $user_balace = DB::table("balances")
            ->select("current_balance")
            ->where("actor_id" , $request->input("user_id"))
            ->where("type" , "user")
            ->first();
        if($user_balace != null){
            $user_current_balance = $user_balace->current_balance;
        }else{
            return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
        }
        

        if($request->input("value") > $user_current_balance){
            return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
        }


        //check if the current balance is greater than min limit of withdrawing
        $min_balance = DB::table("app_settings")
            ->select("min_balace_to_withdraw")
            ->first();
        if($request->input("value") < $min_balance->min_balace_to_withdraw){
            return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);
        }

		$check = DB::table('withdraw_balance')->insert([
					'actor_id'        => $request->input('user_id'),
					'due_balance'     => 0,
					'current_balance' => $request->input('value'),
					'type'            => 'user',
					'name'            => $request->input('name'),
					'bank_name'       => $request->input('bank_name'),
					'account_num'     => $request->input('account_num'),
					'phone'           => $request->input('phone')
				]);

		if($check){
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		}else{
			return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
		}
	}
}
