<?php

namespace App\Http\Controllers;

/**
 * Class ProviderController.
 * it is a class to manage all provider functionalities
  
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Memberships;
use App\Providers;
use App\Marketers;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use Storage;
use DateTime;
use Carbon\Carbon;

class ProviderController extends Controller
{


	public function __construct(Request $request){
		 
			
			
			
		}
		
		  
   

	//method to prevent visiting any api link
	public function echoEmpty(){
		echo "";
	}

 


	protected function saveImage($data, $image_ext, $path){
		if(!empty($data)){
			// header('Content-Type: image/jpeg');
			// $data = base64_decode($data);
			// $name = $path.'img-'.str_random(4).'.jpg';
			// $target_file = base_path()."/public/".$name;
			// file_put_contents($target_file,$data);
			// return $name;
			// Log::debug("image_string: ". $data);
			$data = str_replace('\n', "", $data);
			$data = base64_decode($data);
			$im   = imagecreatefromstring($data);
			if ($im !== false) {
				$name = 'img-'.str_random(4).'.'.$image_ext;
				if ($image_ext == "png"){
					imagepng($im, $path . $name, 9);
				}else{
					imagejpeg($im, $path . $name, 100);
				}

				return $path.$name;
			} else {
				return "";
			}
		}else{
			return "";
		}
	}
      

      //prepare providers signup page first step
	public function prepareSignUp(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$cat_col 	    = "cat_ar_name AS cat_name";
			$country_col    = "country_ar_name AS country_name";
			$membership_col ="membership_ar_name AS membership_name";
		}else{
			$cat_col 	    = "cat_en_name AS cat_name";
			$country_col    = "country_en_name AS country_name";
			$membership_col = "membership_en_name AS  membership_name";
		}

		
		//get memeberships
		$memberships = Memberships::where('publish', 1)->select('membership_id', $membership_col)->get();
        

		//get countries
		$countries = DB::table('country')->where('publish', 1)->select('country_id', $country_col, 'country_code')->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'countries' => $countries,'memberships' => $memberships]);
	}

          
	public function prepareSignUpSecondStep(Request $request){

 

		$lang = $request->input('lang');


          if($lang == "ar"){
			$msg = array(
				 
				1 =>  'لابد من توكن المستخدم ',
			 
			);
		}else{
			$msg = array(
				 
				1 => 'access_token required',
			 
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


		if($lang == "ar"){
			$cat_col 	        = "cat_ar_name AS cat_name";
			$delivery_method_col = "method_ar_name AS delivery_method_name";
  		}else{
			$cat_col 	    = "cat_en_name AS cat_name";
			$delivery_method_col = "method_en_name AS delivery_method_name";
 		}


            //get provider by token "required -->access_token"

 		$provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'))  -> select('provider_id AS id','full_name AS provider_name','category_id','store_name', 'phone', 'country_code','country_id', 'city_id','membership_id', 'token AS access_token','provider_rate','created_at') ->first();
 

		  //get main categories
		$cats = Categories::where('publish', 1)->select('cat_id', $cat_col)->get();

 
                  if(isset($cats) && $cats -> count() > 0)  {

                           
                            foreach ($cats as $key => $cat) {

                            if ($provider -> category_id  ==  $cat -> cat_id ) {
                            	        

                            	  $cat    -> choosen = 1; 
 
                            }else{
  
                                    $cat    -> choosen = 0;        
                            }                     	   
                    }    

                  }


		 
        //get delivery Methods available 
	 	$delivery_methods = DB::table("delivery_methods")
	 	                        ->    select('method_id',$delivery_method_col)
                                ->    get();


                    
                  if(isset($delivery_methods) && $delivery_methods -> count() > 0)  {

                           
                            foreach ($delivery_methods as $key => $method) {

                            if ($provider -> delivery_method_id  ==  $method -> method_id ) {
                            	        

                            	  $method -> choosen = 1; 
                                   

                                  if($method_id ==2) 
                            	  $method -> price = $provider -> delivery_price;       

                            }else{
  
                                    $method -> choosen = 0;       

                            } 
                    	   
                    }    

                  }

 
  

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '','provider'=> $provider ,'delivery_methods' =>$delivery_methods,'cats' => $cats]);
	}



     //store providers first step sinup
	public function signUp(Request $request){
	     
 	    
		$lang   = $request->input('lang');
		$status = 0;
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
 				2 => 'الرقم السرى يجب الا يقل عن 8 حروف',
 				3 => 'كلمة المرور غير  متطابقه ',
				4 => 'رقم الجوال مستخدم من قبل',
 				5 => 'فشل التسجيل من فضلك حاول لاحقا',
 				6 => 'فئة المتجر غير موجوده ',
 				7 => 'الدولة غير موجوده ',
 				8 => 'المدينو غير موجوده ',

 			);
			
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'Password must not be less than 8 characters',
				3 => 'Password not confirmed',
				4 => 'Phone is already used',
 				5 => 'Failed to register, please try again later',
 				6 => 'membership type not exists',
 				7 => 'country   not exists',
 				8 => 'city  not exists',

 			);
		}

		$messages = array(
			'required'              => 1,
 			'min'                   => 2,
 			'password.confirmed'    => 3,
			'phone.unique'          => 4,			
			'membership_id.exists'  => 6,
			'country_id.exists'     => 7,
			'city_id.exists'        => 8

 		);

		$validator = Validator::make($request->all(), [
			'full_name'              => 'required',
   			'store_name'             => 'required',
 			'phone'                  => 'required|unique:providers',
			'country_code'           => 'required',
			'password_confirmation'  => 'required',
			'password'               => 'required|min:8|confirmed',			
			'country_id'             => 'required|exists:country,country_id',
			'membership_id'          => 'required|exists:memberships,membership_id',
			'city_id'                => 'required|exists:city,city_id',
 			'provider_token'         => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		    $rateCounter = 0;
			if($request->input('country_id') == 0 || $request->input('country_id') == "0" || is_null($request->input('country_id')) || empty($request->input('country_id'))){
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}

			if($request->input('city_id') == 0 || $request->input('city_id') == "0" || is_null($request->input('city_id')) || empty($request->input('city_id'))){
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}
  


		   $data['image'] = "avatar_ic.png";

				    if($request->input('profile_pic')){
	 
	                    $image  = $request->input('profile_pic') ;
	                  //save new image   
	                    $image ->store('/','providers');
	                                       
	                  $nameOfImage = $image ->hashName();

	                $data['image'] =  $nameOfImage;

	 
		           }  


					$full_name = $request->input('full_name');

					$data['full_name']      = $full_name;
	 				$data['store_name']     = $request->input('store_name');
	 				$data['phone']          = $request->input('phone');
					$data['country_code']   = '+'.$request->input('country_code');
					$data['password']       = $request->input('password');
					$data['country_id']     = $request->input('country_id');
					$data['city_id']        = $request->input('city_id');
					$data['membership_id']        = $request->input('membership_id');
	 				$data['status']         = $status;
	 				$data['provider_token'] = $request->input('provider_token');
	 				$data['provider_rate'] = 0;

				  

                          // send activation code to provider 


				    $code          = $this->generate_random_number(4);

			        $data['token'] = $this -> getRandomString(128);

			        $data['activate_phone_hash'] = json_encode([
			            'code'   => $code,
			            'expiry' => Carbon::now()->addDays(1)->timestamp,
			        ]);
			        
			        $message = (App()->getLocale() == "en")?
			                    "Your Activation Code is :- " . $code :
			                     "رقم الدخول الخاص بك هو :- " .$code ;
 
                    

				$id = "";


				try {
					DB::transaction(function () use ($data, &$id) {

						$id = Providers::insertGetId([
							'full_name'    => $data['full_name'],
 							'store_name'   => $data['store_name'],
 							'phone'        => $data['phone'],
							'country_code' => $data['country_code'],
							'password'     => md5($data['password']),
							'country_id'   => $data['country_id'],
							'membership_id'=> $data['membership_id'],
							'city_id'      => $data['city_id'],
							'status'       => $data['status'],
 							'device_reg_id'          => $data['provider_token'],
 							'provider_rate'          => $data['provider_rate'], 
 							'activate_phone_hash'    => $data['activate_phone_hash'],
 							'token'                   => $data['token'],
 							'profile_pic'             => $data['image']
						]);


						if($id){

							$inserts = array();
							 
							 //intialize balance with zero 
 							DB::table('balances')->insert(['actor_id' => $id, 'type' => 'provider','current_balance' => 0, 'due_balance' => 0]);

						}else{
							return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
						}


					});
  



                            //  return auth data to response 

					$providerData = Providers::where('provider_id', $id)->select('provider_id AS id','full_name AS provider_name', 'store_name', 'phone', 'country_code','country_id', 'city_id','membership_id', 'token AS access_token','status','provider_rate','created_at')->first();


                    $res = (new SmsController())->send($message , $providerData ->phone);
  

					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $providerData]);

				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
				}
 

			}
 
  

   //store providers second step sinup
	public function signUpSecondStep(Request $request){
	     
 	    
		$lang   = $request->input('lang');
		$status = 0;
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
 				2 => 'القسم التابع له المتجر غير موجود ',
 				3 => 'امتداد صوره غير صالح ',
				4 => 'نوع التوصيل غير موجود ',
 				5 => 'لابد من توكن المستخدم ',
 				6 =>  'لابد ان تكون  ارقام ',
 				7 => 'سعر التوصيل مطلوب',
 				8=> 'فشل التسجيل من فضلك حاول لاحقا',
  			);
			
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'Category id doesn\'t exists',
				3 => 'image not valid',
				4 => 'delivery method not exists',
 				5=> 'access_token required',
 				6 => 'must be number ' ,
 				7 => 'delivery price  required',
 				8 => 'Failed to register, please try again later',
  			);
		}

		$messages = array(
           
			'category_id.required'        => 1,
			'commercial_photo.required'   => 1,
			'delivery_method_id.required' => 1,
			'longitude.required'          => 1,
			'latitude.required'           => 1,
 			'category_id.exists'          => 2,
 			'mimes'                       => 3,
 			'delivery_method_id.exists'   => 4,
			'access_token.required'       => 5,			
			'numeric'                     => 6,
			'delivery_price.required'     => 7,
  		);

 		$rules= [

			'category_id'            => 'required|numeric|exists:categories,cat_id',
   			'commercial_photo'       => 'required|mimes:jpeg,png,jpg',
 			'delivery_method_id'     => 'required|numeric|exists:delivery_methods,method_id',
            'longitude'              => 'required',
			'latitude'               => 'required',
			'access_token'           => 'required',

		];

               

               //if delivery method is 2 this required price field in the request 
		if($request -> delivery_method_id == 2){
              

                  $rules['delivery_price'] = 'required';

		}

		$validator = Validator::make($request->all(),$rules, $messages);

 

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

                    $data=[];
					$data['category_id']            = $request-> category_id;
	 				$data['delivery_method_id']     = $request-> delivery_method_id ;
	 				$data['longitude']              = $request-> longitude;
					$data['latitude']               = $request-> latitude;

                             // delivery required additional price 
					if($data['delivery_method_id'] == 2){
 
                      $data['delivery_price']     = $request-> delivery_price;                           
					}

                   

                   if($request -> known_phone ){
  
	                      $data['known_phone'] = $request -> known_phone;
                        
					}


					if($request -> hasFile('commercial_photo')){
 
	                     $request -> commercial_photo ->store('/','providers');
	                               
	                      $commercial_photo = $request->commercial_photo->hashName();

	                      $data['commercial_photo'] = $commercial_photo;
                        
					}else{

                            
                            $data['commercial_photo'] = "";

					}
				  

                $id = $this->get_id($request,'providers','provider_id');  
                $provider = Providers::where('provider_id',$id);
                    
 

				try {
					    DB::transaction(function () use ($data,$provider,&$id) {
  

						 $provider -> update($data);
 

					    });
  
 

                            //  return auth data to response 

					$providerData = Providers::where('provider_id', $id)
					    ->select(
					    	'provider_id AS id',
					    	'full_name AS provider_name',
					    	 'store_name', 
					    	 'phone', 
					    	 'country_code',
					    	 'country_id',
				    	     'city_id',
				    	     'membership_id',
				    	     'delivery_method_id',
				    	     'category_id',
				    	     'provider_rate',
				    	     'phoneactivated',
				    	     'known_phone',
				    	     'token AS access_token',
				    	     'provider_rate',
				    	     'longitude',
				    	     'latitude',
				    	     'delivery_price',
				    	     DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/', providers.commercial_photo) AS commercial_photo"),
				    	     'status',
				    	     'publish',
				    	     'known_phone',
				    	     'created_at'
					    	)->first();

 

					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $providerData]);

				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
				}
 

			}
   

    public function activateAccount(Request $request){

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


		$provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'));

		 $activate_phone_hash = $provider -> first() ->  activate_phone_hash;

		  $code                 = json_decode($activate_phone_hash) -> code;
 
		  if($code  != $request -> code)
		  {
              
             return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);

		  }
 
        $data['phoneactivated'] = "1";
        $data['status'] = "1";
        $data['activate_phone_hash'] = null;

        $provider -> update($data);
 
        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
    }


    public function resendActivationCode(Request $request){

      // required access_token 


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

    $provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'))  ;
 
    $code          = $this->generate_random_number(4);

  

    $data['activate_phone_hash'] = json_encode([
        'code'   => $code,
        'expiry' => Carbon::now()->addDays(1)->timestamp,
    ]);
 

     $provider -> update($data);

    $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                 "رقم الدخول الخاص بك هو :- " .$code ;
  
    $res = (new SmsController())->send($message , $provider -> first() ->phone);
   

    return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

}


	public function providerLogin(Request $request){

		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم الدخول',
				1 => 'رقم التليفون مطلوب',
				2 => 'تاكد من رقم التليفون مع إضافة كود الدوله',
				3 => 'كلمة السر مطلوبه',
				4 => 'خطأ فى البيانات',
				5 => 'لم يتم تفعيل حسابكم من قبل الاداره ',
				6 => 'رقم الجهاز مطلوب',
				7=> 'لابد من تفعيل حساب ',
			);
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$msg = array(
				0 => 'Logined successfully',
				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'Password is required',
				4 => 'Wrong data',
				5 => 'admin not active your account',
				6 => 'provider_token is required',
				7 => 'need to activate your account '
			);
			$city_col = "city.city_en_name AS city_name";
		}
		$messages = array(
				'phone.required'    => 1,
				'password.required' => 3,
				'provider_token.required' => 6
			);
		$validator = Validator::make($request->all(), [
			'phone'    => 'required',
			'password' => 'required',
			'provider_token' => 'required'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$getProvider = Providers::where('providers.password', md5($request->input('password')))
							->where(function($q) use ($request){
						        $q->where('providers.phone', $request->input('phone'))
						          ->orWhere(DB::raw('CONCAT(providers.country_code,providers.phone)'), $request->input('phone'));
						    })
							->join('city', 'providers.city_id', 'city.city_id')
							->select('providers.*', $city_col)
							->first();
			if($getProvider != NULL){

                      


                       //update device FCM token 
				Providers::where('provider_id', $getProvider->provider_id)->update(['device_reg_id' => $request->input('provider_token')]);

   
 				    	      
					    	
				$providerData = array(
					'id'              => $getProvider->provider_id,
					'provider_name'   => $getProvider->full_name,
					'store_name'      => $getProvider->store_name,
 					'phone' 		  => $getProvider->phone,
					'country_code'    => $getProvider->country_code,
 					'longitude'       => $getProvider->longitude,
					'latitude' 		  => $getProvider->latitude,
                    'commercial_photo'               =>env('APP_URL').'/public/providerProfileImages/'.$getProvider->commercial_photo,
					'membership_id' 		      => $getProvider->membership_id,
					'delivery_method_id' 		  => $getProvider->delivery_method_id,
					'category_id' 		          => $getProvider->category_id,
					'phoneactivated'              => $getProvider->phoneactivated,
					'known_phone'                 => $getProvider->known_phone,
					'access_token'                => $getProvider->token,
					'delivery_price'              => $getProvider->delivery_price,
                    'status'                      => $getProvider->status, 
				    'publish'                     => $getProvider->publish, 
					'country_id'      => $getProvider->country_id,
					'city_id'         => $getProvider->city_id,
 					'provider_rate'   => $getProvider->provider_rate,
 					'created_at'      => date('Y-m-d h:i:s', strtotime($getProvider->created_at))

				);

				      //admin not active the provider account 
				if($getProvider->publish == 0 || $getProvider->publish == 0){
					return response()->json(['status'=> false, 'errNum' => 7, 'data' => $providerData, 'msg' => $msg[7]]);
				}

				 //need to activate account by verfiy phone number
				if($getProvider-> status == 0 || $getProvider->status == 0){
					return response()->json(['status'=> false, 'errNum' => 5, 'data' => $providerData, 'msg' => $msg[5]]);
				}


				return response()->json(['status'=> true, 'errNum' => 0, 'data' => $providerData, 'msg' => $msg[0]]);
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
                   "phone" => "required|numeric|exists:providers,phone"
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

		        //select provider base on his/her phone number if exists
		        $providerData = DB::table("providers")->where("phone" , $request->input("phone"))->select("provider_id")->first();

		        $provider = Providers::where('provider_id',$providerData -> provider_id);
                 

 		        if($provider -> first()->  phoneactivated == '0' ){

		            return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);

		        }


		        $code = $this -> generate_random_number(4);

		        $message = (App()->getLocale() == "en")?
		            "Your Activation Code is :- " . $code :
		            $code . "رقم الدخول الخاص بك هو :- " ;

		        $activate_phone_hash = json_encode([
		            'code'   => $code,
		            'expiry' => Carbon::now()->addDays(1)->timestamp,
		        ]);

		        $provider -> update([
		        	 'activate_phone_hash'   => $activate_phone_hash,
		        ]);

		        (new SmsController())->send($message , $provider ->first()->phone);

		        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4] , "access_token" => $provider -> first() ->token]);

	}

	 

	   public function updatePassword(Request $request){

           $lang = $request->input('lang');

        $rules      = [
            "password"      => "required|min:8|confirmed",
            "access_token"  => "required"
        ];

        $messages   = [
            "required"              => 1,
            'password.min'          => 2,
            'password.confirmed'    => 3,
        ];



		if($lang == "ar"){
			$msg = array(
 				1 => 'لابد من ادخال كلمة المرور ',
				2 => 'كلمه المرور  8 احرف ع الاقل ',
				3 => 'كلمة المرور غير متطابقه ',
 				4 => 'تم تغيير كلمة  المرور بنجاح ',
				
			);
			 
		}else{
			$msg = array(
 				1 => 'password field required',
				2 => 'password minimum characters is 8',
				3 => 'password not confirmed',
   				4 => 'password successfully updated'    
			);
			 
		}

       
        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }

        $provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'))
                        -> update([
                                      
                                         'password'              =>  md5($request->input('password')),
                                         'activate_phone_hash'   => null
                                 ]);

               
 

        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);
    }

	public function getProfileData(Request $request){

		$lang = $request->input('lang');

		if($lang == "ar"){
 			$country_col              = "country_ar_name AS country_name";
			$city_col                 = "city_ar_name AS city_name";
			$cat_col                  = "categories.cat_ar_name";
			$delivery_method_col      = "delivery_methods.method_ar_name";
			$msg = array(
				0 => '',
				1 => 'توكن المستخدم مطلوب',
 				2 => 'لا يوجد بيانات'
			);
		}else{
 			$country_col = "country_en_name AS country_name";
			$city_col    = "city_en_name AS city_name";
			$cat_col     ="categories.cat_en_name";
			$delivery_method_col      = "delivery_methods.method_en_name";
			$msg = array(
				0 => '',
				1 => 'access_token is required',
 				2 => 'There is no data'
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
		}else{

         
           $providerData = DB::table("providers") 
                             ->where('provider_id',$this->get_id($request,'providers','provider_id'))
                             ->join("city" , "providers.city_id" , "city.city_id")
                             ->select(
										"providers.provider_id",
										"providers.full_name AS provider_name",
										'providers.phone',
										'providers.category_id',
										'providers.store_name',
										'providers.longitude',
										'providers.latitude',
										'providers.country_code',
										'providers.country_id',
										'providers.token AS access_token',
										'providers.city_id',
										'providers.delivery_price',
										'providers.delivery_method_id',
										'city.' .$city_col,
		
										DB::raw("CONCAT('". url('/') ."','/providers/',providers.profile_pic) AS profile_pic")
									)	
                             ->first();

                             

                               
 
			$providerCountry  = $providerData->country_id;
			$providerCity     = $providerData->city_id;
			$providerCategory = $providerData->category_id;


                // get all countries and add choosen to provider registed country

			$countries       = 
			          DB::table('country')
			                ->where('publish', 1)
			                ->select(
			                	      'country_id',
			                	       $country_col, 
			                	       DB::raw('IF(country_id = '.$providerCountry.', true, false) AS choosen'), 
			                	       'country_code'
			                	   )
			                ->get();


             //get main categories
		$cats = Categories::where('publish', 1)
		              ->select(
			         'cat_id',
			          $cat_col,
			           DB::raw('IF(cat_id = '.$providerCategory.', true, false) AS choosen')

			     )->get();

  


			$cities          = 

			         DB::table('city')
			                  ->select(
			                  	'city_id', 
			                  	$city_col,
			                  	 DB::raw('IF(city_id = '.$providerCity.', 1, 0) AS choosen')
			                  	)->get();


	        //get delivery Methods available 
	 	$delivery_methods = DB::table("delivery_methods")
	 	                        ->    select('method_id',$delivery_method_col)
                                ->    get();


                    
                  if(isset($delivery_methods) && $delivery_methods -> count() > 0)  {

                           
                            foreach ($delivery_methods as $key => $method) {

                            if ($providerData -> delivery_method_id  ==  $method -> method_id ) {
                            	        

                            	  $method -> choosen = 1; 
                                  
                                if($method -> method_id == 2)
                            	  $method -> price = $providerData -> delivery_price;       

                            }else{
  
                                    $method -> choosen = 0;       
 
                            } 
                    	   
                    }    

                  }
                  		                  

			 
			return response()->json([
										'status'       => true,
										'errNum'       => 0,
										'msg' 		   => $msg[0],
										'data'         => $providerData,
										'countries'    => $countries,
										'cities'       => $cities,
										'cats'         => $cats,
										'delivery_methods' => $delivery_methods

 									]);
		}
	}

	public function UpdateProfile(Request $request){

 
		$lang = $request->input('lang');

		if($lang == "ar"){
			$msg = array(
				0 => 'تم تعديل البيانات بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'الدولة غير موجودة ',
				3 => 'المدينة  غير موجودة',
				4 => '',
				5 => 'فشلت العمليه من فضلاك حاول لاحقا',
				6 => 'نوع التوصيل غير موجود ',
				7 => 'التصنيف غير موجود ',
				8 => ' التوصيل لابد ان يكون رقم صحيح ',
				9 => 'صوره الملف الشخصي غير صالحة ',
				10 => 'رقم الهاتف  لابد ان يكون ارقام '
			);
		}else{
			$msg = array(
				0 => 'Updated successfully',
				1 => 'All fields are required',
 				2 => 'Country doesn\'t exists',
				3 => 'city doesn\'t exists',
 				4 => '',
				5 => 'Failed to update, please try again later',
				6 => 'delivery method not exists',
				7 => 'category not exists',
				8 => 'delivery_method_id must be numeric',
				9 => 'profile image  not valid',
				10 => 'phone number must be numeric'


			);
		}

		$messages = array(

			'required'                  => 1,
 			'country_id.exists'         => 2,
 			'city_id.exists'            => 3,
 			'delivery_method_id.exists' => 6,
 			'category_id.exists'        => 7,
 			'delivery_method_id.numeric'=> 8,
 			'mimes'                     => 9,
 			'numeric'                   => 10


		);



		$rules=[

			'access_token'     =>  'required',
 			'full_name'        => 'required',
 			'store_name'       => 'required',
  			'longitude'        => 'required',
 			'latitude'         => 'required',
			'country_id'       => 'required|exists:country,country_id',
			'category_id'      => 'required|exists:categories,cat_id',
            'city_id'          => 'required|exists:city,city_id',
            'delivery_method_id'     => 'required|numeric|exists:delivery_methods,method_id'
 
		];

	

      $provider = DB::table("providers") 
                           ->where('provider_id',$this->get_id($request,'providers','provider_id'));
      


       $input = $request->only('full_name' , 'phone', 'city_id' ,'category_id','store_name','country_id','longitude','longitude');

         
        $input['country_code'] ='+'.$request->input('country_code');


        if($input['phone'] != $provider ->first()->  phone){

            $rules['phone'] = "required|unique:providers,phone|numeric";
             $rules['country_code'] = "required";

        }else{

            $rules['phone'] = "required|numeric";
             $rules['country_code'] = "required";

        }


        if($request -> profile_pic){
 

            $rules['profile_pic'] = "mimes:jpeg,png,jpg";

        } 



               //if delivery method is 2 this required price field in the request 
		if($request -> delivery_method_id == 2){
              

                  $rules['delivery_price'] = 'required';

		}

 
 
     	$validator = Validator::make($request->all(), $rules ,$messages);

		if($validator->fails()){
			 $error = $validator->errors() ->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

        
         if($input['phone'] != $provider ->first() ->  phone){

            $code = $this -> generate_random_number(4);

            $input['activate_phone_hash'] = json_encode([
                'code'   => $code,
                'expiry' => Carbon::now()->addDays(1)->timestamp,
            ]);

            $input['phoneactivated'] = "0";

            $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                $code . "رقم الدخول الخاص بك هو :- " ;

            (new SmsController())->send($message , $provider ->first()-> phone);

            $isPhoneChanged = true;
        }else{
            $isPhoneChanged = false;
        }

        

                  $input['delivery_method_id']     = $request-> delivery_method_id ;
	 				 

                             // delivery required additional price 
					if($input['delivery_method_id'] == 2){
 
                      $input['delivery_price']     = $request-> delivery_price;                           
					}else{

			                $input['delivery_price']     = 0;
                     }

 

         $nameOfImage = "avatar_ic.png";

        if($request-> profile_pic ){


            $image  = $request -> profile_pic ;

 
            if($provider ->first() -> profile_pic != null && $provider ->first() -> profile_pic != ""){
                    
		                //delete the previous image from storage 
		               if(Storage::disk('providers')->exists($provider ->first()  -> profile_pic))
		               {
		                     
		                     Storage::disk('providers')->delete($provider ->first()  -> profile_pic);

		               }
 

              //save new image   
                    $image ->store('/','providers');
                                       
                   $nameOfImage = $image ->hashName();

 
            }

            

                   $input['profile_pic'] =  $nameOfImage;


 
    }  
       
            $provider -> update($input);


          $getProvider  =  $provider -> first();

				$providerData = array(
					'id'              => $getProvider->provider_id,
					'provider_name'   => $getProvider->full_name,
					'store_name'      => $getProvider->store_name,
 					'phone' 		  => $getProvider->phone,
					'country_code'    => $getProvider->country_code,
 					'longitude'       => $getProvider->longitude,
					'latitude' 		  => $getProvider->latitude,
                    'commercial_photo'               =>env('APP_URL').'/public/providerProfileImages/'.$getProvider->commercial_photo,
					'membership_id' 		      => $getProvider->membership_id,
					'delivery_method_id' 		  => $getProvider->delivery_method_id,
					'category_id' 		          => $getProvider->category_id,
					'phoneactivated'              => $getProvider->phoneactivated,
					'known_phone'                 => $getProvider->known_phone,
					'access_token'                => $getProvider->token,
					'delivery_price'              => $getProvider->delivery_price,
                    'status'                      => $getProvider->status, 
				    'publish'                     => $getProvider->publish, 
					'country_id'      => $getProvider->country_id,
					'city_id'         => $getProvider->city_id,
 					'provider_rate'   => $getProvider->provider_rate,
 					'profile_pic'     =>  env('APP_URL').'/public/providerProfileImages/'.$getProvider->profile_pic ,


 					'created_at'      => date('Y-m-d h:i:s', strtotime($getProvider->created_at))

				);


               //isPhoneChanged to notify mobile  app developers to redirect to activate phone number page 
  
           return response()->json([

           	     'status' => true, 
           	     'errNum' => 0, 
           	     'msg' => $msg[0] ,
           	     'provider' => $providerData,
           	     'isPhoneChanged' => $isPhoneChanged
 
           	 ]);
 
  	}


 

 
	       //provider categories functions 

	public function getProviderMainCats(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "categories.cat_ar_name AS cat_name";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "categories.cat_en_name AS cat_name";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

        
        $provider_id = $this->get_id($request,'providers','provider_id');

		$selected = 'IF((SELECT count(provider_id) FROM providers WHERE category_id = categories.cat_id AND providers.provider_id = '.$provider_id .') > 0,1,0) AS choosen';
 

		$maincategory = DB::table('categories') 
						    -> where('categories.publish',1)
 						    ->select(
								    	'categories.cat_id', 
								    	$cat_col,
								    	DB::raw($selected) 
						            )
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'maincat' => $maincategory]);
	}


	public function getProviderStoreCategories(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "categories_stores.store_cat_ar_name AS store_cat_name";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "categories_stores.store_cat_en_name AS store_cat_name";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


 

		$providerCats = DB::table('providers') 
						    -> join('categories_stores','providers.provider_id','categories_stores.provider_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
						    ->select('categories_stores.id AS cat_id', $cat_col)
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'cats' => $providerCats]);
	}

	 
 
public function addProviderCategory(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه التصنيف بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 =>  'فشل في اضافه التصنيف '
			);
		}else{
			$msg = array(
				0 => 'Category added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'Failed to add Category',
				 
			);
		}

		$messages = array(
			'required'       => 1,
			'access_token'   => 2
		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'store_cat_ar_name'    => 'required',
			'store_cat_en_name'    => 'required',
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('store_cat_ar_name','store_cat_en_name');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
 

			try {
 

				$id=DB::table('categories_stores') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'cat_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}




public function editProviderCategory(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم التصنيف ',
				4 =>  'التصنيف غير موجود '
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'category id  required',
				4 => 'category not found ',

				 
			);
		}

		$messages = array(
						
			'required'                 => 1,
			'access_token.required'    => 2,
			'cat_id.required'          => 3,
			'cat_id.exists'            => 4

		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'cat_id'               => 'required|exists:categories_stores,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('cat_id');


         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $cat = DB::table('categories_stores') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> cat_id

                                         ])
                               -> select(
                               	           'id',
                               	           'store_cat_ar_name',
                               	           'store_cat_en_name'
                               
                                        ) 
                               -> first();



             if(!$cat){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $cat]);

 
		
	}

public function updateProviderCategory(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  التصنيف بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم التصنيف ',
				4 =>  'فشل في  تعديل  التصنيف ',
				5 => 'ألتصنيف غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Category updated successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'category id  required',
				4 => 'Failed to update Category',
				5 => 'Category not exists'

				 
			);
		}

		$messages = array(
			
			
			'required'                 => 1,
			'access_token.required'    => 2,
			'cat_id.required'          => 3,
			'cat_id.exists'            => 5

		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'store_cat_ar_name'    => 'required',
			'store_cat_en_name'    => 'required',
			'cat_id'               => 'required|exists:categories_stores,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('store_cat_ar_name','store_cat_en_name');


         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $cat = DB::table('categories_stores') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> cat_id

                                         ]) ;


             if(!$cat -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $cat -> update($inputs);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}


	

public function deleteProviderCategory(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  حذف  التصنيف بنجاح ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم التصنيف ',
				3 =>  'فشل في  حذف   التصنيف ',
				4 => 'ألتصنيف غير موجود ',

			);
		}else{
			$msg = array(
				0 => 'Category delete successfully',
				1 => 'access_token required',
				2 => 'category id  required',
				3 => 'Failed to delete Category',
				4 => 'Category not exists'
				 
			);
		}

		$messages = array(
			
			
			'access_token.required'    => 1,
			'cat_id.required'          => 2,
			'cat_id.exists'            => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'         => 'required',
			'cat_id'               => 'required|exists:categories_stores,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $cat = DB::table('categories_stores') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> cat_id

                                         ]) ;


             if(!$cat -> first()){
 
                return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
             }





                
			try {
 
                   $cat -> delete();     

                   DB::table('products')-> where('category_id',$request -> cat_id) -> delete();

 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}




////////// provider offers apis ///////////////


	public function getProviderOffers(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "providers_offers.offer_title";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "providers_offers.offer_title";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


 

		$providerOffers = DB::table('providers') 
						    -> join('providers_offers','providers.provider_id','providers_offers.provider_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
						    ->select(

						    	'providers_offers.id AS offer_id',
						    	 $cat_col,
						    	 DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo"),

						    	  'start_date',
						    	  'end_date',
						    	  'paid',
						    	  'paid_amount',
						    	  'providers_offers.status'
						    	  
						    	)
						    -> get();



		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'offers' => $providerOffers]);
	}



public function addProviderOffer(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه  العرض  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 => 'تاريخ  بدأ  ونهاية العرض لابد ان يكون علي الشكل  (yyyy-mm-dd H:i:s)' ,
				4 => 'صورة غير صالحة ',
				5 =>   'تاريح بدا العرض اكبر من تاريخ انتهاء العرض ',
				6 =>   'لابد ان يكون تاريج بدا العرض اكبر من او  يساوي تاريخ اليوم ' ,
				7 =>  'فشل في اضافه  العرض '
			);
		}else{
			$msg = array(
				0 => 'Offer added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>  'start and end date  must be in format (yyyy-mm-dd H:i:s)',
				4 =>  'image not valid' ,
				5 => 'start date greater than end date ',
				6 =>  'start date of the offer must greater  than or equal to  today',
				7 =>'Failed to add Offers'
				 
			);
		}

		$messages = array(

			'required'                => 1,
			'access_token.required'   => 2,
			'date_format'             => 3,
			'mimes'                   => 4,
			'after'                   => 5,
			'after_or_equal'          => 6

		);

         $rules=[

			'access_token'         => 'required',
			'offer_title'          => 'required',
 			'start_date'           => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d'),
			'end_date'             => 'required|date_format:Y-m-d H:i:s|after:start_date',
			'photo'                => 'required|mimes:jpeg,png,jpg'
   
		    ];


		$validator = Validator::make($request->all(),$rules, $messages);

 
		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('offer_title','start_date','end_date');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
             

              if($request-> photo ){


			            $image  = $request -> photo ;

						//save new image   
						$image ->store('/','offers');

						$nameOfImage = $image ->hashName();

						$inputs['photo'] =  $nameOfImage;

			    }  

 
           
			try {
 

				$id=DB::table('providers_offers') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'offer_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
			}
		
	}




	public function payProviderOffer(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم   تغيير حاله العرض الي مدفوع ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  العرض ',
				3 =>  'كل الحقول مطلوبة ',
				4 =>  'فشل في   تغيير حالة الطلب  ',
				5 => 'العرض غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'offer status changed successfully',
				1 => 'access_token required',
				2 => 'offer id  required',
				3 =>  'All fields required', 
				4 =>  'Failed to change status',
				5 =>  'Offer not exists',
				 
			);
		}

		$messages = array(
			 
			'access_token.required'      => 1,
			'offer_id.required'          => 2,
			'required'                   => 3,
			'offer_id.exists'            => 5

		);

		$validator = Validator::make($request->all(),[

			'access_token'           => 'required',
			'offer_id'               => 'required|exists:providers_offers,id' ,
			'paid_amount'            => 'required'
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // offer_id

              $offer = DB::table('providers_offers') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> offer_id
                                         ]) ;


             if(!$offer -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $offer -> update([
                           

                             'paid'          => '1',
                             'paid_amount'   => $request -> paid_amount,
                             'status'        => '2'

                   	    ]);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}

 

	public function stopProviderOffer(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  ايقاف العرض ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  العرض ',
				3 =>  'كل الحقول مطلوبة ',
				4 =>  'فشل في   ألعرض  ',
				5 =>  'لا يمكنك ايقاف الطلب قبل موافقه الاداره علية اولا ',
				6 => 'العرض غير موجود '
			);
		}else{
			$msg = array(
				0 => 'offer  stoped successfully',
				1 => 'access_token required',
				2 => 'offer id  required',
				3 =>  'All fields required', 
				4 =>  'Failed to stop offer',
				5 => 'cant\'t  stop offers that not accepted from admin ',
				6 => 'offer not exists'
				 
			);
		}

		$messages = array(
			 
			'access_token.required'      => 1,
			'offer_id.required'          => 2,
			'required'                   => 3,
			'offer_id.exists'            => 6

		);

		$validator = Validator::make($request->all(),[

			'access_token'           => 'required',
			'offer_id'               => 'required|exists:providers_offers,id' ,
 			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // offer_id

              $offer = DB::table('providers_offers') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> offer_id
                                         ]) ;


             if(!$offer -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


             if($offer -> first() -> publish == 0 ){


             	  return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
             }


                
			try {
 
                   $offer -> update([
                           

                             'publish'          => '0',
                             

                   	    ]);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}



	  //provider product APIS

	public function addProduct(Request $request){

		$lang = $request->input('lang');


	 if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه المنتج بنجاح ',
				1 => 'جميع الحقول مطلوبة ',
				2 => 'السعر لابد ان يكون ارقام ',
				3 => ' القسم المختار غير موجود ',
				4 => ' الصور لابد ان تكون علي هيئة  مصفوفه ',
				5 => 'امتداد الصوره غير مسموح به ',
				6 => 'التفضيلات لابد ان  تكون علي شكل مصفوفه ',
				7 => ' الاحجام  لابد ان  تكون علي شكل مصفوفه ',
				8 => ' الالوان  لابد ان  تكون علي شكل مصفوفه ',
				9 =>  ' سعر الاضافات مطلوب في حال وجود اضافه ',
				10 => 'سعر الحجم  مطلوب  في حال وجود احجام ',
				11 => 'سعر اللون  مطلوب في حال وجود الوان ',
				12 => ' فشل في رفع الصوره من فضلك حاول مجددا ',
				13 => 'فشل في اضافه المنتج من فضلك حاول مجددا ',
				14 => 'access_token not found',
				15 => 'رقم المتجر غير موجود ',
				16 => 'ألتصنيف غير موجود ' ,
				17 => ' اقصي حجم مسموح به في الصور هو 20000' ,
				18 => 'المفضلات لابد ان تكون ضافه وحده علي الاقل  ' ,
				19 => 'الحجوم لابد ان تكون تكون حجم واخد علي الاقل ' ,
				20 => 'الالوان لابد ان تكون تكون لون واحد ع الاقل ' ,
				21 => 'اسعار المفضلات لابد ان تكون مصفوفه ' ,
				22 => ' اسعار الحجوم لابد ان تكون مصفوفه ' ,
				23 => ' اسعار الالوان لابد ان تكون مصفوفه ' ,
			);
		}else{
			$msg = array(
				0 => 'Product added successfully',
				1 => 'All fields are required',
				2 => 'price must be a number',
				3 => 'category_id not exists',
				4 => 'product images must be array ',
				5 => 'image extension not allowed',
				6 => 'options must be array',
				7 => 'sizes must be array',
				8 => 'colors must be array',
				9 => 'option price  required when option added',
				10 => 'size price  required when sizes added',
				11 => 'color price  required when colors added',
				12 => 'Failed to upload image, please try again later',
				13 => 'Failed to add the product , please try again later',
				14 => 'access_token not found',
				15 => 'store does\'t exist' ,
				16 => ' category not exists' ,
				17 => 'max image size is 20000' ,
				18 => 'options must be at least one option' ,
				19 => 'sizes must be at least one size' ,
				20 => 'colors must be at least one color' ,
				21 => 'options prices must pass as array ' ,
				22 => ' sizes prices must pass as array ' ,
				23 => 'colors prices must pass as array' ,
				

			
				 
			);
		}

		$messages = array(
			'access_token.required'    => 14,
			'required'                 => 1,
			'numeric'                  => 2,
			'exists'                   => 3,
			'product_images.array'     => 4,
			'mimes'                    => 5,
			'options.array'            => 6,
			'sizes.array'              => 7,
			'colors.array'             => 8,
			'options_price.required_with' => 9,
			'sizes_price.required_with'   => 10,
			'colors_price.required_with'  => 11,
			'category_id.exist'           => 16,
			'max'                         => 17,
			'options.min'                 => 18,
			'sizes.min'                   => 19,
			'colors.min'                  => 20,
			'options_price.array'         => 21,
			'sizes_price.array'           => 22,
			'colors_price.array'          => 23

			   


		);

		$validator = Validator::make($request->all(),[
			'access_token'   => 'required',
 			'title'          => 'required',
			'category_id'    => 'required|exists:categories_stores,id',
			'description'    => 'required',
 			'price'          => 'required|numeric',
 			'product_images' => 'required',
 			'product_images'   => 'required|array',
            'product_images.*' => 'required|max:20000|mimes:png,jpeg,jpg,gif',

            'options'         => 'array|min:1',
            'options_price'   => 'array|required_with:options',
            'sizes'   => 'array|min:1',
            'sizes_price'   => 'array|required_with:sizes',
            'colors'  => 'array|min:1',
            'colors_price'   => 'array|required_with:colors'


 		],$messages);

		if($validator->fails()){
		      $error = $validator->errors()-> first()  ;

			 
		   return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

		
       $provider_id = $this->get_id($request,'providers','provider_id');

       $provider    = DB::table("providers") 
                           ->where('provider_id',$provider_id);

          if(! $provider -> first()){
 
                  return response()->json(['status'=> false, 'errNum' => 15, 'msg' => $msg[15]]);

          }                 
       
       $id =0; 

       $id = DB::table('products') -> insertGetId([
                        
                        'provider_id'    => $provider_id,
						'title'          => $request  ->  title,
						'category_id'    => $request  ->  category_id,
						'price'          => $request  ->  price,
						'description'    => $request  ->  description,

 					]);

        if($id ==0 )
        {
           
            return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
        	
        }


 			if( $request -> hasFile('product_images')){
 
  
				foreach($request->  product_images  AS $image){
  
                   //save new image   
                    $image ->store('/','products');
                                       
                   $nameOfImage = $image ->hashName();

                     DB::table('product_images') -> insert([

                     	  'image'      => $nameOfImage,
                     	  'product_id' => $id

                     ]);
 
 
					} 
 	  
				}


           try {

                  //store options and its price if availble 

				    if( $request -> has('options')){
   
				    	$this -> storeOptionsWithPrices($request -> options ,$request -> options_price,$id);
   
                     }



                      //store sizes and its price if availble 

				    if( $request -> has('sizes')){
   
				    	$this -> storeSizesWithPrices($request -> sizes ,$request -> sizes_price,$id);
   
                     }

 
                      //store options and its price if availble 

				    if( $request -> has('colors')){
   
				    	$this -> storeColorsWithPrices($request -> colors ,$request -> colors_price,$id);
   
                     }

               } catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 13, 'msg' => $msg[13]]);
			}
			      

          return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0] ,'product_id' => $id]);
 
 
}




public function getProducts(Request $request){

   $lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "products.title";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "products.title";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}



    //products with last inserted image
 

		  $products = DB::table('providers') 
		                    -> join('products','providers.provider_id','=','products.provider_id')
						    -> join('product_images','products.id','product_images.product_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
 						    ->where('products.publish',1)
						    ->select(

							    	'products.id AS product_id',
							    	 $cat_col,
							    	 DB::raw("CONCAT('". url('/') ."','/products/',product_images.image) AS product_image"),
							    	 'products.description',
							    	 'providers.provider_id'
						    	   
						    	)
						    -> paginate(10);




  return response()->json(['status' => true, 'errNum' => 0, 'msg' => '','products' => $products]);
 

}



public function delete_Product(Request $request){
       

       $lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  حذف   المنتج بنجاح ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  المنتج  ',
				3 =>  'فشل في  حذف   المنتج  ',
				4 => 'هذا المنتج غير موجود '
			);
		}else{
			$msg = array(
				0 => 'Product delete successfully',
				1 => 'access_token required',
				2 => 'Product id  required',
				3 => 'Failed to delete Product',
				4 => 'this product doen\'t exists'
				 
			);
		}

		$messages = array(
			
			
			'access_token.required'        => 1,
			'product_id.required'          => 2,
			'exists'                       => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'            => 'required',
			'product_id'               => 'required|exists:products,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
               
              $product = DB::table('products') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> product_id

                                         ]) ;


             if(!$product -> first()){
 
                return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
             }


                
			try {
 
                   $product -> delete();   

                   DB::table('product_images') -> where('product_id',$request -> product_id) -> delete();
                   DB::table('product_options') -> where('product_id',$request -> product_id) -> delete();
                   DB::table('product_sizes') -> where('product_id',$request -> product_id) -> delete();
                   DB::table('product_colors') -> where('product_id',$request -> product_id) -> delete();
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
 
  
}




public function prepare_Product_Update(Request $request){

 		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  المنتج  ',
				4 =>  ' المنتج  غير موجود ',
				5 => 'المتجر غير موجود '
			);


			$cat_col = 'categories_stores.store_cat_ar_name';


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'product id  required',
				4 => 'product not found ',
				5 => 'provider not found '
				 
			);

			$cat_col = 'categories_stores.store_cat_en_name';
		}

		$messages = array(
						
			'required'                        => 1,
			'access_token.required'           => 2,
			'product_id.required'             => 3,
			'product_id.exists'               => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'             => 'required',
 			'product_id'               => 'required|exists:products,id' 
			
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
         


          $provider_id = $this->get_id($request,'providers','provider_id');

          $provider    = DB::table("providers") 
                           ->where('provider_id',$provider_id);


       if(!$provider){
              

           return response()->json(['status' => false, 'errNum' =>5 , 'msg' => $msg[5]]);
       }


 
		$selected = 'IF((SELECT count(id) FROM products WHERE category_id = categories_stores.id AND products.id = '.$request-> product_id .') > 0,1,0) AS choosen';


		$product = DB::table('products') 
		             -> where('products.id', $request-> product_id)
					 ->join('providers', 'products.provider_id', '=', 'providers.provider_id')
					 -> where('providers.provider_id',$provider_id)
					 ->select('products.title', 
					 	      'products.description',
					 		  'products.price')
					 ->first();


		$images = DB::table('product_images')
		                 ->where('products.id', $request-> product_id)
						 ->join('products', 'product_images.product_id','products.id')
						 ->select( 'product_images.id AS  image_id',
 						 	        DB::raw("CONCAT('". url('/') ."','/products/',product_images.image) AS product_image")
						 	)->get();



		$categories = DB::table('categories_stores') 
		                         ->join('providers','categories_stores.provider_id','providers.provider_id')
		                         ->where('categories_stores.provider_id',$provider_id)
		                         ->select(
		                         	       'categories_stores.id AS cat_id',
		                         	       $cat_col,
		                         	       DB::raw($selected)


		                                 )
		                         -> get();

 

		return response()->json([
								 'status' => true,
								 'errNum' => 0,
								 'msg' => '',
								 'product' => $product,
								 'images' => $images,
								 'cats' => $categories
								]);
         
}




public function updateProduct(Request $request){

		$lang = $request->input('lang');
 
	 if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  المنتج بنجاح ',
				1 => 'جميع الحقول مطلوبة ',
				2 => 'السعر لابد ان يكون ارقام ',
				3 => ' القسم المختار غير موجود ',
				4 => ' الصور لابد ان تكون علي هيئة  مصفوفه ',
				5 => 'امتداد الصوره غير مسموح به ',
				6 => 'التفضيلات لابد ان  تكون علي شكل مصفوفه ',
				7 => ' الاحجام  لابد ان  تكون علي شكل مصفوفه ',
				8 => ' الالوان  لابد ان  تكون علي شكل مصفوفه ',
				9 =>  ' لابد من ادحال سعر التفضيلات   في حال وجود تفضيلات ',
				10 => 'سعر الحجم مطبوي في حال وجود احجام ',
				11 => 'سعر اللون  مطلوب في حال وجود الوان ',
				12 => ' فشل في رفع الصوره من فضلك حاول مجددا ',
				13 => 'فشل في  تعديل  المنتج من فضلك حاول مجددا ',
				14 => 'توكن المستخدم غير موجود ',
				15 => ' رقم المتجر غير موجود ' ,
				16 => 'الصور المحذوفه لابد ان تكون مرره علي شكل مصفوفه ',

				17 => ' اقصي حجم مسموح به في الصور هو 20000' ,
				18 => 'المفضلات لابد ان تكون ضافه وحده علي الاقل  ' ,
				19 => 'الحجوم لابد ان تكون تكون حجم واخد علي الاقل ' ,
				20 => 'الالوان لابد ان تكون تكون لون واحد ع الاقل ' ,
				21 => 'اسعار المفضلات لابد ان تكون مصفوفه ' ,
				22 => ' اسعار الحجوم لابد ان تكون مصفوفه ' ,
				23 => ' اسعار الالوان لابد ان تكون مصفوفه ' ,
				24 => 'ألتصنيف غير موجود ' ,
			);
		}else{
			$msg = array(
				0 => 'Product updated successfully',
				1 => 'All fields are required',
				2 => 'price must be a number',
				3 => 'category_id not exists',
				4 => 'product images must be array ',
				5 => 'image extension not allowed',
				6 => 'options must be array',
				7 => 'sizes must be array',
				8 => 'colors must be array',
				9 => 'option price  required when option added',
				10 => 'size price  required when sizes added',
				11 => 'color price  required when colors added',
				12 => 'Failed to upload image, please try again later',
				13 => 'Failed to update the product , please try again later',
				14 => 'access_token not found',
				15 => 'store does\'t exist' ,
				16 => 'deleted images must be array ',
				17 => ' اقصي حجم مسموح به في الصور هو 20000' ,
				18 => 'المفضلات لابد ان تكون ضافه وحده علي الاقل  ' ,
				19 => 'الحجوم لابد ان تكون تكون حجم واخد علي الاقل ' ,
				20 => 'الالوان لابد ان تكون تكون لون واحد ع الاقل ' ,
				21 => 'اسعار المفضلات لابد ان تكون مصفوفه ' ,
				22 => ' اسعار الحجوم لابد ان تكون مصفوفه ' ,
				23 => ' اسعار الالوان لابد ان تكون مصفوفه ' ,
				24 => 'ألتصنيف غير موجود ' ,

			
				 
			);
		}

		$messages = array(
			'access_token.required'    => 14,
			'required'                 => 1,
			'numeric'                  => 2,
			'exists'                   => 3,
			'product_images.array'     => 4,
			'mimes'                    => 5,
			'options.array'            => 6,
			'sizes.array'              => 7,
			'colors.array'             => 8,
			'options_price.required_with' => 9,
			'sizes_price.required_with'   => 10,
			'colors_price.required_with'  => 11,
			'deleted_images.array'        => 16,

			'max'                         => 17,
			'options.min'                 => 18,
			'sizes.min'                   => 19,
			'colors.min'                  => 20,
			'options_price.array'         => 21,
			'sizes_price.array'           => 22,
			'colors_price.array'          => 23,
			'category_id.exist'           => 24

		);

		$validator = Validator::make($request->all(),[

			'access_token'     => 'required',
 			'title'            => 'required',
			'category_id'      => 'required|exists:categories_stores,id',
			'product_id'       => 'required|exists:products,id',  
			'description'      => 'required',
 			'price'            => 'required|numeric',
 			'product_images'   => 'required',
 			'product_images'   => 'required|array',
            'product_images.*' => 'required|max:20000|mimes:png,jpeg,jpg,gif',
            'options'          => 'array|min:1',
            'options_price'    => 'array|required_with:options',
            'sizes'            => 'array|min:1',
            'sizes_price'      => 'array|required_with:sizes',
            'colors'           => 'array|min:1',
            'colors_price'     => 'array|required_with:colors',
            'deleted_images'   => 'nullable'


 		],$messages);

		if($validator->fails()){
		      $error = $validator->errors()-> first()  ;

			 
		   return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
    

          
      $provider_id = $this->get_id($request,'providers','provider_id');

       $provider    = DB::table("providers") 
                           ->where('provider_id',$provider_id);
       $id          = $request -> product_id;                    

          if(! $provider -> first()){
 
                  return response()->json(['status'=> false, 'errNum' => 15, 'msg' => $msg[15]]);

          }        


              // delete array of image from database and storage 
 
              /* if($request->has('deleted_images')){

               	        //remove null values if exists 
 
                       

               	        $deleted_images = array_filter($request -> deleted_images,function($deleted_images){

                                 $image_exists = DB::table('product_images') -> where('id',$deleted_images);


                                if(!empty($deleted_images) && $image_exists){


                                	return $deleted_images;
                                }

			            });
  
                         
                         DB::table('product_images')
				         ->whereIn('id',$deleted_images)->delete();
                         

                        foreach ($deleted_images as $key => $images) {
                        	   
		                      $image =  DB::table('product_images')
						         ->where('id',$images);

						       $name = $image -> first() ->  image;

						       //delete from storage to space disk free 

						       if(Storage::disk('products')->exists($name))
					               {
					                     
					                    Storage::disk('products')->delete($name);

					               }

                                  //delete from database 
						       $image -> delete();  
  
                        }
				        

			    }*/

          $data=$request -> only('title','description','category_id','description','price');

 		  $data['provider_id']    =  $provider_id;               


             //delete previous images
 		   $image =  DB::table('product_images')
						         ->where('product_id',$request -> product_id)
						         ->delete();



 			if( $request -> hasFile('product_images')){
 
                    
				foreach($request->  product_images  AS $image){
  
                   //save new image   
                    $image ->store('/','products');
                                       
                   $nameOfImage = $image ->hashName();

                     DB::table('product_images') -> insert([

                     	  'image'      => $nameOfImage,
                     	  'product_id' => $id

                     ]);
 
 
					} 
 	  
				}
 

 
           try {

                  //store options and its price if availble 

				    if( $request -> has('options')){

				    	 //delete  old options then  store 
				    	   DB::table('product_options') -> where('product_id',$id) ->  delete();
   
				    	$this -> storeOptionsWithPrices($request -> options ,$request -> options_price,$id);
   
                     }

 
                      //store sizes and its price if availble 

				    if( $request -> has('sizes')){

				    	 DB::table('product_sizes') -> where('product_id',$id) ->  delete();
   
				    	$this -> storeSizesWithPrices($request -> sizes ,$request -> sizes_price,$id);
   
                     }

 
                      //store options and its price if availble 

				    if( $request -> has('colors')){

				    	DB::table('product_colors') -> where('product_id',$id) ->  delete();
   
				    	$this -> storeColorsWithPrices($request -> colors ,$request -> colors_price,$id);
   
                     }

                     DB::table('products') -> where('id',$id) -> update($data);

               } catch (Exception $e) {

				return response()->json(['status'=> false, 'errNum' => 13, 'msg' => $msg[13]]);

			}
			   
                     
  

       return response()->json(['status'=> true, 'errNum' => 0, 'msg' =>$msg[0]]);

		 
	}




