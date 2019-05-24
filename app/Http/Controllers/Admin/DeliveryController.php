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
use App\Deliveries;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

class DeliveryController extends Controller
{
	public function __construct(){
		
	}

	public function getDeliveryIncomeView(){
		$deliveries = Deliveries::get();
		return view('cpanel.deliveries.income', compact('deliveries'));
	}

	public function create(){
		//get countries
		$countries = DB::table('country')->where('publish', 1)->get();
		//get categories
		$categories = DB::table('categories')->where('publish', 1)->get();
		return view('cpanel.deliveries.create', compact('countries', 'categories'));
	}

	public function uploadImage($file, $folder, $title){
        $fileName = $title.'-'.time().$file->getClientOriginalName();
        $path = url($folder.'/'.$fileName);
        $uploaded = $file->move(public_path().'/'.$folder.'/', $fileName);
        if(!$uploaded){
            return false;
        }else{
        	return $path;
        }
	}

	public function show(){
		$deliveries = Deliveries::where('deliveries.publish', 1)
							    ->join('city', 'deliveries.city_id', '=','city.city_id')
							    ->join('country', 'deliveries.country_id', '=','country.country_id')
							    ->select('deliveries.*', DB::raw('country.country_ar_name AS country'), DB::raw('city.city_ar_name AS city'))
							    ->get();
		return view('cpanel.deliveries.deliveries', compact('deliveries'));
	}

