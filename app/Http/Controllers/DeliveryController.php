<?php

namespace App\Http\Controllers;

/**
 * Class DeliveryController.
 * it is a class to manage all delivery functionalities
 * Zad delivery sign up
 * Zad delivery log in
 * Zad delivery forget password
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
use DateTime;

class DeliveryController extends Controller
{
	protected $api_auth_email;
	protected $api_auth_password;
	public function __construct(Request $request){
		$this->api_auth_email    = "api_user@authentication.com";
		$this->api_auth_password = "M*y@H0tSu9_o";
		$method = $request->method();
		if($method != "GET"){
			$api_email    = $request->input('api_email');
			$api_password = $request->input('api_password');
			$get = DB::table('api_users')->where('api_email', $api_email)
					   					 ->where('api_pass', md5($api_password))
					   					 ->first();
			if(!$get || $get == NULL){
				$response = array('status' => false, 'errNum' => 500,'msg' => 'Authentication failed');
				echo json_encode($response);
				die();
			}
		}
	}

	//method to prevent visiting any api link
	public function echoEmpty(){

		echo "";
	}
 

   
	protected function getCountries($lang, $selected = NULL){
		if($lang == "ar"){
			$country_col = "country_ar_name AS country_name";
		}else{
			$country_col = "country_en_name AS country_name";
		}

		if($selected != NULL){
			return DB::table('country')->where('publish', 1)->select('country_id', $country_col, DB::raw('IF(country_id = '.$selected.', 1, 0) AS chosen'), 'country_code')->get();
		}

		return DB::table('country')->select('country_id', $country_col, 'country_code')->get();
	}

	protected function getCities($lang, $selected = NULL){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		if($selected != NULL){
			return DB::table('city')
					 ->join('country', 'city.country_id', '=', 'country.country_id')
					 ->select('city.city_id', $city_col, DB::raw('IF(city.city_id = '.$selected.', 1, 0) AS chosen'), 'country.country_code')->get();
		}

		return DB::table('city')
				  ->join('country', 'city.country_id', '=', 'country.country_id')
				  ->select('city.city_id', $city_col, 'country_code')->get();
	}

	protected function getDeliveryData($id, $lang, $action = "get", $password = NULL, $phone = NULL){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		if($action == "get"){

                return Deliveries::where('deliveries.delivery_id', $id)
                    ->join('city', 'deliveries.city_id', 'city.city_id')
                    ->select('deliveries.delivery_id', 'deliveries.full_name AS delivery_name','deliveries.firstname',
                        'deliveries.secondname','deliveries.receive_orders' , 'deliveries.thirdname', 'deliveries.lastname', 'deliveries.profile_pic', 'car_img1', 'car_img2', 'car_img3', 'id_img','id_date', 'insurance_img', 'authorization_img', 'license_img','license_date',
                        'deliveries.status', 'deliveries.phone', 'deliveries.country_code', 'deliveries.email','deliveries.device_reg_id',
                        'deliveries.longitude', 'deliveries.latitude', 'deliveries.attach_id','deliveries.car_type','deliveries.country_id', 'deliveries.delivery_rate','deliveries.city_id',$city_col, DB::raw('DATE(deliveries.created_at) AS created'),DB::raw('DATE(deliveries.admin_activation_time) AS admin_activation_date') , DB::raw('TIME(deliveries.admin_activation_time) AS admin_activation_time') , 'admin_activation_time AS Test_Full_Time')
                    ->first();

		}elseif($action == "login"){
			return Deliveries::where('deliveries.password', md5($password))
					         ->where(function($q) use ($phone){
						         $q->where('deliveries.phone', $phone)
						           ->orWhere(DB::raw('CONCAT(deliveries.country_code,deliveries.phone)'), $phone);
						     })
					         ->join('city', 'deliveries.city_id', 'city.city_id')
					         ->select('deliveries.delivery_id', 'deliveries.full_name AS delivery_name', 'deliveries.firstname',
					         	      'deliveries.secondname', 'deliveries.receive_orders' , 'deliveries.thirdname', 'deliveries.lastname', 'deliveries.profile_pic', 'car_img1', 'car_img2', 'car_img3', 'id_img','id_date', 'insurance_img', 'authorization_img', 'license_img','license_date',
					       			  'deliveries.status', 'deliveries.phone', 'deliveries.country_code', 'deliveries.email', 
					       			  'deliveries.longitude', 'deliveries.latitude', 'deliveries.attach_id','deliveries.car_type','deliveries.country_id', 'deliveries.delivery_rate', 'deliveries.city_id',$city_col, DB::raw('DATE(created_at) AS created'))
					         ->first();
		}else{
			return NULL;
		}
		
	}

	public function getCountryCities($lang, $country){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		return DB::table('city')->where('city.country_id', $country)
								->join('country', 'city.country_id', '=', 'country.country_id')
								->select('city.city_id', $city_col, 'country.country_code')->get();
	}

	public function prepareSignUp(Request $request){
		$lang = $request->input('lang');
		
		if($lang == "ar"){
			$cat_col 	 = "cat_ar_name AS cat_name";
			$country_col = "country_ar_name AS country_name";
		}else{
			$cat_col 	 = "cat_en_name AS cat_name";
			$country_col = "country_en_name AS country_name";
		}
		

        $countries = $this->getCountries($lang);

        //get categories
		$cats = Categories::where('publish', 1)->select('cat_id', $cat_col)->get();
		
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'countries' => $countries,'cats' => $cats]);
	}

	protected function saveImage($data, $image_ext, $path){
		if(!empty($data)){
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

	public function uploadDeliveryImages($img, $ext, $path){
		if(!empty($img)){
			$link = $this->saveImage($img, $ext, $path);
			if($link != ""){
				$link = url($link);
			}else{
				return false;
			}
		}else{
			$link = '';
		}

		return $link;
	}

	public function signUp(Request $request){
	    
	    
	  //  return response() -> json($request);
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'كلا من المدينه و الدوله يجب ان تكون ارقاما',
				3 => 'صيغة البريد الإلكترونى غير صحيحة',
				4 => 'الرقم السرى لا يجب ان يقل عن 8 حروف',
				5 => 'فشل فى رفع الصور',
				6 => 'فشل التسجيل، من فضلك حاول فى وقت لاحق',
				7 => 'البريد الإلكترونى مستخدم من قبل',
				8 => 'رقم الجوال مستخدم من قبل',
				9 => 'جميع الصوره المطلوبه يجب ان تكون فى صيغة jpeg او png',
			    10 => 'التصنيفات مطلوبه',
			);
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'country and city must be numeric',
				3 => 'E-mail must be in email format',
				4 => 'Password can not be less than 8 characters',
				5 => 'Failed to upload images',
				6 => 'Failed to sign up, please try again later',
				7 => 'Repeated email',
				8 => 'Repeated phone',
				9 => 'Requested images must be jpeg or png type',
				10 => 'Categories are required',
			);
		}

		$messages = array(
			'required'       => 1,
			'numeric'        => 2,
			'email'          => 3,
			'min'            => 4,
			'email.unique'   => 7,
			'phone.unique'   => 8,
			'mimes' 		 => 9
		);

		$validator = Validator::make($request->all(), [
			'firstname'    		=> 'required',
			'secondname'   		=> 'required',
			'thirdname'    		=> 'required',
			'lastname'     		=> 'required',
			'country_id'   		=> 'required|numeric',
			'city_id'      		=> 'required|numeric',
			'email' 	   		=> 'required|email|unique:deliveries',
			'phone' 	   		=> 'required|unique:deliveries',
			'country_code' 		=> 'required',
			'password'     		=> 'required|min:8',
			'car_type'     		=> 'required',
			'longitude'    		=> 'required',
			'latitude'     		=> 'required',
			'profile_pic'  		=> 'sometimes|nullable',
			'image_ext'    		=> 'required_with:profile_pic',
			'id_img'       		=> 'required',
			'id_date'       	=> 'required',
			'car_img1'       	=> 'required',
			'car_img2' 	        => 'required',
			'car_img3'  	    => 'required',
			'license_img'    	=> 'required',
			'license_date'    	=> 'required',
			'authorization_img' => 'required',
			'insurance_img'     => 'required',
			'token'	            => 'required'

		], $messages);

		if($validator->fails()){
	 
		}else{



              if(empty($request->input('cats'))){
    				return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
    			}
    			
			// if(empty($request->input('images'))){
			// 	return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			// }else{
			// 	$images = $request->input('images');
			// }
			// if(in_array("", $request->input('images'))){
			// 	return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			// }
			//upload profile picture
			if(!empty($request->input('profile_pic'))){
				$profile_image = $this->saveImage($request->input('profile_pic'), $request->input('image_ext'), 'deliveryImages/');
				if($profile_image != ""){
					$profile_image = url($profile_image);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$profile_image = url('providerProfileImages/avatar_ic.png');
			}

			$car_img1 = $this->uploadDeliveryImages($request->input('car_img1'), 'jpeg', 'deliveryImages/');
			if($car_img1 == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}

			$car_img2 = $this->uploadDeliveryImages($request->input('car_img2'), 'jpeg', 'deliveryImages/');
			if($car_img2 == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}

			$car_img3 = $this->uploadDeliveryImages($request->input('car_img3'), 'jpeg', 'deliveryImages/');
			if($car_img3 == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}

			$insurance_img = $this->uploadDeliveryImages($request->input('insurance_img'), 'jpeg', 'deliveryImages/');
			if($insurance_img == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}

			$authorization_img = $this->uploadDeliveryImages($request->input('authorization_img'), 'jpeg', 'deliveryImages/');
			if($authorization_img == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}

			$license_img = $this->uploadDeliveryImages($request->input('license_img'), 'jpeg', 'deliveryImages/');
			if($license_img == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}

			$id_img = $this->uploadDeliveryImages($request->input('id_img'), 'jpeg', 'deliveryImages/');
			if($id_img == false){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			}
			//uploading image if exist
			// $attachId = 0;
			// $counter  = 0;
			// foreach($images AS $image){
			// 	$paper = $this->saveImage($image['image'], $image['ext'], 'deliveryImages/');

			// 	if($paper == ""){
			// 		return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			// 	}else{
			// 		$imageName = explode("/", $paper)[1];
			// 		$paper     = url($paper);
			// 		//get attach_no
			// 		if($attachId == 0){
			// 			$attachId = DB::table('attachments')->select(DB::raw("(SELECT (IFNULL(MAX(attach_id),0) + 1) FROM attachments) AS newAttachId"))->first()->newAttachId;
			// 		}
			// 		$inserts[$counter]['attach_id']   = $attachId;
			// 		$inserts[$counter]['attach_name'] = $imageName;
			// 		$inserts[$counter]['attach_path'] = $paper;
			// 		$counter++;
			// 	}
			// }

			// if(!empty($inserts)){
			// 	$check = DB::table('attachments')->insert($inserts);
			// 	if(!$check){
			// 		return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			// 	}
			// }

            
              
        			
        			
			$data['firstname']    		  = $request->input('firstname');
			$data['secondname']   		  = $request->input('secondname');
			$data['thirdname']    		  = $request->input('thirdname');
			$data['lastname']     		  = $request->input('lastname');
			$data['country_id']   		  = $request->input('country_id');
			$data['city_id']      		  = $request->input('city_id');
			$data['email']        		  = $request->input('email');
			$data['phone']        		  = $request->input('phone');
			$data['country_code'] 		  = '+'.$request->input('country_code');
			$data['password']     		  = $request->input('password');
			$data['car_type']     		  = $request->input('car_type');
			$data['longitude']    		  = $request->input('longitude');
			$data['latitude']     		  = $request->input('latitude');
			$data['full_name']    		  = $data['firstname']." ".$data['secondname']." ".$data['thirdname']." ".$data['lastname'];
			$data['image']        		  = $profile_image;
			$data['car_img1']       	  = $car_img1;
			$data['car_img2']       	  = $car_img2;
			$data['car_img3']       	  = $car_img3;
			$data['license_img']          = $license_img;
			$data['authorization_img']    = $authorization_img;
			$data['insurance_img']        = $insurance_img;
			$data['id_img']        		  = $id_img;
			$data['token']        		  = $request->input('token');
			$data['id_date']              = $request->input('id_date');
			$data['license_date']         = $request->input('license_date');
			$data['cats']                 = $request->input('cats');
			
			if(!empty($request->input('marketer_code'))){
				$data['marketer_code'] = $request->input('marketer_code');
			}else{
				$data['marketer_code'] = "";
			}
			try {
				$id = 0;
				DB::transaction(function() use ($data, &$id){
					$id = DB::table('deliveries')->insertGetId([
						'full_name'     	  => $data['full_name'],
						'firstname'     	  => $data['firstname'],
						'secondname'    	  => $data['secondname'],
						'thirdname'     	  => $data['thirdname'],
						'lastname'      	  => $data['lastname'],
						'country_id'    	  => $data['country_id'],
						'city_id'       	  => $data['city_id'],
						'email' 	    	  => $data['email'],
						'phone' 	    	  => $data['phone'],
						'country_code'  	  => $data['country_code'],
						'password'      	  => md5($data['password']),
						'car_type'      	  => $data['car_type'],
						'longitude'     	  => $data['longitude'],
						'latitude'      	  => $data['latitude'],
						'profile_pic'   	  => $data['image'],
						'car_img1'   		  => $data['car_img1'],
						'car_img2'   		  => $data['car_img2'],
						'car_img3'   		  => $data['car_img3'],
						'insurance_img'   	  => $data['insurance_img'],
						'authorization_img'   => $data['authorization_img'],
						'id_img'  			  => $data['id_img'],
						'id_date'  			  => $data['id_date'],
						'license_img'  		  => $data['license_img'],
						'license_date'        => $data['license_date'],
						'device_reg_id'		  => $data['token'],
						'marketer_code'       => $data['marketer_code']
					]);
					
					 
					  if($id){
							$inserts = array();
							for($i = 0; $i < count($data['cats']); $i++){
								$inserts[$i]['delivery_id'] = $id;
								$inserts[$i]['cat_id']      = $data['cats'][$i];
							}
							DB::table('deliveries_categories')->insert($inserts);
 
						}else{
						    
							return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
						}
						
						 
					DB::table('balances')->insert(['actor_id' => $id, 'current_balance' => 0, 'due_balance' => 0, 'type' => 'delivery']);
				});
				
				$delivery = $this->getDeliveryData($id, $lang, "get");
				// $delivery = DB::table('deliveries')->where('delivery_id', $id)
				// 			  ->select('delivery_id', 'profile_pic', 'car_img1', 'car_img2', 'car_img3', 'insurance_img', 'authorization_img', 'license_img', 'id_img', DB::raw('created_at AS created'))
				// 			  ->first();
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'delivery' => $delivery]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
			}
		}
	}

	public function activateDelivery(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التفعيل بنجاح وبإنتظار تفعيل الإدارة',
				1 => 'رقم التوصيل مطلوب',
				2 => 'فشل التفعيل حاول فى وقت لاحق',
				3 => 'نوع العملية يجب ان يكون فى (register, edit)'
			);
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$msg = array(
				0 => 'Activated successfully, and waitting for management approval',
				1 => 'delivery_id is required',
				2 => 'Failed to activate, please try again later',
				3 => 'type must be in (register, edit)'
			);
			$city_col = "city.city_en_name AS city_name";
		}
		if(!empty($request->input('delivery_id'))){
			if(empty($request->input('type'))){
				$status = 2;
			}elseif($request->input('type') == 'register'){
				$status = 2;
			}elseif($request->input('type') == 'edit'){
				$status = 1;
			}else{
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
			$check = Deliveries::where('delivery_id', $request->input('delivery_id'))->update(['status' => $status]);
			if($check){
				$getDelivery = $this->getDeliveryData($request->input('delivery_id'), $lang);
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'delivery' => $getDelivery]); 
			}else{
				return response()->json(['status'=> false, 'errNum' => 2, 'msg' => $msg[2]]);
			}
		}else{
			return response()->json(['status'=> false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
	}

	//method to redirect to reset passwrod page
	public function dforgetPassView($deliveryId){
		return view('dforgetPass', ['delivery_id' => $deliveryId, 'api_email' => $this->api_auth_email, 'api_pass' => $this->api_auth_password]);
	}

	//method to update password
	public function dForgetPassAction(Request $request){
		$validator = Validator::make($request->all(), [
			'password' => 'required|min:8',
			'password_confirmation' => 'required|min:8|same:password',
			'delivery_id'  => 'required|numeric'
		]);

		if($validator->fails()){
			return redirect()
						->back()
                        ->withErrors($validator)
                        ->withInput();
		}else{
			$check = Deliveries::where('delivery_id', $request->input('delivery_id'))
						 ->update(['password' => md5($request->input('password'))]);
			if($check){
				return redirect()->back()->with('success', 'Your password updated successfully');
			}else{
				return redirect()->back()->with('err', 'Proccess failed, please try again later');
			}
		}
	}

	public function sendMailApi(Request $request){
		$lang  = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'قم بتفقد البريد الإلكترونى الخاص بك لإعادة ضبط كلمة السر الخاصه بك',
				1 => 'البريد الإلكترونى مطلوب',
				2 => 'يجب ان يكون البريد الإلكترونى فى صيغة البيرد الإلكترونى',
				3 => 'هذا البريد الإلكترونى غير موجود',
				4 => 'فشل فى إرسال الميل من فضلك حاول فى وقت لاقح'
			);
		}else{
			$msg = array(
				0 => 'Please check your mail to reset your password',
				1 => 'Delivery email is required',
				2 => 'Delivery email must be in email format',
				3 => 'This email is not exist',
				4 => 'Failed to send, please try again later'
			);
		}

		$messages = array(
			'required' => 1,
			'email'    => 2, 
			'unique'   => 3
		);


		$validator = Validator::make($request->all(),[
			'email' => 'required|email|unique:deliveries'
		], $messages);

		if($validator->fails()){
			$errors = $validator->errors();
			$error  = $errors->first();
			if($error != 3){
				return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
			}else{
				//get user id
				$email = $request->input('email');
				$delivery  = Deliveries::where('email', $email)->first();
				if($delivery->count()){
					$deliveryId = $delivery->delivery_id;
					//here we gonna send email
					$subject = "Mathaq reset password link";
					$link    = "zad.al-yasser.info/public/api/dForgetPass/".$deliveryId;
					if($lang == "ar"){
						$message = "قم بزيارة الرابط التالى لإعادة ضبط كلمة السر الخاصة بك \n ".$link." \n\n ملحوظة إذا لم تقم بطلب إعادة ضبط كلمة السر الخاصة بك من فضلك تجاهل هذه الرسالة";
					}else{
						$message = "Please visit the next url to reset your password \n ".$link>" \n\n Note:- if you didn't request to reset your password, please ignore this email";
					}
					Mail::send('mail-template', ['content' => $message], function ($m) use ($delivery) {
			            $m->from('info@zad.al-yasser.info', 'Mathaq user application');
			            $m->to($delivery->email, $delivery->full_name)->subject("Mathaq reset password link");
			        });

			        if(count(Mail::failures()) > 0) {
					   return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
					} else {
					    return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
					}
			        
				}else{
					return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
				}
			}
		}else{
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}
	}

	public function deliveryLogin(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم الدخول',
				1 => 'رقم التليفون مطلوب',
				2 => 'تاكد من رقم التليفون مع إضافة كود الدوله', 
				3 => 'كلمة السر مطلوبه', 
				4 => 'خطأ فى البيانات',
				5 => 'لم يتم تفعيل الحساب بعد',
				6 => 'فى إنتظار تفعيل الإدارة',
				7 => 'رقم الجهاز مطلوب'
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
				6 => 'Waitting for management activation',
				7 => 'token is required'
			);
			$city_col = "city.city_en_name AS city_name";
		}
		$messages = array(
				'phone.required'    => 1,
				'password.required' => 3,
				'token.required' 	=> 7

			);
		$validator = Validator::make($request->all(), [
			'phone'    => 'required',
			'password' => 'required',
			'token'    => 'required'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $msg[$error]]); 
		}else{
			$getDelivery = $this->getDeliveryData(0, $lang, "login", $request->input('password'), $request->input('phone'));
			if($getDelivery != NULL && !empty($getDelivery) && $getDelivery->count()){
				Deliveries::where('delivery_id', $getDelivery->delivery_id)->update(['device_reg_id' => $request->input('token')]);
				if($getDelivery->status == 0 || $getDelivery->status == "0" || $getDelivery->status == 3 || $getDelivery->status == "3"){
					return response()->json(['status'=> false, 'errNum' => 5, 'delivery' => $getDelivery, 'msg' => $msg[5]]);
				}elseif($getDelivery->status == 2 || $getDelivery->status == "2"){
				    return response()->json(['status'=> false, 'errNum' => 6, 'delivery' => $getDelivery, 'msg' => $msg[6]]);
				}
				
				return response()->json(['status'=> true, 'errNum' => 0, 'delivery' => $getDelivery, 'msg' => $msg[0]]); 
			}else{
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]); 
			}
		}
	}

	public function fetchOrdersCounts(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required'
			);
		}

		if(empty($request->input('delivery_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$delivery_id = $request->input('delivery_id');
		}

		
		$today = date("Y-m-d");
		//get new orders count 
		$news    = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->where('status_id', 3)
										   ->count();

		$current = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->whereIn('status_id', [8])
										   ->where(DB::raw('DATE(expected_delivery_time)'), '<=',$today)
										   ->count();

		$futures = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->whereIn('status_id', [8])
										   ->where(DB::raw('DATE(expected_delivery_time)'), '>' ,$today)
										   ->count();

		$old = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->whereIn('status_id', [4,5,6,7])
										   ->count();

		$complains = DB::table('complains')->where('delivery_id', $delivery_id)->count();
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

	public function getComplains(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم م قدم الخدمه مطلبو',
				2 => 'من فضلك حدد الشكوى من من',
				3 => 'الشكوى من يجب ان كتون فى (delivery, user, both)'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required',
				2 => 'complain_from is required',
				3 => 'complain_from must be in (provider, user, both)'
			);
		}

		if(empty($request->input('delivery_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$delivery_id = $request->input('delivery_id');
		}

		if(empty($request->input('complain_from'))){
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);	
		}else{
			$complain_from = $request->input('complain_from');
		}

		if(!in_array($complain_from, array('provider', 'user', 'both'))){
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);	
		}
		$jointable    = "";
		$joincol1     = "";
		$joincol2     = "";
		$selectedCol  = "";
		$nullCol      = "";
		$conditions[] = array("complains.delivery_id", "=", $delivery_id);
		if($complain_from == "provider"){
			$conditions[] = array("complains.provider_id", "!=", 0);
			$conditions[] = array("complains.user_id", "=", 0);
			$jointable    = "providers";
			$joincol1     = "complains.provider_id";
			$joincol2     = "providers.provider_id";
			$selectedCol  = "IFNULL(providers.brand_name, '') AS provider_name";
			$nullCol      = "'' AS user_name";
		}elseif($complain_from == "user"){
			$conditions[] = array("complains.provider_id", "=", 0);
			$conditions[] = array("complains.user_id", "!=", 0);
			$jointable    = "users";
			$joincol1     = "complains.user_id";
			$joincol2     = "users.user_id";
			$selectedCol  = "IFNULL(users.full_name,'') AS user_name";
			$nullCol      = "'' AS provider_name";
		}
		$data = array();
		//get provider complains
		if($complain_from != 'both'){
			$complains = DB::table('complains')->where($conditions)
						    ->join($jointable, $joincol1, '=', $joincol2)
						    ->join('orders_headers', 'complains.order_id', '=', 'orders_headers.order_id')
						    ->select('complains.order_id', 'orders_headers.order_id', 'orders_headers.order_code', DB::raw($selectedCol), DB::raw($nullCol), 'complains.complain','complains.attach_no', 'complains.id')
						    ->get();
		}else{
			$complains = DB::table('complains')->where('complains.delivery_id', $delivery_id)
						    ->leftjoin('users', 'complains.user_id', '=', 'users.user_id')
						    ->leftjoin('providers', 'complains.provider_id', '=', 'providers.provider_id')
						    ->join('orders_headers', 'complains.order_id', '=', 'orders_headers.order_id')
						    ->select('complains.order_id', 'orders_headers.order_id', 'orders_headers.order_code', DB::raw('IFNULL(users.full_name, "") AS user_name'), DB::raw('IFNULL(providers.brand_name, "") AS provider_name'),'complains.complain','complains.attach_no', 'complains.id')
						    ->get();
		}

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

				array_push($data, ['user_name' => $row->user_name, 'provider_name' => $row->provider_name,'order_id' => $row->order_id, 'order_code' => $row->order_code, 'complain' => $row->complain, 'attaches' => $attaches]);
			}
		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $data]);
	}

	public function getProfileData(Request $request){
	    
		$lang = $request->input('lang');
		
		if($lang == "ar"){
		    
		   	$cat_col     = "cat_ar_name AS cat_name";
			$country_col = "country_ar_name AS country_name";
			$city_col    = "city_ar_name AS city_name";
			
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب'
			);
		}else{
		    
		    $cat_col     = "cat_en_name AS cat_name";
			$country_col = "country_en_name AS country_name";
			$city_col    = "city_en_name AS city_name";
			
			$msg = array(
				0 => '',
				1 => 'delivery_id is required'
			);
		}

		if(empty($request->input('delivery_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$id = $request->input('delivery_id');
		}

		$delivery = $this->getDeliveryData($id, $lang, $action = "get");

		if($delivery != NULL){
			$country = $delivery->country_id;
			$attach  = $delivery->attach_id;
		}else{
			$country = 0;
			$attach  = 0;
		}

		$countries = $this->getCountries($lang);
		$cities    = $this->getCountryCities($lang, $country);

		$attaches  = DB::table('attachments')->where('attach_id', $attach)
											 ->select('id', 'attach_path')
											 ->get();
											 
											 
		$cats            = DB::table('categories')->where('publish', 1)->select('cat_id', $cat_col, DB::raw('IF((SELECT COUNT(id) FROM 	deliveries_categories WHERE 	deliveries_categories.cat_id =  categories.cat_id AND delivery_id = '.$request->input('delivery_id').') > 0, 1, 0) AS chosen'))->get();
											 	
											 	

		return response()->json([
									'status' => true, 
									'errNum' => 0, 
									'msg'    => $msg[0], 
									'countries' => $countries, 
									'cities'    => $cities, 
									'attaches'  => $attaches,
									'delivery'  => $delivery,
									'cats'  	   => $cats
								]);
	}

	public function editProfile(Request $request){

		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم تعديل البيانات بنجاح',
				1 => 'كل الحقول ماعدا كلمة السر و صورة البورفايل مطلوبه',
				2 => 'كلا من المدينه و الدوله يجب ان تكون ارقاما',
				3 => 'صيغة البريد الإلكترونى غير صحيحة',
				4 => 'الرقم السرى لا يجب ان يقل عن 8 حروف',
				5 => 'فشل فى رفع الصور',
				6 => 'فشل تعديل البيانات من فضلك حاول فى وقت لاحق',
				7 => 'البريد الإلكترونى مستخدم من قبل',
				8 => 'رقم الجوال مستخدم من قبل',
				9 => 'غير مسموح لك بتعديل الاسم بعد مرور 24 ساعة من تاريخ التفعيل',
				10 => 'تاريخ انتهاء اثبات الشخصية مطلوب',
				11 => 'تاريخ انتهاء الرخصة مطلوب',
				12 => 'التصنيفات مطلوبه',
			);
		}else{
			$msg = array(
				0 => 'updated successfully',
				1 => 'All fields but password and profile pic are required',
				2 => 'country and city must be numeric',
				3 => 'E-mail must be in email format',
				4 => 'Password can not be less than 8 characters',
				5 => 'Failed to upload images',
				6 => 'Failed to update, please try again later',
				7 => 'Repeated email',
				8 => 'Repeated phone',
				9 => 'You can not update your name after 24H of activation',
				10 => 'id exp date is requires',
				11 => 'license exp date is requires',
				12 => 'Categories is required',
				
			);
		}

		$messages = array(
			'required' => 1,
			'numeric'  => 2, 
			'email'    => 3,
			'min'      => 4,
			'email.unique'   => 7,
			'phone.unique'   => 8
		);

		$validator = Validator::make($request->all(), [
			'delivery_id'  => 'required',
			'firstname'    => 'required',
			'secondname'   => 'required',
			'thirdname'    => 'required',
			'lastname'     => 'required',
			'country_id'   => 'required|numeric',
			'city_id'      => 'required|numeric',
			'email' 	   => 'required|email|unique:deliveries,email,'.$request->input('delivery_id').',delivery_id',
			'password'     => 'sometimes|nullable|min:8',
			'car_type'     => 'required',
			'longitude'    => 'required',
			'latitude'     => 'required',
			'profile_pic'  => 'sometimes|nullable',
			'image_ext'    => 'required_with:profile_pic'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
		    
		    $cats = $request->input('cats');
			if(empty($cats)){
				return response()->json(['status' => false, 'errNum' => 12, 'msg' => $msg[12]]);
			}



			$status = 1;
			$delivery = Deliveries::where('delivery_id', $request->input('delivery_id'))
								   ->select('attach_id', DB::raw("created_at AS created") , "admin_activation_time", 'full_name')
								   ->first();
			$attachId = $delivery->attach_id;
			$created  = strtotime($delivery->admin_activation_time);
		

			$old_name = $delivery->full_name;

			$new_name = $request->input('firstname')." ".$request->input('secondname')." ".$request->input('thirdname')." ".$request->input('lastname');
			//date_default_timezone_set('Asia/Riyadh');
			$now_saudi = (time()+10800);
			$now      = strtotime(date('Y-m-d h:i:s'));
			$createdPlus24 = strtotime('+24 hours', $created);
			if($now_saudi <= $createdPlus24){
				$allow = "yes";
			}else{
				$allow = "no";
			}
			if($new_name != $old_name && $allow == "no"){
				return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
			}
			// $images = $request->input('images');
			// if(!empty($request->input('images'))){
			// 	$status = 2;
			// }

			// if(!empty($request->input('images')) && in_array("", $request->input('images'))){
			// 	return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			// }

			//upload profile picture
			if(!empty($request->input('profile_pic'))){
				$profile_image = $this->saveImage($request->input('profile_pic'), $request->input('image_ext'), 'deliveryImages/');
				if($profile_image != ""){
					$profile_image = url($profile_image);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$profile_image = "";
			}

			if(!empty($request->input('id_img'))){

			    if($request->input("id_date") == "" || $request->input("id_date" == null)){
			        // return error
                    return response()->json(['status' => false, 'msg' => $msg[10]]);
                }
				$status = 2;
				$id_img = $this->saveImage($request->input('id_img'), 'jpeg', 'deliveryImages/');
				if($id_img != ""){
					$id_img = url($id_img);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$id_img = "";
			}

			if(!empty($request->input('car_img1'))){
				$status = 2;
				$car_img1 = $this->saveImage($request->input('car_img1'), 'jpeg', 'deliveryImages/');
				if($car_img1 != ""){
					$car_img1 = url($car_img1);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$car_img1 = "";
			}

			if(!empty($request->input('car_img2'))){
				$status = 2;
				$car_img2 = $this->saveImage($request->input('car_img2'), 'jpeg', 'deliveryImages/');
				if($car_img2 != ""){
					$car_img2 = url($car_img2);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$car_img2 = "";
			}

			if(!empty($request->input('car_img3'))){
				$status = 2;
				$car_img3 = $this->saveImage($request->input('car_img3'), 'jpeg', 'deliveryImages/');
				if($car_img3 != ""){
					$car_img3 = url($car_img3);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$car_img3 = "";
			}

			if(!empty($request->input('license_img'))){
			    if($request->input("license_date") == "" || $request->input("license_date")== null){
                    return response()->json(['status' => false, 'msg' => $msg[11]]);
                }
				$status = 2;
				$license_img = $this->saveImage($request->input('license_img'), 'jpeg', 'deliveryImages/');
				if($license_img != ""){
					$license_img = url($license_img);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$license_img = "";
			}

			if(!empty($request->input('authorization_img'))){
				$status = 2;
				$authorization_img = $this->saveImage($request->input('authorization_img'), 'jpeg', 'deliveryImages/');
				if($authorization_img != ""){
					$authorization_img = url($authorization_img);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$authorization_img = "";
			}

			if(!empty($request->input('insurance_img'))){
				$status = 2;
				$insurance_img = $this->saveImage($request->input('insurance_img'), 'jpeg', 'deliveryImages/');
				if($insurance_img != ""){
					$insurance_img = url($insurance_img);
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
				}
			}else{
				$insurance_img = "";
			}

			// if(!empty($request->input('deleted_images'))){
			// 	$status = 2;
			// 	$deleted_images = $request->input('deleted_images');
			// 	DB::table('attachments')->whereIn('id', $deleted_images)->delete();
			// 	$get = DB::table('attachments')->where('attach_id', $attachId)->first();
			// 	if($get == NULL){
			// 		$attachId = 0;
			// 	}
			// }
			//uploading image if exist
			// $counter  = 0;
			// foreach($images AS $image){
			// 	$paper = $this->saveImage($image['image'], $image['ext'], 'deliveryImages/');
			// 	if($paper == ""){
			// 		return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			// 	}else{
			// 		$imageName = explode("/", $paper)[1];
			// 		$paper     = url($paper);
			// 		//get attach_no
			// 		if($attachId == 0){
			// 			$attachId = DB::table('attachments')->select(DB::raw("(SELECT (IFNULL(MAX(attach_id),0) + 1) FROM attachments) AS newAttachId"))->first()->newAttachId;
			// 		}
			// 		$inserts[$counter]['attach_id']   = $attachId;
			// 		$inserts[$counter]['attach_name'] = $imageName;
			// 		$inserts[$counter]['attach_path'] = $paper;
			// 		$counter++;
			// 	}
			// }

			// if(!empty($inserts)){
			// 	$check = DB::table('attachments')->insert($inserts);
			// 	if(!$check){
			// 		return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg]);
			// 	}
			// }

			if($allow == 'yes'){
				$data['firstname']    = $request->input('firstname');
				$data['secondname']   = $request->input('secondname');
				$data['thirdname']    = $request->input('thirdname');
				$data['lastname']     = $request->input('lastname');
				$data['full_name']    = $data['firstname']." ".$data['secondname']." ".$data['thirdname']." ".$data['lastname'];
			}
			
			$data['country_id']     = $request->input('country_id');
			$data['city_id']        = $request->input('city_id');
			$data['email']          = $request->input('email');

            $date_id_date        = DateTime::createFromFormat('Y-m-d', $request->input("id_date"));
            $data['id_date']     = $date_id_date;

            $date_license_date = DateTime::createFromFormat('Y-m-d', $request->input("license_date"));
            $data['license_date']        = $date_license_date;



			if(!empty($request->input('password'))){
				$data['password']     = md5($request->input('password'));
			}
			$data['car_type']     = $request->input('car_type');
			$data['longitude']    = $request->input('longitude');
			$data['latitude']     = $request->input('latitude');
			$data['status']       = $status;
			if($profile_image != ""){
				$data['profile_pic'] = $profile_image;
			}

			if($id_img != ""){
				$data['id_img'] = $id_img;
			}

			if($car_img1 != ""){
				$data['car_img1'] = $car_img1;
			}

			if($car_img2 != ""){
				$data['car_img2'] = $car_img2;
			}

			if($car_img3 != ""){
				$data['car_img3'] = $car_img3;
			}

			if($license_img != ""){
				$data['license_img'] = $license_img;
			}

			if($authorization_img != ""){
				$data['authorization_img'] = $authorization_img;
			}

			if($insurance_img != ""){
				$data['insurance_img'] = $insurance_img;
			}
			try {
				$id = $request->input('delivery_id');
				DB::table('deliveries')->where('delivery_id', $id)->update($data);
				
			   DB::table('deliveries_categories')->where('delivery_id', $id)->delete();
			   
			   
			   $inserts = array();
					for($i = 0; $i < count($cats); $i++){
						$inserts[$i]['delivery_id'] = $id;
						$inserts[$i]['cat_id']      = $cats[$i];
					}
					
				DB::table('deliveries_categories')->insert($inserts);
					
					 

				$delivery = $this->getDeliveryData($id, $lang);
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'delivery' => $delivery]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
			}
		}
	}

	public function getDeliveryOrders(Request $request){
		$lang     = $request->input('lang');
		$allPages = $request->input('allPages');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب',
				2 => 'نوع الطلبات مطلوب',
				3 => 'نوع العمليه يجب ان يكون 1 او 2 او 4',
				4 => 'لا يوجد طلبات بعد',
				5 => 'رقم الموصل غير صحيح'
			);
			$payment_col  = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$status_col	  = "order_status.ar_desc AS status_text";
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required',
				2 => 'type is required',
				3 => 'type must be 1, 2 or 4',
				4 => 'There is no ordes yet',
				5 => 'Invalid delivery id'
			);
			$payment_col  = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$status_col	  = "order_status.en_desc AS status_text";
		}

		$messages  = array(
			'delivery_id.required' => 1,
			'type.required'        => 2,
			'in'                   => 3,
			'exists'			   => 5
		);
		$validator = Validator::make($request->all(), [
			'delivery_id' => 'required|exists:deliveries,delivery_id',
			'type'        => 'required|in:1,2,4'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$delivery_id  = $request->input('delivery_id');
			$type 		  = $request->input('type');
			$today        = date('Y-m-d');
			$conditions[] = ['deliveries.delivery_id','=', $delivery_id];
			$inCondition  = [];
//			if($type == 1){
//				$inCondition = [3];
//				// array_push($conditions, ['orders_headers.status_id' , '=', 3]);
//			}elseif($type == 2){
//				$inCondition = [8];
//				// array_push($conditions, ['orders_headers.status_id' , '>', 3]);
//				// array_push($conditions, ['orders_headers.status_id' , '!=', 6]);
//				array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
//			}elseif($type == 3){
//				$inCondition = [8];
//				// array_push($conditions, ['orders_headers.status_id' , '>', 3]);
//				// array_push($conditions, ['orders_headers.status_id' , '!=', 6]);
//				array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '>', $today]);
//			}else{
//				$inCondition = [4,5,6,7];
//				// array_push($conditions, ['orders_headers.status_id' , '=', 6]);
//			}
			if($type == 1){
				$inCondition = [3];
				// array_push($conditions, ['orders_headers.status_id' , '=', 3]);
			}elseif($type == 2){
				$inCondition = [8];
				// array_push($conditions, ['orders_headers.status_id' , '>', 3]);
				// array_push($conditions, ['orders_headers.status_id' , '!=', 6]);
				//array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
			}else{
				$inCondition = [4,5,6,7];
				// array_push($conditions, ['orders_headers.status_id' , '=', 6]);
			}

			

			//get orders
			if(empty($allPages) || $allPages == "0" || $allPages == 0){
				$orders = DB::table('orders_headers')
                            ->where($conditions)
							->whereIn('orders_headers.status_id', $inCondition)
						    ->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
						    ->join('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
						    ->join('delivery_methods', 'orders_headers.delivery_method' ,'=', 'delivery_methods.method_id')
						    ->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
						    ->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
						    ->select('orders_headers.user_longitude','orders_headers.user_latitude','orders_headers.order_id','providers.brand_name AS provider_name', 'orders_headers.address', $payment_col, $delivery_col, DB::raw("(SELECT count(order_details.id) FROM order_details WHERE order_details.order_id = orders_headers.order_id) AS meals_count"), $status_col,DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.transfer_to_delivery_at) AS created_time'))
						    ->orderBy('orders_headers.order_id', 'DESC')
						    ->paginate(10);
			}else{
				$orders['data'] = DB::table('orders_headers')->where($conditions)
							->whereIn('orders_headers.status_id', $inCondition)
							->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
							->join('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
							->join('delivery_methods', 'orders_headers.delivery_method' ,'=', 'delivery_methods.method_id')
							->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
							->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
							->select('orders_headers.user_longitude','orders_headers.user_latitude','orders_headers.order_id','providers.brand_name AS provider_name', 'orders_headers.address', $payment_col, $delivery_col, DB::raw("(SELECT count(order_details.id) FROM order_details WHERE order_details.order_id = orders_headers.order_id) AS meals_count"), $status_col,DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.transfer_to_delivery_at) AS created_time'))
							->orderBy('orders_headers.order_id', 'DESC')
							->get();
			}

			//get allowed time to accept the order
			if($type == 1){
				$get_time_counter = DB::table("app_settings")->first();
				if($get_time_counter != NULL){
					$time_counter_in_hours  = ($get_time_counter->max_time_to_accept_order) / 60;
					$time_counter_in_min    = $get_time_counter->max_time_to_accept_order;
				}else{
					$time_counter_in_hours = 0;
					$time_counter_in_min   = 0;
				}
			}else{
				$time_counter_in_hours = 0;
				$time_counter_in_min   = 0;
			}

			$today_date = date('Y-m-d');
			$now        = date('h:i:s');
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
				2 => 'رقم الموصل مطلوب',
				3 => 'نوع العمليه مطلوب',
				4 => 'نوع العمليه يجب ان يكون (accept or reject)',
				5 => 'فشلت العمليه من فضلك حاول فى وقت لاحق'
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'order_id is required',
				2 => 'delivery_id is required',
				3 => 'type is required',
				4 => 'type must be (accept or reject)',
				5 => 'Process failed please try again later'
			);
		}

		$messages = array(
			'order_id.required'    => 1,
			'delivery_id.required' => 2, 
			'type.required'        => 3,
			'in' 				   => 4
		);

		$validator = Validator::make($request->all(), [
			'order_id' => 'required', 
			'delivery_id' => 'required', 
			'type'        => 'required|in:accept,reject',
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$type = $request->input('type');
			
			if($type == "accept"){
				$status = 8;
				if($lang == "ar"){
				    $notify_title = "تم قبول الطلب";
    				$notify_message = "تم قبول الطلب من قبل الموصل";   
				}else{
				    $notify_title = "delivery accept order";
				    $notify_message = "delivery accepted this order";
				}
			}else{
				$status = 2;
				if($lang == "ar"){
				    $notify_title = "رفض الطلب";
    				$notify_message = "تم رفض الطلب من قبل الموصل";    
				}else{
				    $notify_title = "reject order from delivery";
				    $notify_message = "delivery rejected this order";
				}
			}
			try {
				$delivery_id = $request->input('delivery_id');
				$order_id    = $request->input('order_id');
				DB::transaction(function() use ($status, $order_id, $delivery_id){
					DB::table("orders_headers")->where('order_id', $order_id)->update(['status_id' => $status]);
					DB::table("order_details")->where('order_id', $order_id)->update(['status' => $status]);
				});
				$notif_data = array();
				$notif_data['title']      = $notify_title;
			    $notif_data['message']    = $notify_message;
			    $notif_data['order_id']   = $order_id;
			    $provider_data = DB::table("orders_headers")
			                        ->join("providers" , "providers.provider_id" , "orders_headers.provider_id")
			                        ->where("orders_headers.order_id" , $order_id)
			                        ->select("providers.device_reg_id")
			                        ->first();

			    if($provider_data != null){
			        if($provider_data->device_reg_id != null){
			            $push_notif = $this->singleSend($provider_data->device_reg_id, $notif_data, $this->provider_key);
			        }
			    }
				
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}
		}
	}

	public function getDeliveryBalance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required'
			);
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'delivery_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
		    $balance = DB::table('balances')
                ->where('actor_id', $request->input('delivery_id'))
                ->where('type', 'delivery')
                ->select('due_balance', 'current_balance', 'forbidden_balance')
                ->first();
            //get current balance
            $current = DB::table('orders_headers')
                ->where('payment_type', 2)
                ->where('status_id', 4)
                ->where('delivery_id', $request->input('delivery_id'))
                ->where('delivery_balance_status', 1)
                ->where('delivery_complain_flag', 0)
                ->sum(DB::raw('delivery_price - delivery_app_value'));

            //get due balance
            $due = DB::table('orders_headers')
                ->where('payment_type', 1)
                ->where('status_id', 4)
                ->where('provider_id', $request->input('provider_id'))
                ->where('delivery_balance_status', 1)
                ->sum('delivery_app_value');

            //forbidden balance
            $forbidden = DB::table('orders_headers')
                ->where('payment_type', 2)
                ->where('status_id', 4)
                ->where('provider_id', $request->input('provider_id'))
                ->where('delivery_balance_status', 1)
                ->where('delivery_complain_flag', 1)
                ->sum(DB::raw('delivery_price - delivery_app_value'));

            // delivery bank data
            // check if the user has bank data
            $delivery_bank = DB::table("withdraw_balance")
                        ->select("*")
                        ->where("actor_id" , $request->input("delivery_id"))
                        ->where("type" , "delivery")
                        ->get();
            if($delivery_bank !== null && count($delivery_bank) != 0){
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
            date_default_timezone_set('Asia/Riyadh');
            $timestamp =  date("Y/m/d H:i:s", time());
            $balance = array('current_balance' => $current, 'due_balance' => $due, 'forbidden_balance' => $forbidden , "updated_at" => $timestamp);
            return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'balance' => $balance, "bank_name" => $bank_name , "bank_phone" => $bank_phone,"account_num" => $bank_account_num  , "bank_username" => $bank_username]);
		}
	}

	public function withdraw(Request$request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'رقم الموصل مطلوب',
				2 => 'الرصيد الحالى مطلوب',
				3 => 'الرصيد المستحق مطلوب',
				4 => 'فشلت العملية من فضلك حاول لاحقا',
				5 => 'لديك طلبات لم يتم الرد عليها بعد',
				6 => 'ادخل رقم الرصيد المستحق المراد سحبة',
				7 => 'current_balance يجب ان يكون رقم',
                8 => 'ليس لديك رصيد كافى لاتمام هذة العملية',
                9 => 'رصيدك الحالى اقل من الحد الادنى لسحب الرصيد',
                10 => 'رقم الرصيد الحالى مطلوب',
                11 => 'الاسم مطلوب',
                12 => 'رقم الحساب مطلوب',
                13 => 'رقم الهاتف مطلوب',
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'delivery_id is required',
				2 => 'current_balance is required',
				3 => 'due_balance is required',
				4 => 'Process failed, please try again later',
				5 => 'You already have pending requests',
				6 => 'Enter a valid current_balance number',
				7 => 'current_balance must be a number',
                8 => "You Don't have enough balance",
				9 => "Your balance is less than minimum balance to withdraw",
                10 => 'bank_name is required',
                11 => 'name is required',
                12 => 'account_num is required',
                13 => 'phone is required',
                14 => 'forbidden_balance is required',
                15 => 'forbidden_balance must be a number'
			);
		}

		$messages = array(
			'delivery_id.required'     => 1,
			'current_balance.required' => 2,
			'current_balance.min'      => 6,
			'current_balance.numeric'  => 7,
			'due_balance.required'     => 3,
            'bank_name.required'       => 10,
            'name.required'            => 11,
            'account_num.required'     => 12,
            'phone.required'           => 13,
            'forbidden_balance.required' => 14,
            "forbidden_balance.numeric" => 15
		);

		$validator = Validator::make($request->all(), [
			'delivery_id'     => 'required',
			'current_balance' => 'required|numeric',
			'due_balance'     => 'required',
            'bank_name'       => 'required',
            'name'            => 'required',
            'account_num'     => 'required',
            'phone' 	      => 'required' , 
            'forbidden_balance' => 'required|numeric'

		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{

			//check if there is pending requests
			$check  = DB::table('withdraw_balance')->where('actor_id', $request->input('delivery_id'))
												   ->where('type', 'delivery')
												   ->where('status', 1)
												   ->first();

            // insert bank account data into database
            $actor_bank_data = DB::table("withdraw_balance")
                ->where("actor_id" , $request->input("delivery_id"))
                ->where("type" , "delivery")
                ->first();
            // if($actor_bank_data !== null){
            //     // update bank data
            //     DB::table("withdraw_balance")
            //         ->where("actor_id" , $request->input("delivery_id"))
            //         ->where("type" , "delivery")
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
            //             "actor_id" => $request->input("delivery_id"),
            //             "type" => "delivery",
            //             "name" => $request->input("name"),
            //             "phone" => $request->input("phone"),
            //             "bank_name" => $request->input("bank_name"),
            //             "account_num" => $request->input("account_num"),
            //             "created_at" =>date('Y-m-d h:i:s')
            //         ]);
            // }
            if($request->input("current_balance") < 0.1){
                return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
            }
			if($check != NULL){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}

            // check if the user requested blance is avaliable
            $delivery_balace = DB::table("balances")
                ->select("current_balance")
                ->where("actor_id" , $request->input("delivery_id"))
                ->where("type" , "delivery")
                ->first();
            $delivery_current_balace = $delivery_balace->current_balance;

            if($request->input("current_balance") > $delivery_current_balace){
                return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
            }


            //check if the current balance is greater than min limit of withdrawing
            $min_balance = DB::table("app_settings")
                ->select("min_balace_to_withdraw")
                ->first();
            if($request->input("current_balance") < $min_balance->min_balace_to_withdraw){
                return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
            }


			$insert = DB::table("withdraw_balance")->insert([
						 'actor_id'        => $request->input('delivery_id'),
						 'current_balance' => $request->input('current_balance'),
						 'due_balance'     => $request->input('due_balance'),
                         'forbidden'       => $request->input('forbidden_balance'),
                         'status'          =>  1,
                         'bank_name' 	   => $request->input('bank_name'),
                         'name' 		   => $request->input('name'),
                         'account_num' 	   => $request->input('account_num'),
                         'phone' 		   => $request->input('phone'),
						 'type' 		   => 'delivery'
					  ]);
			if($insert){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
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
		}

		$messages = array(
			'required' => 1,
			'in'	   => 2
		);

		$validator = Validator::make($request->all(), [
			'delivery_id' 		=> 'required',
			'switch'			=> 'required|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$check = Deliveries::where('delivery_id', $request->input('delivery_id'))
							  ->update(['receive_orders' => $switch]);
			if($check){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		}
	}

	public function orderFinalAction(Request $request){
		$lang = $request->input('lang');
		$type = $request->input('type');
		if($lang == "ar"){
			if($type == 0){
				$m = 'تم إلغاء الطلب';
			}elseif($type == 1){
				$m = 'تم تسليم الطلب بنجاح';
			}else{
				$m = 'تمت العملية بنجاح';
			}
			$msg = array(
				0  => $m,
				1  => 'كل الحقول مطلوبه',
				2  => 'قيمة نوع العملية يجب ان تنحصر بين 0 و 1',
				3  => 'فشلت العمليه'
			);
		}else{
			if($type == 0){
				$m = 'Order canceled successfully';
			}elseif($type == 1){
				$m = 'Order delivered successfully';
			}else{
				$m = 'Process done successfully';
			}
			$msg = array(
				0  => $m,
				1  => 'All fields are required',
				2  => 'type value must be between 0 and 1',
				3  => 'Process failed',
			);
		}

		$messages = array(
			'required' => 1,
			'in'	   => 2
		);

		$validator = Validator::make($request->all(), [
			'order_id'    => 'required',
			'delivery_id' => 'required',
			'type'		  => 'required|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			if($type == 0){
				//cancel order
				DB::table("orders_headers")->where('order_id', $request->input('order_id'))->update(["status_id" => 2]);
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				//get order payment type
				$data           = DB::table("orders_headers")->where('order_id', $request->input('order_id'))->select('payment_type', 'delivery_price', 'app_value' , 'delivery_app_value')->first();
				$payment_type   = $data->payment_type;
				$delivery_price = $data->delivery_price;
				$app_value      = $data->app_value;
				$delivery_app_value      = $data->delivery_app_value;
				$net 			= ($delivery_price - $app_value);
				$delivery_id    = $request->input('delivery_id');
				$order_id       = $request->input('order_id');
				try {
					DB::transaction(function() use($app_value, $net, $delivery_id, $order_id, $payment_type, $delivery_app_value, $delivery_price){
						DB::table('orders_headers')->where('order_id', $order_id)->update(['status_id' => 4]);
						if($payment_type != 1){  // try to change it to == 2 and test it 
							DB::table("balances")->where("actor_id", $delivery_id)
												 ->where('type', 'delivery')
												 ->update([ 'current_balance' => DB::raw('current_balance + '. $delivery_price) ]);
						}else{
							DB::table("balances")->where("actor_id", $delivery_id)
												 ->where('type', 'delivery')
												 ->update([ 'due_balance' => DB::raw('due_balance + '. $delivery_app_value) ]);
						}
					});

					if($lang == "ar"){
						if($type == 1){
							$title   = "توصيل الطلب";
							$message = "تم توصيل طلبك بنجاح";
							$type    = 'evaluate';
						}else{
							$title   = "إلغاء طلب";
							$message = "تم إلغاء طلبك من قبل الموصل";
							$type    = 'order';
						}
					}else{
						if($type == 1){
							$title   = "Order delivered";
							$message = "Your order has been delivered successfully";
							$type    = 'delivery_evaluate';
						}else{
							$title   = "Order canceled";
							$message = "Your order has been canceled by delivery";
							$type    = 'order';
						}
					}
					//get user and provider tokens
					$getTokens = DB::table('orders_headers')
								   ->where('orders_headers.order_id', $order_id)
								   ->join('users', 'orders_headers.user_id', '=', 'users.user_id')
								   ->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
								   ->select('providers.device_reg_id AS provider_token', 'users.device_reg_id AS user_token')
								   ->first();
					$notif_data = array();
					$notif_data['title']      = $title;
				    $notif_data['message']    = $message;
				    $notif_data['order_id']   = $order_id;
				    $notif_data['notif_type'] = $type;
					$push_notif = $this->singleSend($getTokens->user_token, $notif_data, $this->user_key);
					$notif_data['notif_type'] = 'order';
					$push_notif = $this->singleSend($getTokens->provider_token, $notif_data, $this->provider_key);
					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
				}
			}
		}
	}

	public function setLocTracker(Request $request){
		DB::table('orders_headers')->where('order_id', $request->input('order_id'))
								   ->update([
								   		'track_long' => $request->input('lng'),
								   		'track_lat'  => $request->input('lat')
								   	]);
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '']);
	}

	public function getLocTracker(Request $request){
		$get = DB::table('orders_headers')->where('order_id', $request->input('order_id'))
										  ->select('track_long', 'track_lat')
										  ->first();
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'locations' => $get]);
	}
}