public function storeOptionsWithPrices($optionsreq , $options_pricereq , $product_id) {


              // because there are array of options passed to request and my be one of index is null so we filter by remove it 


			 $options = array_filter($optionsreq,function($options){

                               return !empty($options);

			            });

			 $options_price = array_filter($options_pricereq,function($options_price){

                               return !empty($options_price);

			            });
 

				if (count($options)) {

						foreach ($options as $index => $option) {

							//get the  price by index of option name 

							$price = isset($options_price[$index]) ? $options_price[$index] : 0;

                             
                                DB::table('product_options') -> insert([
                                      
                                      'product_id'   =>  $product_id ,
                                      'name'         =>  $option,
                                      'price'        =>  $price

                                   ]);

						}
				}

}




public function storeSizesWithPrices($sizesreq , $sizes_pricereq , $product_id) {


              // because there are array of options passed to request and my be one of index is null so we filter by remove it 


			 $sizes = array_filter($sizesreq,function($sizes){

                               return !empty($sizes);

			            });

			 $sizes_price = array_filter($sizes_pricereq,function($sizes_price){

                               return !empty($sizes_price);

			            });
 

				if (count($sizes)) {

						foreach ($sizes as $index => $size) {

							//get the  price by index of option name 

							$price = isset($sizes_price[$index]) ? $sizes_price[$index] : 0;

                             
                                DB::table('product_sizes') -> insert([
                                      
                                      'product_id'   =>  $product_id ,
                                      'name'         =>  $size,
                                      'price'        =>  $price

                                   ]);

						}
				}

}