	public function store(Request $request){
		$validator = Validator::make($request->all(), [
			'identity'      => 'required|mimes:jpeg,png,jpg',
			'license'       => 'required|mimes:jpeg,png,jpg',
			'insurance'     => 'required|mimes:jpeg,png,jpg',
			'authorization' => 'required|mimes:jpeg,png,jpg',
			'car1'          => 'required|mimes:jpeg,png,jpg',
			'car2'          => 'required|mimes:jpeg,png,jpg',
			'car3'          => 'required|mimes:jpeg,png,jpg',
			'email'         => 'required|unique:deliveries,email',
			'phone'         => 'required|unique:deliveries,phone'
		]);

		if($validator->fails()){
			return redirect()->back()->withInput()->withErrors($validator->errors());
		}

		if($request->hasFile('identity')){
			$identity = $this->uploadImage($request->file('identity'), 'deliveryImages', 'delivery');
            if(!$identity){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload identity image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if($request->hasFile('license')){
			$license = $this->uploadImage($request->file('license'), 'deliveryImages', 'delivery');
            if(!$license){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload license image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if($request->hasFile('insurance')){
			$insurance = $this->uploadImage($request->file('insurance'), 'deliveryImages', 'delivery');
            if(!$insurance){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload insurance image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if($request->hasFile('authorization')){
			$authorization = $this->uploadImage($request->file('authorization'), 'deliveryImages', 'delivery');
            if(!$authorization){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload authorization image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if($request->hasFile('car1')){
			$car1 = $this->uploadImage($request->file('car1'), 'deliveryImages', 'delivery');
            if(!$car1){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload car image 1 image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if($request->hasFile('car2')){
			$car2 = $this->uploadImage($request->file('car2'), 'deliveryImages', 'delivery');
            if(!$car2){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload car image 2 image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if($request->hasFile('car3')){
			$car3 = $this->uploadImage($request->file('car3'), 'deliveryImages', 'delivery');
            if(!$car3){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload car image 3 image');
            return redirect()->back()->withInput()->withErrors($errors);
        }
        $data['firstname']    		  = $request->input('fname');
		$data['secondname']   		  = $request->input('sname');
		$data['thirdname']    		  = $request->input('tname');
		$data['lastname']     		  = $request->input('lname');
		$data['country_id']   		  = $request->input('countries');
		$data['city_id']      		  = $request->input('cities');
		$data['email']        		  = $request->input('email');
		$data['phone']        		  = $request->input('phone');
		$data['country_code'] 		  = $request->input('country_code');
		$data['password']     		  = md5($request->input('password'));
		$data['car_type']     		  = $request->input('car_type');
		$data['longitude']    		  = '11';
		$data['latitude']     		  = '11';
		$data['full_name']    		  = $data['firstname']." ".$data['secondname']." ".$data['thirdname']." ".$data['lastname'];
		$data['image']        		  = url('providerProfileImages/avatar_ic.png');
		$data['car_img1']       	  = $car1;
		$data['car_img2']       	  = $car2;
		$data['car_img3']       	  = $car3;
		$data['license_img']          = $license;
		$data['authorization_img']    = $authorization;
		$data['insurance_img']        = $insurance;
		$data['id_img']        		  = $identity;
		$data['token']        		  = NULL;
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
					'license_img'  		  => $data['license_img'],
					'device_reg_id'		  => $data['token'],
					'marketer_code'       => $data['marketer_code']
				]);

				DB::table('balances')->insert(['actor_id' => $id, 'current_balance' => 0, 'due_balance' => 0, 'type' => 'delivery']);
			});
			$request->session()->flash('success', 'Delivery added successfully');
			return redirect()->route('deliveries.show');
		} catch (Exception $e) {
			$errors = array('Failed to add, please try again later');
			return redirect()->back()->withInput()->withErrors($errors);
		}
	}

	public function edit($id){
		$delivery = Deliveries::where('delivery_id', $id)->first();
		$countries = DB::table('country')->where('publish', 1)->get();
		if($delivery != NULL){
			$cities = DB::table('city')->where('country_id', $delivery->country_id)->get();
		}else{
			$cities = array();
		}
		return view('cpanel.deliveries.edit',compact('delivery', 'countries', 'cities'));
	}

	public function update(Request $request){
		$validator = Validator::make($request->all(), [
			'identity'      => 'nullable|mimes:jpeg,png,jpg',
			'license'       => 'nullable|mimes:jpeg,png,jpg',
			'insurance'     => 'nullable|mimes:jpeg,png,jpg',
			'authorization' => 'nullable|mimes:jpeg,png,jpg',
			'car1'          => 'nullable|mimes:jpeg,png,jpg',
			'car2'          => 'nullable|mimes:jpeg,png,jpg',
			'car3'          => 'nullable|mimes:jpeg,png,jpg',
			'email'         => 'required|unique:deliveries,email,'.$request->input('id').',delivery_id',
			'phone'         => 'required|unique:deliveries,phone,'.$request->input('id').',delivery_id'
		]);

		if($validator->fails()){
			return redirect()->back()->withInput()->withErrors($validator->errors());
		}

		if($request->hasFile('identity')){
			$identity = $this->uploadImage($request->file('identity'), 'deliveryImages', 'delivery');
            if(!$identity){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $identity = "";
        }

        if($request->hasFile('license')){
			$license = $this->uploadImage($request->file('license'), 'deliveryImages', 'delivery');
            if(!$license){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $license = "";
        }

        if($request->hasFile('insurance')){
			$insurance = $this->uploadImage($request->file('insurance'), 'deliveryImages', 'delivery');
            if(!$insurance){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $insurance = "";
        }

        if($request->hasFile('authorization')){
			$authorization = $this->uploadImage($request->fiel('authorization'), 'deliveryImages', 'delivery');
            if(!$authorization){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $authorization = "";
        }

        if($request->hasFile('car1')){
			$car1 = $this->uploadImage($request->file('car1'), 'deliveryImages', 'delivery');
            if(!$car1){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $car1 = "";
        }

        if($request->hasFile('car2')){
			$car2 = $this->uploadImage($request->file('car2'), 'deliveryImages', 'delivery');
            if(!$car2){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $car2 = "";
        }

        if($request->hasFile('car3')){
			$car3 = $this->uploadImage($request->file('car3'), 'deliveryImages', 'delivery');
            if(!$car3){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $car3 = "";
        }
        $data['firstname']    		  = $request->input('fname');
		$data['secondname']   		  = $request->input('sname');
		$data['thirdname']    		  = $request->input('tname');
		$data['lastname']     		  = $request->input('lname');
		$data['country_id']   		  = $request->input('countries');
		$data['city_id']      		  = $request->input('cities');
		$data['email']        		  = $request->input('email');
		$data['phone']        		  = $request->input('phone');
		$data['country_code'] 		  = $request->input('country_code');
		if(!empty($request->input('pass')) && $request->input('pass') != ""){
			$data['password']     		  = md5($request->input('pass'));
		}
		
		$data['car_type']     		  = $request->input('car_type');
		$data['full_name']    		  = $data['firstname']." ".$data['secondname']." ".$data['thirdname']." ".$data['lastname'];
		if($car1 != ""){
			$data['car_img1'] = $car1;
		}
		
		if($car2 != ""){
			$data['car_img2'] = $car2;
		}

		if($car3 != ""){
			$data['car_img3'] = $car3;
		}

		if($license != ""){
			$data['license_img'] = $license;
		}
		
		if($authorization != ""){
			$data['authorization_img'] = $authorization;
		}

		if($insurance != ""){
			$data['insurance_img'] = $insurance;
		}

		if($identity != ""){
			$data['id_img'] = $identity;
		}
		$id = $request->input('id');
		try {
			DB::transaction(function() use ($data, $id){
				DB::table('deliveries')->where('delivery_id', $id)->update($data);
			});
			$request->session()->flash('success', 'Delivery updated successfully');
			return redirect()->route('deliveries.show');
		} catch (Exception $e) {
			$errors = array('Failed to update, please try again later');
			return redirect()->back()->withInput()->withErrors($errors);
		}
	}

	// function to activate delivery
    public function activateDelivery($id , Request $request){

	    $delivery_info =  DB::table("deliveries")
                            ->where("delivery_id" , $id)
                            ->select("status")
                            ->first();
	    if($delivery_info->status == 0 || $delivery_info->status == "0"){
	        $update = 3;
        }else{
            $update = 1;
        }
        date_default_timezone_set('Asia/Riyadh');
        $timestamp =  date("Y/m/d H:i:s", time());
        DB::table("deliveries")
            ->where("delivery_id" , $id)
            ->update(["status" => $update, "admin_activation_time" => $timestamp]);
        $request->session()->flash('success', 'Delivery has been activated successfully');
        return redirect()->back()->withInput();
    }
}