public function storeColorsWithPrices($colorsreq , $colors_pricereq , $product_id) {


              // because there are array of options passed to request and my be one of index is null so we filter by remove it 


			 $colors   = array_filter($colorsreq,function($colors){

                               return !empty($colors);

			            });

			 $colors_price = array_filter($colors_pricereq,function($colors_price){

                               return !empty($colors_price);

			            });
 

				if (count($colors)) {

						foreach ($colors as $index => $color) {

							//get the  price by index of option name 

							$price = isset($colors_price[$index]) ? $colors_price[$index] : 0;

                             
                                DB::table('product_colors') -> insert([
                                      
                                      'product_id'   =>  $product_id ,
                                      'name'         =>  $color,
                                      'price'        =>  $price

                                   ]);

						}
				}

}





         //// provider jobs functions /////////

public function providerJobs(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

 			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

 		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


 

		$providerJobs = DB::table('providers') 
						    -> join('provider_jobs','providers.provider_id','provider_jobs.provider_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
						    ->select('provider_jobs.id AS job_id','job_title','job_desc')
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'jobs' => $providerJobs]);
	}

	 
 
public function addProviderJob(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه  الوظيفة  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 =>  'فشل في اضافه   الوظيفة  '
			);
		}else{
			$msg = array(
				0 => 'Job added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'Failed to add Job',
				 
			);
		}

		$messages = array(
			'required'                 => 1,
			'access_token.rrequired'   => 2
		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'job_title'            => 'required|max:200',
			'job_desc'             => 'required|max:50000',

			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('job_title','job_desc');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
 

			try {
 

				$id=DB::table('provider_jobs') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'job_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}




public function editProviderJob(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  ' الوظيفة  غير موجود '
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'Job id  required',
				4 => 'Job not found '
				 
			);
		}

		$messages = array(
						
			
			'access_token.required'    => 2,
			'required'                 => 1,
			'job_id.required'          => 3

		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'job_id'               => 'required' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

          
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> job_id

                                         ])
                               -> select(
                               	           'id',
                               	           'job_title',
                               	           'job_desc'
                               
                                        ) 
                               -> first();



             if(!$job){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $job]);

 
		
	}

public function updateProviderJob(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  الوظيفة  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  'فشل في  تعديل  الوظيفة  ',
				5 => 'الوظيفه غير موجوده '
			);
		}else{
			$msg = array(
				0 => 'Job updated successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'job id  required',
				4 => 'Failed to update job',
				5 => 'Job Not found'
				 
			);
		}

		$messages = array(
			
			
			'required'                 => 1,
			'access_token.required'    => 2,
			'job_id.required'          => 3,
			'job_id.exists'            => 5
			


		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'job_title'            => 'required|string|max:200',
			'job_desc'             => 'required|max:5000',
			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('job_title','job_desc');


         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> job_id

                                         ]) ;


             if(!$job -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $job -> update($inputs);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}


	

public function deleteProviderJob(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  حذف   الوظيفة  بنجاح ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  الوظيفة  ',
				3 =>  'فشل في  حذف    الوظيفة  ',
				4 => 'الوظيفة غير موجودة '
			);
		}else{
			$msg = array(
				0 => 'Category delete successfully',
				1 => 'access_token required',
				2 => 'category id  required',
				3 => 'Failed to delete Category',
				4 => 'Job not Found'

				 
			);
		}

		$messages = array(
			
			
			'access_token.required'    => 1,
			'job_id.required'          => 2,
			'job_id.exists'            => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'         => 'required',
			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> job_id

                                         ]) ;


             if(!$job -> first()){
 
                return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
             }





                
			try {
 
                   $job -> delete();     

  
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}

 

public function getJobDetails(Request $request){

    
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  ' الوظيفة  غير موجود ',
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'Job id  required',
				4 => 'Job not found ',

				 
			);
		}

		$messages = array(
						
			
			'access_token.required'    => 2,
			'required'                 => 1,
			'job_id.required'          => 3,
			'job_id.exists'            => 4


		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
 

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'      =>   $inputs['provider_id'],
                                	         'provider_jobs.id' =>   $request -> job_id

                                         ])
                               ->leftjoin('applicants','provider_jobs.id','=','applicants.job_id')
                               -> select(
                               	           'provider_jobs.id As job_id',
                               	           'job_title',
                               	           'job_desc',
                               	           DB::raw('DATE(provider_jobs.created_at) AS created_date'),
                               	           DB::raw('COUNT(applicants.job_id) AS applicants')
                               
                                        ) 
                               -> first();

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $job]);
 
}



public function jobApplicants(Request $request){


		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  ' الوظيفة  غير موجود ',
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'Job id  required',
				4 => 'Job not found ',

				 
			);
		}

		$messages = array(
						
			
			'access_token.required'    => 2,
			'required'                 => 1,
			'job_id.required'          => 3,
			'job_id.exists'            => 4


		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
  
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
 
              $applicants = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'      =>   $inputs['provider_id'],
                                	         'provider_jobs.id' =>   $request -> job_id

                                         ])
                               ->leftjoin('applicants','provider_jobs.id','=','applicants.job_id')
                               -> select(
                               	           'applicants.id AS applicant_id',
                               	           'applicants.name AS applicant_name',
                               	           DB::raw("CONCAT('".env('APP_URL')."','/public/cvs/', applicants.cv) AS applicant_cv"),

                               	           DB::raw('DATE(applicants.created_at) AS created_date')
                                
                                        ) 
                               -> paginate(10);

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'applicants' => $applicants]);
 

}



 ///////excellent requests functions /////////////////



public function getExcellenceRequests(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				2 => 'لابد من تحديد نوع الطلبات  0 or 1',
				 
			);

 			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
 				2 => 'type field required_with',
				 
			);

 		}

		$messages = array(
			'access_token.required'   => 1,
			'type.required'           => 2,
			'type.in'                 => 2
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			'type'          => 'required|in:0,1'
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

           
           $provider_id = $this->get_id($request,'providers','provider_id');


           $conditions=[];

           array_push($conditions, ['providers.provider_id', '=', $provider_id]);

           if($request -> type == 0)
             array_push($conditions, ['excellence_requests.status', '=', '0']);
            if($request -> type == 1)
            array_push($conditions, ['excellence_requests.status', '=', '1']);	
 
     
		$providerRequests= DB::table('providers') 
						    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
						    -> where($conditions)
 						    ->select(

						    	  'excellence_requests.id AS request_id',    	   	 
						    	  'paid',
						    	  'paid_amount',
						    	  'excellence_requests.start_date',
						    	  'excellence_requests.end_date',
						    	  'excellence_requests.status'

	                              )
						    -> paginate(10);


			if(isset($providerRequests) && $providerRequests -> count() > 0){

				foreach ($providerRequests as $key => $providerRequest) {


					    if($providerRequest -> status == 0)

					    	$providerRequest -> order_date =  DATE('Y-m-d',strtotime($providerRequest ->start_date));
					    else 

					    	$providerRequest -> expire_date =  DATE('Y-m-d',strtotime($providerRequest ->end_date));
  
                            unset($providerRequest ->start_date);
                            unset($providerRequest ->end_date);
 
				}


			}



		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'requests' => $providerRequests]);
	}

 

public function addExcellenceRequests(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه  العرض  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 => 'تاريخ  بدأ  ونهاية العرض لابد ان يكون علي الشكل  (yyyy-mm-dd H:i:s)' ,
				4 => 'صورة غير صالحة ',
				5 =>   'تاريح بدا العرض اكبر من تاريخ انتهاء العرض ',
				6 =>   'لابد ان يكون تاريج بدا العرض اكبر من او  يساوي تاريخ اليوم ' ,
				7 =>  'فشل في اضافه  العرض ',
				8 =>'التصنيف المختار غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Offer added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>  'start and end date  must be in format (yyyy-mm-dd H:i:s)',
				4 =>  'image not valid' ,
				5 => 'start date greater than end date ',
				6 =>  'start date of the offer must greater  than or equal to  today',
				7 =>'Failed to add Offers',
				8 =>'Category choosed not exists',

				 
			);
		}

		$messages = array(

			'required'                => 1,
			'access_token.required'   => 2,
			'date_format'             => 3,
			'mimes'                   => 4,
			'after'                   => 5,
			'after_or_equal'          => 6,
			'main_category_id.exists' => 8
		);

         $rules=[

			'access_token'         => 'required',
			'country_code'         => 'required',
			'main_category_id'     => 'required|exists:categories,cat_id',
			'phone'                => 'required',
			'name'                 => 'required', 
 			'start_date'           => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d'),
			'end_date'             => 'required|date_format:Y-m-d H:i:s|after:start_date'
    
		    ];


		$validator = Validator::make($request->all(),$rules, $messages);

 
		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('name','start_date','end_date','country_code','phone','main_category_id');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
             

              
 
           
			try {
 

				$id=DB::table('excellence_requests') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'request_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
			}
		
	}



public function payExcellenceRequests(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم   تغيير حاله الطلب  الي مدفوع ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  الطلب  ',
				3 =>  'كل الحقول مطلوبة ',
				4 =>  'فشل في   تغيير حالة  الطلب   ',
				5 => 'العرض غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Request  status changed successfully to Paid',
				1 => 'access_token required',
				2 => 'offer id  required',
				3 =>  'All fields required', 
				4 =>  'Failed to change status',
				5 =>  'Request  not exists',
				 
			);
		}

		$messages = array(
			 
			'access_token.required'      => 1,
			'request_id.required'        => 2,
			'required'                   => 3,
			'request_id.exists'          => 5

		);

		$validator = Validator::make($request->all(),[

			'access_token'           => 'required',
			'request_id'             => 'required|exists:excellence_requests,id' ,
			'paid_amount'            => 'required'
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // offer_id

              $exRequest = DB::table('excellence_requests') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> request_id
                                         ]) ;


             if(!$exRequest -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $exRequest -> update([
                           

                             'paid'          => '1',
                             'paid_amount'   => $request -> paid_amount,
                             'status'        => '0'


                   	    ]);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}

 

public function ExcellenceRequestDetails(Request $request){

 
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  طلب التميز   ',
				4 =>  '  رقم الطلب   غير موجود ',
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'request id  required',
				4 => 'request  not found ',
  
			);
		}

		$messages = array(
			 
			'access_token.required'    => 2,
			'required'                 => 1,
			'request_id.required'      => 3,
			'request_id.exists'        => 4
 
		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'request_id'           => 'required|exists:excellence_requests,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

  
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

      $providerRequest = DB::table('excellence_requests') 
                        -> where ([
                        	         'provider_id'           =>   $inputs['provider_id'],
                        	         'excellence_requests.id' =>   $request -> request_id

                                 ])
                        
                       -> select(
                       	           'excellence_requests.id As request_id',
                       	           'excellence_requests.status',
                       	            'paid',
							        'paid_amount',
                       	           DB::raw('DATE(excellence_requests.start_date) AS start_date'),
                       	           DB::raw('DATE(excellence_requests.end_date) AS end_date')
 
                                ) 

                       -> first();
 
                 $coastPerDay  = DB::table('app_settings') -> select('excellence_day_coast') -> first();
 
if($providerRequest){
 
		    if($providerRequest -> status == 0){


		    	$providerRequest -> order_date =  DATE('Y-m-d',strtotime($providerRequest ->start_date));
		     }else {

		    	$providerRequest -> expire_date =  DATE('Y-m-d',strtotime($providerRequest ->end_date));
		    }


                    $start_date = new DateTime($providerRequest  -> start_date);
					$end_date   = new DateTime($providerRequest -> end_date);

					$interval = $start_date -> diff($end_date);
				 	$days     = $interval   -> format('%a');
 
					 $providerRequest -> daysCount = $days;

                     if($coastPerDay -> 	excellence_day_coast > 0){

					   $providerRequest -> daysCount = (int) $days ;
					   $providerRequest -> totalCost =$days * $coastPerDay ->excellence_day_coast;
					}
					 else
					 	$providerRequest -> daysCount = 0;
			}
 
  
		return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $providerRequest]);
 
}
 



 
	public function fetchOrdersCounts(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'provider_id is required'
			);
		}

		if(empty($request->input('provider_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$provider_id = $request->input('provider_id');
		}


		$today = date("Y-m-d");
		
		 
				$get_time_counter = DB::table("app_settings")->first();
				if($get_time_counter != NULL){
					$time_counter_in_hours = $get_time_counter->time_in_hours;
					$time_counter_in_min    = $get_time_counter->time_in_min;
					
 				}
			 else{
				$time_counter_in_hours = 0;
				$time_counter_in_min   = 0;
			}
			
			
			
			
			 
			
		//get new orders count
		$news    = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->where('status_id', 1)
										   /*->where(
                                                         'orders_headers.created_at', '>', Carbon::now()->addHours(1)->subMinutes($time_counter_in_min)
                                                    )*/
										   ->count();


		$current = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->whereIn('status_id', [2,3,8])
  										   ->where(DB::raw('DATE(expected_delivery_time)'), '<=',$today)
										   ->count();

		$futures = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->whereIn('status_id', [2,3,8])
										   ->where(DB::raw('DATE(expected_delivery_time)'), '>' ,$today)
 										   ->count();

		$old = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->whereIn('status_id', [4,5,6,7])
										   ->count();

		$complains = DB::table('complains')->where('provider_id', $provider_id)
										   ->where('delivery_id', 0)
										   ->count();
		return response()->json([
									'status' => true,
									'errNum' => 0,
									'msg'    => $msg[0],
									'new_orders_count'     => $news,
									'current_orders_count' => $current,
									'future_orders_count'  => $futures,
									'old_orders_count'     => $old,
									'complains_count'      => $complains
								]);
	}

	public function getProviderOrders(Request $request){
	    
 	  $lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب',
				2 => 'نوع الطلبات مطلوب',
				3 => 'نوع العمليه يجب ان يكون 1 او 2 او 3 او 4',
				4 => 'لا يوجد طلبات بعد'
			);
			$payment_col  = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$status_col	  = "order_status.ar_desc AS status_text";
		}else{
			$msg = array(
				0 => '',
				1 => 'provider_id is required',
				2 => 'type is required',
				3 => 'type must be 1, 2, 3 or 4',
				4 => 'There is no ordes yet'
			);
			$payment_col  = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$status_col	  = "order_status.en_desc AS status_text";
		}

		$messages  = array(
			'provider_id.required' => 1,
			'type.required'        => 2,
			'in'                   => 3
		);
		$validator = Validator::make($request->all(), [
			'provider_id' => 'required',
			'type'        => 'required|in:1,2,3,4'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$provider_id  = $request->input('provider_id');
			$type 		  = $request->input('type');
			$today        = date('Y-m-d');
			$conditions[] = ['providers.provider_id','=', $provider_id];
			$inCondition = [];
			if($type == 1){
			    
			     
			    
				$inCondition = [1];
				$get_time_counter = DB::table("app_settings")->first();
				if($get_time_counter != NULL){
					$time_counter_in_hours = $get_time_counter->time_in_hours;
					$time_counter_in_min    = $get_time_counter->time_in_min;
					
 				
			}else{
				$time_counter_in_hours = 0;
				$time_counter_in_min   = 0;
			}

				
			 
			 
			//  array_push($conditions, [DB::raw('orders_headers.created_at') , '>', Carbon::now()->addHours(1)->subMinutes($time_counter_in_min)]);
				  
 				
			}elseif($type == 2){
				$inCondition = [2,3,8];
				array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
			}elseif($type == 3){
				$inCondition = [2,3,8];
				array_push($conditions, ['orders_headers.status_id' , '!=', 1]);
				array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '>', $today]);
			}else{
				$inCondition = [4,5,6,7];
			}
			
			
				//get allowed time to accept the order
			if($type == 1){
				$get_time_counter = DB::table("app_settings")->first();
				if($get_time_counter != NULL){
					$time_counter_in_hours = $get_time_counter->time_in_hours;
					$time_counter_in_min    = $get_time_counter->time_in_min;
					
 				}
			}else{
				$time_counter_in_hours = 0;
				$time_counter_in_min   = 0;
			}

			$today_date = date('Y-m-d');
			$now        = date('h:i:s')  ;  
			
			
			//get orders
 	$orders = \App\Order_header::where($conditions) 
 	                     
						->whereIn('orders_headers.status_id', $inCondition)
						->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
						->join('delivery_methods', 'orders_headers.delivery_method' ,'=', 'delivery_methods.method_id')
                        ->join('users', 'orders_headers.user_id', 'users.user_id')
						->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
						->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
						->select(
						            'orders_headers.order_id',
                                    'orders_headers.delivery_id',
                                    'providers.brand_name AS provider_name',
                                    'orders_headers.address',
                                    'users.full_name AS user_name',
                                    'orders_headers.total_value',
                                    $payment_col, $delivery_col,
                                    DB::raw("(SELECT count(order_details.id) FROM order_details WHERE order_details.order_id = orders_headers.order_id) AS meals_count"),
                                    $status_col,DB::raw('DATE(orders_headers.created_at) AS created_date'),
                                    DB::raw('TIME(orders_headers.created_at) AS created_time')
                        )
                        
                         
						->orderBy('orders_headers.order_id', 'DESC')
						->paginate(10);

		
		
	$get_time_counter = DB::table("app_settings")->first();
				if($get_time_counter != NULL){
					$time_counter_in_hours = $get_time_counter->time_in_hours;
					$time_counter_in_min    = $get_time_counter->time_in_min;
					
 				 
			}else{
				$time_counter_in_hours = 0;
				$time_counter_in_min   = 0;
			}
			
			return response()->json([
										'status' 			    => true,
										'errNum' 			    => 0,
										'msg' 				    => $msg[0],
										'orders' 			    => $orders,
										'time_counter_in_min'   => $time_counter_in_min,
										'time_counter_in_hours' => $time_counter_in_hours,
										'today_date' 			=> $today_date,
										'now' 					=> $now
									]);
		}
	}

	public function orderAcceptance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العمليه بنجاح',
				1 => 'رقم الطلب مطلوب',
				2 => 'رقم مقدم الخدمه مطلوب',
				3 => 'نوع العمليه مطلوب',
				4 => 'نوع العمليه يجب ان يكو (accept or reject)',
				5 => 'فشلت العمليه من فضلك حاول فى وقت لاحق',
				6 => 'ليس لديك صلاحية لتعديل هذا الطلب',
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'order_id is required',
				2 => 'provider_id is required',
				3 => 'type is required',
				4 => 'type must be (accept or reject)',
				5 => 'Process failed please try again later',
				6 => 'you can not access this order'
			);
		}

		$messages = array(
			'order_id.required'    => 1,
			'provider_id.required' => 2,
			'type.required'        => 3,
			'in' 				   => 4
		);

		$validator = Validator::make($request->all(), [
			'order_id' => 'required',
			'provider_id' => 'required',
			'type'        => 'required|in:accept,reject',
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$type = $request->input('type');
			if($type == "accept"){
				if($lang == 'ar'){
					$push_notif_title   ='قبول الطلب';
					$push_notif_message = 'قام مقدم الخدمة بقبول طلبك';
				}else{
					$push_notif_title   ='Order accepted';
					$push_notif_message = 'The provider accepted your order';
				}
				$status = 2;
			}else{
				if($lang == 'ar'){
					$push_notif_title   ='رفض الطلب';
					$push_notif_message = 'قام مقدم الخدمه برفض طلبك';
				}else{
					$push_notif_title   ='Order rejected';
					$push_notif_message = 'The provider rejected your order';
				}
				$status = 6;
			}
			try {
				$provider_id = $request->input('provider_id');
				$order_id    = $request->input('order_id');
				//get order
				$orderDetails = DB::table('orders_headers')->where('order_id', $order_id)->select(
				    'user_id', 'payment_type', 'total_value' , 'provider_id')->first();
                //return "provider_id : " . var_dump($provider_id) . " order_id " . var_dump($orderDetails->provider_id);
				if($provider_id != $orderDetails->provider_id){
                    return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
                }
				if($orderDetails != NULL){
					$payment_type = $orderDetails->payment_type;
					$total_value  = $orderDetails->total_value;
					$user_id      = $orderDetails->user_id;
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
				}
				DB::transaction(function() use ($status, $order_id, $provider_id, $payment_type, $total_value, $user_id){
					DB::table("orders_headers")->where('order_id', $order_id)->update(['status_id' => $status]);
					date_default_timezone_set('Asia/Riyadh');
					DB::table("orders_headers")->where('order_id', $order_id)->update(['provider_accept_order_date' => date("Y/m/d H:i:s", time())]);
					DB::table("order_details")->where('order_id', $order_id)->update(['status' => $status]);
					
					if($status == 6 || $status == "6"){
						if($payment_type != 1 && $payment_type != "1"){
							User::where('user_id', $user_id)->update([
									'points' => DB::raw('points + '.$total_value)
							]);
						}
					}
				});
				$notif_data = array();
				$notif_data['title']      = $push_notif_title;
			    $notif_data['message']    = $push_notif_message;
			    $notif_data['order_id']   = $order_id;
			    $notif_data['notif_type'] = 'order_acceptance';
			    
			    //device register for  firebase
			    $user_token = User::where('orders_headers.order_id', $order_id)
			    				  ->join('orders_headers', 'users.user_id', '=', 'orders_headers.user_id')
			    				  ->select('users.device_reg_id')
			    				  ->first();
			    				  
			    if($user_token != NULL){
			    	$push_notif = $this->singleSend($user_token->device_reg_id,$notif_data,$this->user_key);
			    }
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}
		}
	}

	public function getComplains(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم م قدم الخدمه مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'provider_id is required'
			);
		}

		if(empty($request->input('provider_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$provider_id = $request->input('provider_id');
		}
		$data = array();
		//get provider complains
		$complains = DB::table('complains')->where('complains.provider_id', $provider_id)
					    ->join('users', 'complains.user_id', '=', 'users.user_id')
					    ->join('orders_headers', 'complains.order_id', '=', 'orders_headers.order_id')
					    ->select('complains.order_id', 'orders_headers.order_id', 'orders_headers.order_code', 'users.full_name AS user_name', 'complains.complain','complains.attach_no', 'complains.id')
					    ->get();

		if($complains->count()){
			foreach($complains AS $row){
				//get Attaches
				$attaches = array();
				if($row->attach_no != 0 && $row->attach_no != "0"){
					$getAttaches = DB::table('attachments')->where('attach_id', $row->attach_no)
									 ->select('id','attach_path', 'type')
									 ->get();
					$attaches = $getAttaches;
				}

				array_push($data, ['user_name' => $row->user_name, 'order_id' => $row->order_id, 'order_code' => $row->order_code, 'complain' => $row->complain, 'attaches' => $attaches]);
			}
		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $data]);
	}

	// public function chooseDeliveryForOrder(Request $requ){

	// }

	public function changeOrderStatus(Request $request){
		//Log::debug('data: ', $request->all());
        /////////// define variables////////////
        $payment = "";
        $net = "";
        $app_value = "";
        $delivery_app_value = "";
        $totalVal = "";
        $userId = "";

		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العمليه بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'فشلت العمليه من فضلك حاول لاحقا',
				3 => 'رقم الحالة يجب ان يكون 2 او 3 او 4 او 5',
				4 => 'رقم الموصل مطلوب إذا كان رقم الحاله = 3 و طريقة التوصيل = 1',
				5 => 'رقم الموصل خطأ'
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'All fields are required',
				2 => 'Process failed, please try again later',
				3 => 'status_id must be 2,3,4 or 5',
				4 => 'delivery id is required if status id = 3 AND delivery_method = 1',
				5 => 'Invalid delivery_id'
			);
		}

		$messages = array(
			'required'    => 1,
			'in'          => 3
		);

		$validator = Validator::make($request->all(), [
			'order_id'        => 'required',
			'provider_id'     => 'required',
			'status_id'       => 'required|in:2,3,4,5'
		], $messages);

		if($validator->fails()){


			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		$order_id        = $request->input('order_id');
		$status          = $request->input('status_id');
		$provider_id     = $request->input('provider_id');
		// $delivery_method = $request->input('delivery_method');
		$delivery_id     = $request->input('delivery_id');

		$get = DB::table('orders_headers')
            ->where('order_id', $order_id)
            ->select('delivery_method', 'marketer_percentage', 'delivery_price', 'delivery_app_value', 'marketer_value', 'marketer_delivery_value')
            ->first();
            
		$delivery_method = $get->delivery_method;
		$marketer_percentage = $get->marketer_percentage;
		$delivery_price = $get->delivery_price;
		$provider_marketer_value = $get->marketer_value;
		$marketer_delivery_value = $get->marketer_delivery_value;
		if(($status == 3 || $status == "3") && ($delivery_method == 1 || $delivery_method == "1")){
			if(empty($request->input('delivery_id')) || $request->input('delivery_id') == NULL){
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}

			//check if selected delivery subscribe with marketer
			$check = DB::table('deliveries')->where('delivery_id', $delivery_id)->first();
			if($check != NULL){
				$marketer_code = $check->marketer_code;
				$created       = date('Y-m-d', strtotime($check->created_at));
			}else{
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}


			if($marketer_code != NULL && !is_null($marketer_code) && $marketer_code != ""){
				//get marketer id
				$marketer = DB::table('marketers')->where('marketer_code', $marketer_code)
							  ->first();
				if($marketer != NULL){
					$marketerId = $marketer->marketer_id;
				}else{
					$marketerId = 0;
				}
				$now = time();
				$y1 = date('y', strtotime($created));
				$y2 = date('y', $now);

				$m1 = date('m', strtotime($created));
				$m2 = date('m', $now);

				$d1 = date('d', strtotime($created));
				$d2 = date('d', $now);
				$months = (($y2 - $y1) * 12) + ($m2 - $m1) + (($d2 - $d1) / 30);
				if($months <= 1){
					$delivery_marketer_code = $marketer_code;
					$delivery_marketer_value = ($delivery_price * $marketer_percentage) / 100;
				}else{
					$delivery_marketer_code = "";
					$delivery_marketer_value = 0;
				}
			}else{
				$delivery_marketer_code = "";
				$delivery_marketer_value = 0;
				$marketerId = 0;
			}


		}else{
			$delivery_marketer_code = "";
			$delivery_marketer_value= 0;
			$marketerId = 0;
		}



		//get order payment method
		if($status == 4){
			$orderCredits = DB::table('orders_headers')
									->where("order_id", $order_id)
									->select("payment_type",'net_value', 'app_percentage', 'app_value', 'delivery_app_value', 'user_id', 'total_value')
									->first();
			if($orderCredits != NULL){
				$payment   = $orderCredits->payment_type;
				$net       = $orderCredits->net_value;
				$app_value = $orderCredits->app_value;
				$delivery_app_value = $orderCredits->delivery_app_value;
				$totalVal  = $orderCredits->total_value;
				$userId    = $orderCredits->user_id;
			}else{
				$payment   = "";
				$net 	   = 0;
				$app_value = 0;
				$totalVal  = 0;
				$userId    = 0;
				$delivery_app_value = 0;
			}
		}else{
			$payment   = "";
			$net 	   = 0;
			$app_value = 0;
			$delivery_app_value = 0;
		}


		try {
			DB::transaction(function() use ($delivery_app_value, $marketerId, $marketer_delivery_value, $provider_marketer_value, $order_id, $status, $provider_id, $payment, $totalVal, $userId,$net, $app_value, $delivery_method, $delivery_id, $lang, $delivery_marketer_value, $delivery_marketer_code){
				$updates = array();
				$updates['status_id'] =  $status;
				if(!empty($delivery_id) && $status == 3 && $delivery_method == 1){
					$updates['delivery_id'] = $delivery_id;
                    /*
                       add the time now into database when the provider deliver order to delivery to calculate
                       max time for delivery to accept or reject the order
                     */
                    date_default_timezone_set('Asia/Riyadh');
                    $timestamp =  date("Y/m/d H:i:s", time());
                    $updates["transfer_to_delivery_at"] = $timestamp;

				}

				if($delivery_marketer_value != 0){
					$updates['delivery_marketer_value'] = $delivery_marketer_value;
					$new_delivery_price = $delivery_price - $delivery_marketer_value;
					$updates['delivery_price'] = $new_delivery_price;
				}


				if($delivery_marketer_code != ""){
					$updates['delivery_marketer_code'] = $delivery_marketer_code;
				}

				DB::table('orders_headers')->where('order_id', $order_id)
				  						   ->update($updates);
				  						   
				DB::table('order_details')->where('order_id', $order_id)
				  						  ->update(['status' => $status]);

				if($status == 5 || $status == "5"){
					if($payment != 1 && $payment != "1"){
						User::where('user_id', $userId)->update([
								'points' => DB::raw('points + '.$totalVal)
						]);
					}
				}

				if($status == 4){
					if($payment != 1){
						DB::table("balances")->where("actor_id", $provider_id)
											 ->where('type', 'provider')
											 ->update([ 'current_balance' => DB::raw('current_balance + '. $net) ]);
											 
						if($delivery_method == 1 || $delivery_method == "1"){
							DB::table('balances')->where('actor_id', $delivery_id)
												 ->where('type', 'delivery')
												 ->update(['current_balance' => DB::raw('current_balance + '. $new_delivery_price)]);
						}

						if($marketer_delivery_value != 0 && $marketer_delivery_value != "0"){
							DB::table('balances')->where('actor_id', $marketerId)
												 ->where('type', 'marketer')
												 ->update(['current_balance' => DB::raw('current_balance + '. $marketer_delivery_value)]);
						}
					}else{
					    
					    
					    date_default_timezone_set('Asia/Riyadh');
					    
					    
					    	DB::table('balances')->where('actor_id', $marketerId)
												 ->where('type', 'marketer')
												 ->update(['current_balance' => DB::raw('current_balance + '. $net) ,'updated_at' => date('Y-m-d h:i:s')]);
												 
												 
    												 
    						DB::table("balances")->where("actor_id", $provider_id)
    											 ->where('type', 'provider')
    											 ->update([ 'due_balance' => DB::raw('due_balance + '. $app_value)  ,'updated_at' => date('Y-m-d h:i:s')]);
    
    						if($delivery_method == 1 || $delivery_method == "1"){
    							DB::table('balances')->where('actor_id', $delivery_id)
    												 ->where('type', 'delivery')
    												 ->update(['current_balance' => DB::raw('due_balance + '. $marketer_delivery_value.' + '. $delivery_app_value),'updated_at' => date('Y-m-d h:i:s')]);
    						}
    
    						if($marketer_delivery_value != 0 && $marketer_delivery_value != "0"){
    							DB::table('balances')->where('actor_id', $marketerId)
    												 ->where('type', 'marketer')
    												 ->update(['current_balance' => DB::raw('current_balance + '. $marketer_delivery_value),'updated_at' => date('Y-m-d h:i:s')]);
    						}
    						
    						
    						
						
						
						 
						
						
					}
				}

				//get orderDetails and delivery, user reg id
				$order_data = DB::table('orders_headers')->where('orders_headers.order_id', $order_id)
										   				 ->join('users', 'orders_headers.user_id', '=', 'users.user_id')
										   				 ->select('orders_headers.order_id', 'orders_headers.order_code','orders_headers.address AS user_address','orders_headers.user_longitude',
										   	        			  'orders_headers.user_latitude', 'users.device_reg_id AS user_token')
										   				 ->first();
				if($lang == "ar"){
					$userTitle   = "تم تعديل حالة طلبك";
					$userMessage = "تم تعديل حالة طلبك";
				}else{
					$userTitle   = "Your order status has been updated";
					$userMessage = "Your order status has been changed";
				}
				//send to user
				$notif_data = array();
				$notif_data['title']      = $userTitle;
			    $notif_data['message']    = $userMessage;
			    $notif_data['order_id']   = $order_data->order_id;
			    $notif_data['notif_type'] = 'order';


				$push_notif = $this->singleSend($order_data->user_token, $notif_data, $this->user_key);
				if($status == 3 && $delivery_method == 1){
					if($lang == "ar"){
						$deliveryTitle   = "طلب جديد";
						$deliveryMessage = "تم إرسال طلب جديد إليك";
					}else{
						$deliveryTitle   = "New order";
						$deliveryMessage = "A new order has been send for you";
					}

					if(!empty($delivery_id)){
						$deliveryToken= DB::table('deliveries')->where('delivery_id', $delivery_id)
										   				   ->select('device_reg_id AS delivery_token')
										   				   ->first()->delivery_token;
					}else{
						$deliveryToken = "";
					}
					//send to delivery
					$notif_data = array();
					$notif_data['title']   = $deliveryTitle;
				    $notif_data['message'] = $deliveryMessage;
				    $notif_data['order_id'] 	  = $order_data->order_id;
				    $notif_data['user_longitude'] = $order_data->user_longitude;
				    $notif_data['user_latitude']  = $order_data->user_latitude;
				    $notif_data['user_address']	  = $order_data->user_address;
				    $notif_data['notif_type']	  = 'order';
					$push_notif = $this->singleSend($deliveryToken, $notif_data, $this->delivery_key);
				}
			});
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}
	}

	public function getProviderBalance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'provider_id is required'
			);
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'provider_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{

            // check if the user has bank data
              $get_provider_bank = DB::table("withdraw_balance")
                ->select("*")
                ->where("actor_id" , $request->input("provider_id"))
                ->where("type" , "provider")
                ->get();
                
                
                  

            //get balaces
            $balance = DB::table('balances')
                ->where('actor_id', $request->input('provider_id'))
                ->where('type', 'provider')
                ->select('current_balance', 'due_balance', 'updated_at' , 'forbidden_balance')
                ->first();

            if($balance != null && count($balance) != 0){
                $current_balance = $balance->current_balance;
                $due_balance     = $balance->due_balance;
                $forbidden       = $balance->forbidden_balance;
                $updated         = $balance->updated_at;
            }else{
                $current_balance = "";
                $due_balance     = "";
                $forbidden       = "";
                $updated         = "";
            }
            
            if($get_provider_bank != null && count($get_provider_bank) != 0){
                //return empty($get_provider_bank);
                
                $last_entry = $get_provider_bank[count($get_provider_bank) -1 ];
                $bank_name = $last_entry->bank_name;
                $bank_account = $last_entry->account_num;
                $bank_username = $last_entry->name;
                $bank_phone = $last_entry->phone;
            }else{

                $bank_name = "";
                $bank_account = "";
                $bank_username = "";
                $bank_phone = "";
            }

            return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0],'balance' => ["current_balance"=>$current_balance , "due_balance" => $due_balance , "forbidden_balance" => $forbidden  , "updated_at" => $updated] , 'bank_name'=>$bank_name , 'bank_username'=>$bank_username , 'bank_phone'=>$bank_phone , 'account_num' =>$bank_account]);

            //---------------------------------



			//get current balance
//            $current = DB::table('orders_headers')
//                ->where('payment_type', 2)
//                ->where('status_id', 4)
//                ->where('provider_id', $request->input('provider_id'))
//                ->where('balance_status', 1)
//                ->where('provider_complain_flag', 0)
//                ->sum('net_value');
//
//            //get due balance
//            $due = DB::table('orders_headers')
//                ->where('payment_type', 1)
//                ->where('status_id', 4)
//                ->where('provider_id', $request->input('provider_id'))
//                ->where('balance_status', 1)
//                ->sum('app_value');
//
//            //forbidden balance
//            $forbidden = DB::table('orders_headers')
//                ->where('payment_type', 2)
//                ->where('status_id', 4)
//                ->where('provider_id', $request->input('provider_id'))
//                ->where('provider_complain_flag', 1)
//                ->where('balance_status', 1)
//                ->sum('net_value');
//
//            $balance = array('current_balance' => $current, 'due_balance' => $due, 'forbidden_balance' => $forbidden);

		}
	}

	public function withdraw(Request$request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'رقم صاحب الطلب مطلوب',
				2 => 'الرصيد الحالى مطلوب',
				3 => 'الرصيد المستحق مطلوب',
				4 => 'فشلت العملية من فضلك حاول لاحقا',
				5 => 'لديك طلبات لم يتم الرد عليها بعد',
				6 => 'ادخل رقم الرصيد المستحق المراد سحبة',
				7 => 'current_balance يجب ان يكون رقم',
				8 => 'رقم الرصيد الحالى مطلوب',
				9 => 'الاسم مطلوب',
				10 => 'رقم الحساب مطلوب',
				11 => 'رقم الهاتف مطلوب',
				12 => 'ليس لديك رصيد كافى لاتمام هذة العملية',
				13 => 'رصيدك الحالى اقل من الحد الادنى لسحب الرصيد',
                14 => "النوع يجب ان يكون اما مقدم او مسوق",
                15 => "النوع مطلوب",

			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'actor_id is required',
				2 => 'current_balance is required',
				3 => 'due_balance is required',
				4 => 'Process failed, please try again later',
				5 => 'You already have pending requests',
				6 => 'Enter a valid current_balance number',
				7 => 'current_balance must be a number',
				8 => 'bank_name is required',
				9 => 'name is required',
				10 => 'account_num is required',
				11 => 'phone is required',
				12 => "You Don't have enough balance",
				13 => "Your balance is less than minimum balance to withdraw",
				14 => "type must be either provider or marketer",
				15 => "type is required",
				16 => "forbidden_balance is required",
				17 => 'forbidden_balance must be a number'

			);
		}

		$messages = array(
			'actor_id.required'        => 1,
			'current_balance.required' => 2,
			'type.required'            => 15,
			'current_balance.numeric'  => 7,
			'due_balance.required'     => 3,
			'bank_name.required'       => 8,
			'name.required'            => 9,
			'account_num.required'     => 10,
			'phone.required'           => 11,
			'forbidden_balance.required'       => 16,
			'forbidden_balance.numeric'        => 17

		);

		$validator = Validator::make($request->all(), [
			'actor_id'        => 'required',
			'type'            => 'required',
			'current_balance' => 'required|numeric',
			'due_balance'     => 'required',
            'bank_name'       => 'required',
            'name'            => 'required',
            'account_num'     => 'required',
            'phone' 	      => 'required',
            'forbidden_balance'       => 'required|numeric'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{

		    $type = "";
		    if($request->input("type") == "provider"){
		        $type = "provider";
            }elseif($request->input("type") == "marketer"){
		        $type = "marketer";
            }else{
                return response()->json(['status' => false  , 'msg' => $msg[14]]);
            }

            // insert bank account data into database
            $actor_bank_data = DB::table("withdraw_balance")
                ->where("actor_id" , $request->input("actor_id"))
                ->where("type" , $type)
                ->first();
		  //  if($actor_bank_data !== null){
		  //      // update bank data
    //             DB::table("withdraw_balance")
    //                 ->where("actor_id" , $request->input("actor_id"))
    //                 ->where("type" , $type)
    //                 ->update([
    //                     "name" => $request->input("name"),
    //                     "phone" => $request->input("phone"),
    //                     "bank_name" => $request->input("bank_name"),
    //                     "account_num" => $request->input("account_num"),
    //                     "updated_at" =>date('Y-m-d h:i:s')
    //                 ]);

    //         }else{
		  //      // insert bank data
    //             DB::table("withdraw_balance")
    //                 ->insert([
    //                     "actor_id" => $request->input("actor_id"),
    //                     "type" => $type,
    //                     "name" => $request->input("name"),
    //                     "phone" => $request->input("phone"),
    //                     "bank_name" => $request->input("bank_name"),
    //                     "account_num" => $request->input("account_num"),
    //                     "created_at" =>date('Y-m-d h:i:s')
    //                 ]);
    //         }
            
			//check if there is pending requests
			$check  = DB::table('withdraw_balance')->where('actor_id', $request->input('actor_id'))
												   ->where('type', $type)
												   ->where('status', 1)
												   ->first();
			if($check != NULL){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}

            // check if the user requested blance is avaliable
            $provider_balace = DB::table("balances")
                                ->select("current_balance")
                                ->where("actor_id" , $request->input("actor_id"))
                                ->where("type" , $type)
                                ->first();
            $provider_current_balance = $provider_balace->current_balance;

            if($request->input("current_balance") > $provider_current_balance){
                return response()->json(['status' => false, 'errNum' => 12, 'msg' => $msg[12]]);
            }


            //check if the current balance is greater than min limit of withdrawing
            $min_balance = DB::table("app_settings")
                            ->select("min_balace_to_withdraw")
                            ->first();
            if($request->input("current_balance") < $min_balance->min_balace_to_withdraw){
                return response()->json(['status' => false, 'errNum' => 13, 'msg' => $msg[13]]);
            }


			$insert = DB::table("withdraw_balance")->insert([
						 'actor_id'        => $request->input('actor_id'),
						 'current_balance' => $request->input('current_balance'),
						 'due_balance'     => $request->input('due_balance'),
                         'forbidden'       => $request->input('forbidden_balance'),
						 'type' 		   => $type,
                         'status'          =>  1,
						 'bank_name' 	   => $request->input('bank_name'),
						 'name' 		   => $request->input('name'),
						 'account_num' 	   => $request->input('account_num'),
						 'phone' 		   => $request->input('phone')

					  ]);
			if($insert){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}

	public function getProviderOrderProperties(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب'
			);
			$delivery_col = "method_ar_name AS delivery_name";
		}else{
			$msg = array(
				0 => '',
				1 => 'provider_id is required'
			);
			$delivery_col = "method_en_name AS delivery_name";
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'provider_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$getProviderData = Providers::where('provider_id', $request->input('provider_id'))
										->select(DB::raw('IFNULL(allowed_from_time, "") AS allowed_from_time'), DB::raw('IFNULL(allowed_to_time, "") AS allowed_to_time'), 'delivery_price','receive_orders', 'current_orders', 'future_orders', DB::raw('IFNULL(DATE(avail_date), "") AS avail_date'), DB::raw('DATE(updated_at) AS last_updated'))
										->first();
			$avail_date = $getProviderData->avail_date;
			$updated_at = $getProviderData->last_updated;
			$today      = date('Y-m-d');
			if(strtotime($avail_date) <= strtotime($today)){
				$editFlag = 1;
			}else{
				$editFlag = 0;
			}
			if($editFlag == 1){
				$max_edit_date = date('Y-m-d', strtotime("+30 days"));
			}else{
				$max_edit_date = NULL;
			}

			//get providers orders time
			$getTimes = DB::table("providers_order_timelines")->where('provider_id', $request->input('provider_id'))
															  ->select(DB::raw('allowed_from_time AS from_time'), DB::raw('allowed_to_time AS to_time'))->get();

			//get deliveries
			$deliveries = DB::table("delivery_methods")->select('method_id AS delivery_id',$delivery_col,
																DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$request->input('provider_id').') > 0, 1, 0) AS choosen'))
													   ->get();

			return response()->json([
										'status' 			  => true,
										'errNum' 			  => 0,
										'msg' 				  => $msg[0],
										'provider_properties' => $getProviderData,
										'order_times'   => $getTimes,
										'deliveries'    => $deliveries,
										'editFlag'      => $editFlag,
										'max_edit_date' => $max_edit_date,
										'today_date'    => $today
									]);
		}
	}

	public function saveOrderProperties(Request $request){
		Log::debug("data; ", $request->all());
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0  => 'تم تعيين الإعدادات الجديده بنجاح',
				1  => 'مقدم الخدمه مطلوب',
				2  => 'وقت تلقى الطلبات مطلوب',
				3  => 'وقت تلقى الطلبات مطلوب',
				4  => 'سعر التوصيل مطلوب',
				5  => 'إستقبال الطلبات الفوريه مطلوب',
				6  => 'الحجوزات المستقبليه مطلوب',
				7  => 'حقل إلى فى الحجوزات المستقبلية مطلوب',
				8  => 'إستلام الطلبات مطلوب',
				9  => 'إستلام الطبات الفوريه و الحجوزات المستقبليه و إستلام الطلبات يجب ان يكون 0 او 1 فقط',
				10 => 'حقل إلى يجب ان يكون فى تنسيق yyyy-mm-dd',
				11 => 'لا يمكنك تعديل حقل إلى قبل 30 يوم من اخر تعديل له',
				12 => 'فشل تعيين الإعدادات الجديده من فضلك حاول لاحقا',
				13 => 'من و إلى فى اوقات خروج الطلبات مطلوبة',
				14 => 'طرق التوصيل مطلوبه',
				15 => 'من و إلى فى اوقات خروج الطلبات لا يمكن ان تكون متساوية',
				16 => 'حقل إلى لا يجب ان يكون بعد 30 يوم من اليوم',
				17 => 'وقت خروج الطلبات لا يمكن ان يحتوى على قيمه فارغه',
				18 => 'طرق التوصيل لا يمكن ان تتكرر'
			);
			$delivery_col = "method_ar_name AS delivery_name";
		}else{
			$msg = array(
				0  => 'New settings has been set successfully',
				1  => 'provider is required',
				2  => 'receive order time is required',
				3  => 'receive order time is required',
				4  => 'delivery price is required',
				5  => 'receive current orders is required',
				6  => 'receive future orders is required',
				7  => 'to field at future orders is required',
				8  => 'receive orders is required',
				9  => 'receive current orders, receive future orders and receive orders can be only 0 or 1',
				10 => 'to field must be in format (yyyy-mm-dd)',
				11 => 'You can not update your to date before 30 days from last update',
				12 => 'Failed to set the new settings, please try again later',
				13 => 'From and to at time to issue orders is required',
				14 => 'Delivery methods is required',
				15 => 'From and to at time to issue orders can not be the same',
				16 => 'to field must not be after 30 days from today',
				17 => 'Time to issue can not have a null value',
				18 => 'Delivery methods can not be repeated'
			);
			$delivery_col = "method_en_name AS delivery_name";
		}

		$messages = array(
			'provider_id.required'       => 1,
			'allowed_from_time.required' => 2,
			'allowed_to_time.required'   => 3,
			'delivery_price.required'    => 4,
			'current_orders.required'    => 5,
			'future_orders.required'     => 6,
			'avail_date.required'        => 7,
			'receive_orders.required'    => 8,
			'in' 						 => 9,
			'date_format' 				 => 10
		);

		$validator = Validator::make($request->all(), [
			'provider_id' 		=> 'required',
			'allowed_from_time' => 'required',
			'allowed_to_time'   => 'required',
			'delivery_price'    => 'required',
			'current_orders'    => 'required|in:0,1',
			'future_orders'     => 'required|in:0,1',
			'avail_date'        => 'required|date_format:Y-m-d',
			'receive_orders'    => 'required|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$deliveries 	  = $request->input('delivery_methods');
			$exit_order_times = $request->input('exit_order_times');
			$uniqueDeliveries = array_values(array_unique($deliveries));
			if(empty($deliveries)){
				return response()->json(['status' => false, 'errNum' => 14, 'msg' => $msg[14]]);
			}

			if(count($deliveries) != count($uniqueDeliveries)){
				return response()->json(['status' => false, 'errNum' => 18, 'msg' => $msg[18]]);
			}

			if(in_array("", $deliveries) || in_array(0, $deliveries) || in_array("0", $deliveries)){
				return response()->json(['status' => false, 'errNum' => 14, 'msg' => $msg[14]]);
			}

			if(empty($exit_order_times)){
				return response()->json(['status' => false, 'errNum' => 13, 'msg' => $msg[13]]);
			}

			if(in_array("", $exit_order_times)){
				return response()->json(['status' => false, 'errNum' => 17, 'msg' => $msg[13]]);
			}

			for($i = 0; $i < count($exit_order_times); $i++){
				if(empty($exit_order_times[$i][0]) || $exit_order_times[$i][0] == NULL || $exit_order_times[$i][0] == ""){
					return response()->json(['status' => false, 'errNum' => 17, 'msg' => $msg[13]]);
				}

				if(empty($exit_order_times[$i][1]) || $exit_order_times[$i][1] == NULL || $exit_order_times[$i][1] == ""){
					return response()->json(['status' => false, 'errNum' => 17, 'msg' => $msg[13]]);
				}
			}

			for($i = 0; $i < count($exit_order_times); $i++){
				$unique_exits = array_values(array_unique($exit_order_times[$i]));
				if(count($unique_exits) != count($exit_order_times[$i])){
					return response()->json(['status' => false, 'errNum' => 15, 'msg' => $msg[15]]);
				}
			}
			//get last updated date
			$datesData  = Providers::where('provider_id', $request->input('provider_id'))->select(DB::raw('DATE(updated_at) AS updated_at'), DB::raw('DATE(created_at) AS created_at'), DB::raw('DATE(avail_date) AS avail_date'))->first();
			$created_at = $datesData->created_at;
			$updated_at = $datesData->updated_at;
			$old_avail_date = $datesData->avail_date;
			if($old_avail_date != $request->input('avail_date')){
				if($old_avail_date != NULL && $old_avail_date != "" && $old_avail_date != '0000-00-00 00:00:00'){
					if($updated_at != "0000-00-00 00:00:00" && $updated_at != NULL && $updated_at != "" && $updated_at != $created_at){
						$today = date('Y-m-d');
						$datediff = strtotime($today) - strtotime($updated_at);
						$days = floor($datediff / (60 * 60 * 24));
					}elseif($updated_at == $created_at){
						$days = 31;
					}
				}else{
					$days = 31;
				}

				if($days <= 30){
					return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);
				}else{
					$avail_date = $request->input('avail_date');
					$today = date('Y-m-d');
					$newDate = strtotime('+30 days',strtotime($today));
					if(strtotime($avail_date) > $newDate){
						return response()->json(['status' => false, 'errNum' => 16, 'msg' => $msg[16]]);
					}
				}
			}else{
				$avail_date = "";
			}

			$provider_id        	   = $request->input('provider_id');
			$data['allowed_from_time'] = $request->input('allowed_from_time');
			$data['allowed_to_time']   = $request->input('allowed_to_time');
			$data['delivery_price']    = $request->input('delivery_price');
			$data['current_orders']    = $request->input('current_orders');
			$data['future_orders']     = $request->input('future_orders');
			$data['receive_orders']    = $request->input('receive_orders');

			if(!empty($avail_date) && $avail_date != ""){
				$data['avail_date'] = $avail_date;
				$data['updated_at'] = date('Y-m-d h:s:i');
			}
			try {
				DB::transaction(function() use ($data, $provider_id, $deliveries, $exit_order_times){
					Providers::where('provider_id', $provider_id)
							 ->update($data);

					DB::table('providers_delivery_methods')->where('provider_id', $provider_id)->delete();
					DB::table('providers_order_timelines')->where('provider_id', $provider_id)->delete();
					if(!empty($deliveries)){
						$inserts = array();
						for($i = 0; $i < count($deliveries); $i++){
							$inserts[$i]['provider_id'] 	= $provider_id;
							$inserts[$i]['delivery_method'] = $deliveries[$i];
						}
						DB::table('providers_delivery_methods')->insert($inserts);
					}

					if(!empty($exit_order_times)){
						$inserts = array();
						for($i = 0; $i < count($exit_order_times); $i++){
							$inserts[$i]['provider_id'] 	= $provider_id;
							$inserts[$i]['allowed_from_time']    = $exit_order_times[$i][0];
							$inserts[$i]['allowed_to_time']    = $exit_order_times[$i][1];
						}
						DB::table('providers_order_timelines')->insert($inserts);
					}
				});
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 12, 'msg' => $msg[12]]);
			}
		}
	}

	public function receiveOrderSwitch(Request $request){
		$lang = $request->input('lang');
		$switch = $request->input('switch');
		if($lang == "ar"){
			if($switch == 0){
				$m = "تم إيقاف إستلام الطلبات";
			}elseif($switch == 1){
				$m = "تم تفعيل إستلام الطلبات";
			}else{
				$m = "تمت العمليه بنجاح";
			}
			$msg = array(
				0  => $m,
				1  => 'كل الحقول مطلوبه',
				2  => 'قيمة السويتش يجب ان تنحصر بين 0 و 1',
				3  => 'فشلت العمليه'
			);
		}else{
			if($switch == 0){
				$m = "Receiving orders deactivated";
			}elseif($switch == 1){
				$m = "Receiving orders activated";
			}else{
				$m = "Process done successfully";
			}
			$msg = array(
				0  => $m,
				1  => 'All fields are required',
				2  => 'Switch value must be between 0 and 1',
				3  => 'Process failed',
			);
			$delivery_col = "method_en_name AS delivery_name";
		}

		$messages = array(
			'required' => 1,
			'in'	   => 2
		);

		$validator = Validator::make($request->all(), [
			'provider_id' 		=> 'required',
			'switch'			=> 'required|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$check = Providers::where('provider_id', $request->input('provider_id'))
							  ->update(['receive_orders' => $switch]);
			if($check){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		}
	}

	public function accept_video_file(Request $request){
		$file_name = 'mealsVideos/video-'.str_random(4).'.'.pathinfo($_FILES['video']['name'] ,PATHINFO_EXTENSION);
		$file_path = base_path().'/public/'.$file_name;
		if(move_uploaded_file($_FILES['video']['tmp_name'], $file_path)) {
		    return response()->json(['status' => true, 'errNum' => 0, 'msg' => 'uploaded']);
		} else{
		    return response()->json(['status' => failed, 'errNum' => 1, 'msg' => 'failed']);
		}
	}

	public function saveVideoAsFile($file, $path){
		$file_name = $path.'video-'.str_random(4).'.'.pathinfo($file['name'] ,PATHINFO_EXTENSION);
		$file_path = base_path().'/public/'.$file_name;
		if(move_uploaded_file($file['tmp_name'], $file_path)) {
		    return $file_name;
		} else{
		    return "";
		}
	}

	protected function saveVideo($data, $path){
		header('Content-Type: video/mp4');
		$data = base64_decode($data);
		$name = $path.'video-'.str_random(4).'.mp4';
		$target_file = base_path()."/public/".$name;
		file_put_contents($target_file,$data);
		return $name;
	}

 

	public function getProviderFollowers(Request $request){
		$lang = $request->input('lang');
		if($lang == 'ar'){
			$msg = array(
				0 => '',
				1 => 'يجب إرسال رقم مقدم الخدمه',
				2 => 'رقم مقدم الخدمه غير صحيح',
				3 => 'paginate_flag يجب ان يكون إما 1 او 2'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'provider id is required',
				2 => 'Invalid provider id',
				3 => 'paginate_flag must be 0 or 1'
			);
		}

		$messages = array(
			'required' => 1,
			'exists'   => 2,
			'in'       => 3,
		);

		$validator = Validator::make($request->all(), [
			'provider_id' => 'required|exists:providers',
			'paginate_flag' => 'nullable|sometimes|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		if($request->input('paginate_flag') == 1){
			$followers = User::where('providers_followers.provider_id', $request->provider_id)
						 ->join('providers_followers', 'users.user_id', '=', 'providers_followers.user_id')
						 ->select('users.profile_pic', 'users.full_name', 'users.user_id')
						 ->orderBy('providers_followers.id', 'DESC')
						 ->paginate(10);
		}else{
			$followers = User::where('providers_followers.provider_id', $request->provider_id)
						 ->join('providers_followers', 'users.user_id', '=', 'providers_followers.user_id')
						 ->select('users.profile_pic', 'users.full_name', 'users.user_id')
						 ->orderBy('providers_followers.id', 'DESC')
						 ->get();
		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'followers' => $followers]);
	}

	public function getDeliveries(Request $request){
		$city = $request->input('city_id');
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = "يجب إرسال مدينة مقدم الخدمه";
		}else{
			$msg = "Provider city is required";
		}
		if(empty($city) || $city == 0){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg]);
		}
		$deliveries = DB::table('deliveries')->where('publish', 1)
											 ->where('status', 1)
											 ->where('city_id', $city)
											 ->select('delivery_id', 'full_name')
											 ->get();

		return response()->json(['status' => true, 'deliveries' => $deliveries,'errNum' => 0, 'msg' => '']);
	}

	public function marketerSignUp(Request $request){
		$lang   = $request->input('lang');
		$status = 0;
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'البريد الإلكترونى غير صحيح',
				3 => 'الرقم السرى يجب الا يقل عن 8 حروف',
				4 => 'رقم الجوال مستخدم من قبل',
				5 => 'البريد الإلكترونى مستخدم من قبل',
				6 => 'فشل التسجيل من فضلك حاول لاحقا',
				7 => 'فشل فى رفع الصورة حاول فى وقت لاقح'
			);
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'Invalid e-mail address',
				3 => 'Password must not be less than 8 characters',
				4 => 'Phone is already used',
				5 => 'E-mail is already used',
				6 => 'Failed to register, please try again later',
				7 => 'Failed to upload image, please try again later'
			);
		}
		$messages = array(
			'required'     => 1,
			'email'        => 2,
			'min'          => 3,
			'phone.unique' => 4,
			'email.unique' => 5,
		);

		$validator = Validator::make($request->all(), [
			'first_name'     => 'required',
			'second_name'    => 'required',
			'third_name'     => 'required',
			'last_name'      => 'required',
			'email'          => 'required|email|unique:marketers',
			'phone'          => 'required|unique:marketers',
			'country_code'   => 'required',
			'password'       => 'required|min:8',
			'country_id'     => 'required',
			'city_id'        => 'required',
			'address'        => 'required',
			'profile_pic'    => 'sometimes|nullable',
			'marketer_token' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$profile_pic = $request->input('profile_pic');
			//uploading image if exist
			if(!empty($profile_pic)){
				$image = $this->saveImage($profile_pic, 'jpg', 'marketerProfileImages/');
				if($image == ""){
					return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
				}else{
					$image = url($image);
				}
			}else{
				$image = url('avatar_ic.png');
			}

			$full_name = $request->input('first_name')." ".$request->input('second_name')." ".$request->input('third_name')." ".$request->input('last_name');
			$data['full_name']      = $full_name;
			$data['first_name']     = $request->input('first_name');
			$data['second_name']    = $request->input('second_name');
			$data['third_name']     = $request->input('third_name');
			$data['last_name']      = $request->input('last_name');
			$data['email']          = $request->input('email');
			$data['phone']          = $request->input('phone');
			$data['country_code']   = '+'.$request->input('country_code');
			$data['password']       = $request->input('password');
			$data['country_id']     = $request->input('country_id');
			$data['city_id']        = $request->input('city_id');
			$data['status']         = $status;
			$data['marketer_code']  = str_random(7);
			$data['profile_pic']    = $image;
			$data['address']        = $request->input('address');
			$data['marketer_token'] = $request->input('marketer_token');
			$id = "";
			try {
				DB::transaction(function () use ($data, &$id) {
					$id = Marketers::insertGetId([
						'full_name'    => $data['full_name'],
						'first_name'   => $data['first_name'],
						'second_name'  => $data['second_name'],
						'third_name'   => $data['third_name'],
						'last_name'    => $data['last_name'],
						'email'        => $data['email'],
						'phone'        => $data['phone'],
						'country_code' => $data['country_code'],
						'password'     => md5($data['password']),
						'country_id'   => $data['country_id'],
						'city_id'      => $data['city_id'],
						'status'       => $data['status'],
						'marketer_code'=> $data['marketer_code'],
						'profile_pic'  => $data['profile_pic'],
						'address'      => $data['address'],
						'device_reg_id'=> $data['marketer_token']
					]);

					if($id){
						DB::table('balances')->insert(['actor_id' => $id, 'type' => 'marketer','current_balance' => 0, 'due_balance' => 0]);
					}else{
						return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
					}
				});
				$marketerData = $this->marketerData($id);
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $marketerData]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
			}
		}
	}

	// function to return data of marketer
	public function prepareEditMarketerProfile(Request $request){

	    $marketer_id = $request->input("marketer_id");
	    $lang        = $request->input("lang");

	    if($lang == "ar"){
            $messages = array(
                1 => "رقم المسوق مطلوب",
                2 => "لا يوجد بيانات لهذا المسوق",
                3 => "تم استرجاع البيانات بنجاح",
            );
            $city_col = "city.city_ar_name AS city_name";
            $country_col = "country.country_ar_name AS country_name";
        }else{
            $messages = array(
                1 => "marketer_id is required",
                2 => "no data for this marketer id",
                3 => "Retrieved successfully "
            );
            $city_col = "city.city_en_name AS city_name";
            $country_col = "country.country_en_name AS country_name";

        }
        $mess = array(
                'required'     => 1,
        );
        $validator = Validator::make($request->all(), [
            'marketer_id'     => 'required',
        ], $mess);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => $error, 'msg' => $messages[$error]]);
        }else{

            $marketerData  = $this->marketerData($marketer_id , $lang);


            $countries       = DB::table('country')->where('publish', 1)->select('country_id', $country_col, DB::raw('IF(country_id = '.$marketerData->country_id.', true, false) AS chosen'), 'country_code')->get();
            $cities          = DB::table('city')->select('city_id', $city_col, DB::raw('IF(city_id = '.$marketerData->city_id.', 1, 0) AS chosen'))->get();

            if($marketerData == null){
                return response()->json(['status' => false, 'errNum' => 2 ,'msg' => $messages[2]]);
            }

            return response()->json(['status' => true, 'errNum' => 0,'msg' => $messages[3] , 'data' => $marketerData,'cities' => $cities , "countries" => $countries]);
        }
    }

	public function marketerData($id, $lang = "en" , $type="register", $pass=NULL, $phone=NULL){
		if($lang == "en"){
		    $city = "city_en_name AS city";
		    $country = "country_en_name AS country";
        }else{
		    $city = "city_ar_name AS city";
		    $country = "country_ar_name AS country";
        }
	    if($type == "register"){
			return Marketers::where('marketer_id', $id)
                         ->join("city" , "city.city_id" , "marketers.city_id")
                         ->join("country" , "country.country_id" , "marketers.country_id")
						 ->select('marketer_id AS id', 'full_name AS marketer_name', 'first_name', 'second_name', 'third_name', 'last_name',
						 	      'email', 'phone', 'marketers.country_code', $city , $country ,'profile_pic','address','marketers.country_id', 'marketers.city_id', 'status', 'marketers.publish' ,'marketer_code', DB::raw('DATE(created_at) AS created') , DB::raw('DATE(updated_at) AS updated'))
						 ->first();
		}elseif($type == "login"){
			return Marketers::where('password', md5($pass))
							->where(function($q) use ($phone){
						        $q->where('phone', $phone)
						          ->orWhere(DB::raw('CONCAT(country_code,phone)'), $phone);
						    })
                            ->select('marketer_id AS id', 'full_name AS marketer_name', 'first_name', 'second_name', 'third_name', 'last_name',
  						 	      'email', 'phone', 'country_code', 'profile_pic','address','country_id', 'city_id', 'status', 'marketer_code', DB::raw('DATE(created_at) AS created'))
							->first();
		}

	}

	public function activate_marketer(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التفعيل',
				1 => 'رقم المسوق مطلوب',
				2 => 'فشل التفعيل من فضلك حاول لاقحا'
			);
		}else{
			$msg = array(
				0 => 'Activated successfully',
				1 => 'marketer_id is required',
				2 => 'Failed to activate, please try again later'
			);
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$activate = Marketers::where('marketer_id', $request->input('id'))->update(['status' => 1]);
			if($activate){
				$marketerData = $this->marketerData($request->input('id'));
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $marketerData]);
			}else{
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
			}
		}
	}

	public function marketerLogin(Request $request){
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
				2 => 'Wrong phone number',
				3 => 'Password is required',
				4 => 'Wrong data',
				5 => 'You need to activate your account',
				6 => 'marketer_token is required'
			);
			$city_col = "city.city_en_name AS city_name";
		}
		$messages = array(
				'phone.required'    => 1,
				'password.required' => 3,
				'marketer_token.required' => 6
			);
		$validator = Validator::make($request->all(), [
			'phone'    => 'required',
			'password' => 'required',
			'marketer_token' => 'required'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$getMarketer = $this->marketerData(NULL, $lang ,"login", $request->input('password'), $request->input('phone'));
			if($getMarketer != NULL && !empty($getMarketer) && $getMarketer->count()){
				if($getMarketer->status == 0 || $getMarketer->status == "0"){
					return response()->json(['status'=> false, 'errNum' => 5, 'data' => $getMarketer, 'msg' => $msg[5]]);
				}
				return response()->json(['status'=> true, 'errNum' => 0, 'data' => $getMarketer, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}

	public function marketerEditProfile(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم تعديل البيانات بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'يجب ان يكون البريد الإلكترونى صحيح',
				3 => 'الرقم السرى لا يجب ان يقل عن 8 حروف',
				4 => 'البريد الإلكترونى مستخدم من قبل',
				5 => 'فشلت العمليه من فضلاك حاول لاحقا',
				6 => 'فشل رفع الصوره'
			);
		}else{
			$msg = array(
				0 => 'Updated successfully',
				1 => 'All fields are required',
				2 => 'Invalid e-mail',
				3 => 'Password can not be less than 8 characters',
				4 => 'This e-mail is already used',
				5 => 'Failed to update, please try again later',
				6 => 'Failed to upload the image'
			);
		}

		$messages = array(
			'required'     => 1,
			'email'        => 2,
			'min'          => 3,
			'unique'       => 4
		);

		$validator = Validator::make($request->all(), [
			'id'  => 'required',
			'first_name'   => 'required',
			'second_name'  => 'required',
			'third_name'   => 'required',
			'last_name'    => 'required',
			'email'        => 'required|email|unique:marketers,email,'.$request->input('id').',marketer_id',
			'password'     => 'nullable|sometimes|min:8',
			'country_id'   => 'required',
			'city_id'      => 'required',
			'address'      => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$profile_pic = $request->input('profile_pic');
			if(!empty($profile_pic)){
				$image = $this->saveImage($profile_pic, 'jpg', 'marketerProfileImages/');
				if($image == ""){
					return response()->json(['status'=> false, 'errNum' => 6, 'msg' => $msg[6]]);
				}else{
					$image = url($image);
				}
			}else{
				$image = "";
			}

			$marketerId = $request->input('id');
			$full_name  = $request->input('first_name')." ".$request->input('second_name')." ".$request->input('third_name')." ".$request->input('last_name');
			$data['full_name']   = $full_name;
			$data['first_name']  = $request->input('first_name');
			$data['second_name'] = $request->input('second_name');
			$data['third_name']  = $request->input('third_name');
			$data['last_name']   = $request->input('last_name');
			$data['email']       = $request->input('email');
			// $data['country_code'] = $request->input('country_code');
			if(!empty($request->input('password')) && $request->input('password') != ""){
				$data['password'] = md5($request->input('password'));
			}
			$data['country_id'] = $request->input('country_id');
			$data['city_id'] 	= $request->input('city_id');
			$data['address'] 	= $request->input('address');
			if(!empty($image) && $image != ""){
				$data['profile_pic'] = $image;
			}

			try {
				Marketers::where('marketer_id', $marketerId)->update($data);
				$marketerData = $this->marketerData($marketerId);
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $marketerData]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}
		}
	}

	public function update_marketer_phone(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم تعديل رقم الجوال',
				1 => 'رقم المسوق مطلوب',
				2 => 'رقم الجوال مطلوب',
				3 => 'كود الدوله مطلوب',
				4 => 'فشلت العملية حاول فى وقت لاحق',
				5 => 'رقم الجوال مستخدم من قبل'
			);
		}else{
			$msg = array(
				0 => 'Updated successfully',
				1 => 'marketer_id is required',
				2 => 'Phone is required',
				3 => 'Country code is required',
				4 => 'Failed to update, please try again later',
				5 => 'Phone number is used before'
			);
		}

		$messages = array(
			'id.required'   => 1,
			'phone.required' 		 => 2,
			'phone.unique'			 => 5,
			'country_code.required'  => 3
		);

		$validator = Validator::make($request->all(),[
			'id'  => 'required',
			'phone'        => 'required|unique:marketers,phone,'.$request->input('id').',marketer_id',
			'country_code' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$update = Marketers::where('marketer_id', $request->input('id'))->update([
				'phone'        => $request->input('phone'),
				'country_code' => '+'.$request->input('country_code')
			]);

			if($update){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}

	public function getMarketerBalance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'marketer_id is required'
			);
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'marketer_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
		    // check if the user has bank data
            $get_marketer_bank = DB::table("withdraw_balance")
                                    ->select("*")
                                    ->where("actor_id" , $request->input("marketer_id"))
                                    ->where("type" , "marketer")
                                    ->get();
            //get balaces
            $balance = DB::table('balances')
                ->where('actor_id', $request->input('marketer_id'))
                ->where('type', 'marketer')
                ->select('current_balance', 'due_balance', 'updated_at' , 'forbidden_balance')
                ->first();

            if($balance !== null){
                $current_balance = $balance->current_balance;
                $due_balance     = $balance->due_balance;
                $forbidden       = $balance->forbidden_balance;
                $updated         = $balance->updated_at;


            }else{
                $current_balance = "";
                $due_balance     = "";
                $forbidden       = "";
                $updated         = "";
            }

            if($get_marketer_bank !== null && count($get_marketer_bank) != 0){
                $last_entry = $get_marketer_bank[count($get_marketer_bank) -1];
                $bank_name = $last_entry->bank_name;
                $bank_account = $last_entry->account_num;
                $bank_username = $last_entry->name;
                $bank_phone = $last_entry->phone;
            }else{

                $bank_name = "";
                $bank_account = "";
                $bank_username = "";
                $bank_phone = "";
            }

            return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0],'balance' => ["current_balance"=>$current_balance , "due_balance" => $due_balance , "forbidden_balance" => $forbidden  , "updated_at" => $updated] , 'bank_name'=>$bank_name , 'bank_username'=>$bank_username , 'bank_phone'=>$bank_phone , 'account_num' =>$bank_account]);
		}
	}

	public function withdrawReport(Request $request){
		$lang = $request->input('lang');
		if(empty($lang)){
			$lang = 'en';
		}

		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم المستخدم مطلوب',
				2 => 'النوع مطلوب',
				3 => 'النوع يجب ان يكون فى provider, delivery, user',
				4 => 'يجب ان يكون التاريخ بهذا التنسيق yyyy-mm-dd',
				5 => 'إذا كنت تبحث بالمده فيجب توافر تاريخ البداية والنهاية معا',
				6 => 'لا يوجد نتائج'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'actor_id is required',
				2 => 'type is required',
				3 => 'type must be in provider, delivery, user',
				4 => 'date format must be yyyy-mm-dd',
				5 => 'from date required with to date in search with period',
				6 => 'Empty result'
			);
		}

		$messages = array(
			'actor_id.required' => 1,
			'type.required' => 2,
			'in'       => 3,
			'date_format' => 4,
			'required_with' => 5
		);

		$validator = Validator::make($request->all(), [
			'actor_id' => 'required',
			'type'     => 'required|in:provider,delivery, user',
			'from_date' => 'nullable|date_format:Y-m-d|required_with:to_date',
			'to_date' => 'nullable|date_format:Y-m-d|required_with:from_date'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		$requests = $this->withdrawRequest($request->input('actor_id'), $request->input('type'), $lang, $request->input('from_date') , $request->input('to_date'));

		if($requests == false){
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}

		if($requests->count()){
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'requests' => $requests]);
		}

		return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
	}

	public function getIncome(Request $request){
		$lang = $request->input('lang');
		$type = $request->input('type');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم المستخدم مطلوب',
				2 => 'النوع مطلوب',
				3 => 'النوع يجب ان يكون فى provider, delivery',
				4 => 'يجب ان يكون التاريخ بهذا التنسيق yyyy-mm-dd',
				5 => 'إذا كنت تبحث بالمده فيجب توافر تاريخ البداية والنهاية معا',
				6 => 'لا يوجد نتائج'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'actor_id is required',
				2 => 'type is required',
				3 => 'type must be in provider, delivery',
				4 => 'date format must be yyyy-mm-dd',
				5 => 'from date required with to date in search with period',
				6 => 'Empty result'
			);
		}

		$messages = array(
			'actor_id.required' => 1,
			'type.required' => 2,
			'in'       => 3,
			'date_format' => 4,
			'required_with' => 5
		);

		$validator = Validator::make($request->all(), [
			'actor_id' => 'required',
			'type'     => 'required|in:provider,delivery',
			'from_date' => 'nullable|date_format:Y-m-d|required_with:to_date',
			'to_date' => 'nullable|date_format:Y-m-d|required_with:from_date'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		if($type == 'provider'){
            $table = 'providers';
            $col   = 'providers.provider_id';
            $cond  = 'orders_headers.provider_id';
            $money = 'orders_headers.net_value AS credit';
        }elseif($type == 'delivery'){
            $table = 'deliveries';
            $col   = 'deliveries.delivery_id';
            $cond  = 'orders_headers.delivery_id';
            $money = '(orders_headers.delivery_price - orders_headers.delivery_app_value) AS credit';
        }else{
            return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
        }


        $conditions[] = [$cond, '=', $request->input('actor_id')];
        $conditions[] = ['orders_headers.status_id' , '=', 4];
        if(!empty($request->input('from_date'))){
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '>=', $request->input('from_date')];
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '<=', $request->input('to_date')];
        }
	    $result = DB::table('orders_headers')->where($conditions)
	        						         ->join($table, $cond, '=', $col)
	        						         ->join('order_details', 'orders_headers.order_id', '=', 'order_details.order_id')
	        						         ->select(
			        						   		'orders_headers.order_code', 'orders_headers.total_qty', 'orders_headers.total_value', 'orders_headers.order_id AS invo_no',
			        						   		DB::raw($money), DB::raw('COUNT(order_details.meal_id) AS mealsCount'), 'orders_headers.order_id'
			        						  )
	        						         ->groupBy('orders_headers.order_id')
	        						         ->get();
		if($result->count()){
	    	return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'result' => $result]);
	    }

	    return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);

	}

	public function getTotalIncome(Request $request){
		$lang = $request->input('lang');
		$type = $request->input('type');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم المستخدم مطلوب',
				2 => 'النوع مطلوب',
				3 => 'النوع يجب ان يكون فى provider, delivery',
				4 => 'يجب ان يكون التاريخ بهذا التنسيق yyyy-mm-dd',
				5 => 'إذا كنت تبحث بالمده فيجب توافر تاريخ البداية والنهاية معا',
				6 => 'لا يوجد بيانات'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'actor_id is required',
				2 => 'type is required',
				3 => 'type must be in provider, delivery',
				4 => 'date format must be yyyy-mm-dd',
				5 => 'from date required with to date in search with period',
				6 => 'Empty result'
			);
		}

		$messages = array(
			'actor_id.required' => 1,
			'type.required' => 2,
			'in'       => 3,
			'date_format' => 4,
			'required_with' => 5
		);

		$validator = Validator::make($request->all(), [
			'actor_id' => 'required',
			'type'     => 'required|in:provider,delivery',
			'from_date' => 'nullable|date_format:Y-m-d|required_with:to_date',
			'to_date' => 'nullable|date_format:Y-m-d|required_with:from_date'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		if($type == 'provider'){
            $table = 'providers';
            $col   = 'providers.provider_id';
            $cond  = 'orders_headers.provider_id';
            $money = '(orders_headers.net_value + orders_headers.app_value) AS credit';
        }elseif($type == 'delivery'){
            $table = 'deliveries';
            $col   = 'deliveries.delivery_id';
            $cond  = 'orders_headers.delivery_id';
            $money = '(orders_headers.delivery_price) AS credit';
        }else{
            return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
        }


        $conditions[] = [$cond, '=', $request->input('actor_id')];
        $conditions[] = ['orders_headers.status_id' , '=', 4];
        if(!empty($request->input('from_date'))){
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '>=', $request->input('from_date')];
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '<=', $request->input('to_date')];
        }
	    $result = DB::table('orders_headers')->where($conditions)
	        						         ->join($table, $cond, '=', $col)
	        						         ->join('order_details', 'orders_headers.order_id', '=', 'order_details.order_id')
	        						         ->select(
			        						   		'orders_headers.order_code',
                                                    'orders_headers.order_id AS invo_no' ,
                                                    'orders_headers.total_qty',
                                                    'orders_headers.total_value',
			        						   		DB::raw($money),
                                                    DB::raw('COUNT(order_details.meal_id) AS mealsCount'),
                                                    'orders_headers.order_id'
			        						  )
	        						         ->groupBy('orders_headers.order_id')
	        						         ->get();
	    if($result->count()){
	    	return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'result' => $result]);
	    }

	    return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
	}

	public function getMarketerClients(Request $request)
	{
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'لا توجد بيانات للمسوق المرسل'
			);
			$provider = 'مقدم خدمة';
			$delivery = 'موصل';
		}else{
			$msg = array(
				0 => '',
				1 => 'There is no data for the sent marketer'
			);
			$provider = 'provider';
			$delivery = 'Delivery';
		}

		$marketer = Marketers::where('marketer_id', $request->input('marketer_id'))
                    ->select('marketer_code')
                    ->first();
		if(is_null($marketer)){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$code = $marketer->marketer_code;
		}

		$table = "(SELECT provider_id AS id, full_name, profile_pic, '".$provider."' AS type, created_at AS created FROM providers WHERE marketer_code = '".$code."'";
		$table .= " UNION ";
		$table .= "SELECT delivery_id AS id, full_name, profile_pic, '".$delivery."' AS type, created_at AS created FROM deliveries WHERE marketer_code = '".$code."') AS tble";

		$result = DB::table(DB::raw($table))->orderBy('created', 'DESC')->paginate(10);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'code' => $code, 'result' => $result]);
	}

	public function marketerBalanceDetails(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'لا توجد بيانات للمسوق المرسل'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'There is no data for the sent marketer'
			);
		}

		$setting = DB::table('app_settings')->select('marketer_percentage')->first();
		if(is_null($setting)){
			$current_percentage = 0;
		}else{
			$current_percentage = $setting->marketer_percentage;
		}

		$marketer = Marketers::where('marketer_id', $request->input('marketer_id'))->select('marketer_code')->first();
		if(is_null($marketer)){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$code = $marketer->marketer_code;
		}
		$conditions   = [];
		$conditions[] = ['orders_headers.provider_marketer_code', '=', $code];

		if(!empty($request->input('from')) && $request->input('from') != NULL){
			$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '>=', $request->input('from')];
		}

		if($request->input('to') && $request->input('to') != NULL){
			$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '<=', $request->input('to')];
		}
		$balance = DB::table('orders_headers')->where($conditions)
				   ->join('providers', 'orders_headers.provider_id', 'providers.provider_id')
				   ->select('providers.full_name', 'orders_headers.total_value', 'orders_headers.marketer_value', 'marketer_percentage')
				   ->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'percentage' => $current_percentage,'balance' => $balance]);
	}

	// function to chekcif the actor receive orders
    function isReceiveOrders(Request $request){
        $lang = $request->input('lang');
        if($lang == "ar"){
            $msg = array(
                0 => '',
                1 => 'رقم المستخدم مطلوب',
                2 => 'النوع مطلوب',
                3 => 'النوع يجب ان يكون فى provider, delivery',
                4 => 'لا يوجد بيانات',
                5 => 'رقم المستخدم مطلوب',
                6 => 'تم بنجاح'
            );
        }else{
            $msg = array(
                0 => '',
                1 => 'actor_id is required',
                2 => 'type is required',
                3 => 'type must be in provider, delivery',
                4 => 'Empty result',
                5 => 'actor_id is not valid',
                6 => "success"

            );
        }

        $messages = array(
            'actor_id.required' => 1,
            'type.required' => 2,
            'in'       => 3,

        );

        $validator = Validator::make($request->all(), [
            'actor_id' => 'required',
            'type'     => 'required|in:provider,delivery',
            'from_date' => 'nullable|date_format:Y-m-d|required_with:to_date',
            'to_date' => 'nullable|date_format:Y-m-d|required_with:from_date'
        ], $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
        }else{

            $type = $request->input("type");
            $actor_id = $request->input("actor_id");
            if($type == "provider"){

                $id = "provider_id";
                $select = "providers";
                $provider = DB::table("providers")
                        ->where("provider_id" , $actor_id)
                        ->first();
                if(!$provider){
                    return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[4]]);
                }
            }else{
                $id = "delivery_id";
                $select = "deliveries";
                $delivery = DB::table("deliveries")
                    ->where("delivery_id" , $actor_id)
                    ->first();
                if(!$delivery){
                    return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[4]]);
                }
            }

            $data = DB::table($select)
                    ->where($id , $actor_id)
                    ->select("receive_orders")
                    ->first();
            if($data){
                return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[6] , "receive_orders" => $data->receive_orders]);
            }else{
                return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);
            }
        }
    }
}