<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Providers;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //define firebase_keys
    protected $api_key;
    protected $user_key     = "AAAA7oOKXvM:APA91bG-HKMnpy7QwuUKaPlA5cnC1vL8SS4yIdmfuuXJf_XaY0VqaNZIBaAeKSLINCG4pCFp9ntKfYgCl1dCdo2WhbWYtJxmFTv0rx0OL0-N9Wlx54fCKClLKh5QpmLcsySgqpk2Silu";
    protected $provider_key = "AAAA7GLiwxE:APA91bFPKw3EoQycXVFYho-GknoWCVlpTUoJShiWGxrhxqCIM0J_JhlhV_7-qKOOCAGZTtuA8Ry3DiAUaxtWp7DKSHAwYE_GP-sP117DGuRpuYPKIB06vl26beXTaKWim11uqqEYUBgj";
    protected $delivery_key = "AAAACWiFdaE:APA91bFIKnRGXO4AQCRCg4FQxkFtC4tsPDfYjbzZA8O2PKR9huiVWWFdPj5lIS0D-wyHSbT95MgVv1mhUwd_LOnh7o3xOCRVoMLgitnWNOqxG0XsJ8E7d76g63xFLpSEi_-oTPbwQmZ_";
    protected $push_notif_icon      = '';


    // sending push message to single user by firebase reg id
    protected function send($to, $message) {
        $fields = array(
            'to' => $to,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }
    
    
    /*
        	    $notif_data = array();
				$notif_data['title']      = $push_notif_title;
			    $notif_data['message']    = $push_notif_message;
			    $notif_data['order_id']   = $order_id;
			    $notif_data['notif_type'] = 'order_acceptance';
			    
			     $notif_data['is_background'] = true;
                $notif_data['image']         = $this->push_notif_icon;
                $notif_data['payload']       = $this->setPayload();
                $notif_data['timestamp']     = date('Y-m-d h:i:s');
			    
    
    */
    
    
 
    // Sending message to a topic by topic name
    protected function sendToTopic($to, $message) {
        $fields = array(
            'to' => '/topics/' . $to,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }
 
    // sending push message to multiple users by firebase registration ids
    protected function sendMultiple($registration_ids, $message) {
        $fields = array(
            'to'           => $registration_ids,
            'notification' => $message,
        );
 
        return $this->sendPushNotification($fields);
    }
 
    // function makes curl request to firebase servers
    protected function sendPushNotification($fields) {
        
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $this->api_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        return $result;
        
       
    }

    // public function singleSend($regId, $data){
    //     $res           = array();
    //     $title         = $request->input('title');
    //     $message       = $request->input('message');
    //     $is_background = $request->input('is_background');
    //     $res['data']['title'] = $title;
    //     $res['data']['is_background'] = $is_background;
    //     $res['data']['message'] = $message;
    //     $res['data']['image'] = '';
    //     $res['data']['product_id'] = 123;
    //     $payload = array();
    //     $payload['team'] = '';
    //     $payload['score'] = '';
    //     $res['data']['payload'] = $payload;
    //     $res['data']['timestamp'] = date('Y-m-d h:i:s');
    //     $regId = 'fggO37SVbwg:APA91bHTrZC6NpyD9gFrMPHm4wvf1G14gY4r_MGTezQDm_0zYuc527PnRbP0mq_GVdXz695OJyPRhVRdVQWWvQPWGj1RfyBcBcbCLvJrcmYOsZIFSnwyGT33QDaSGyqcwKGjAqmjCEW5';
    //     $response = $this->send($regId, $res);
    //     return response()->json([$response]);
    // }
    public function setPayload(){
        $payload = array();
        $payload['team'] = '';
        $payload['score'] = '';
        return $payload;
    }
    public function singleSend($regId, $data, $key){
        
          //device id
        $this->api_key = $key;
        
        $notif_data = array();
        foreach($data AS $key => $value){
            $notif_data[$key] = $value;
        }
        
        $notif_data['is_background'] = true;
        $notif_data['image']         = $this->push_notif_icon;
        $notif_data['payload']       = $this->setPayload();
        $notif_data['timestamp']     = date('Y-m-d h:i:s');
        
        $response = $this->send($regId, $notif_data);
        return $response;
    }

    public function multipleSend($regIds, $data, $key){
        $this->api_key = $key;
        $notif_data = array();
        foreach($data AS $key => $value){
            $notif_data[$key] = $value;
        }
        $notif_data['is_background'] = true;
        $notif_data['image']         = $this->push_notif_icon;
        $notif_data['payload']       = $this->setPayload();
        $notif_data['timestamp']     = date('Y-m-d h:i:s');
        $response = $this->sendMultiple($regIds, $data);
        return $response;
    }




function getDistance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return round(($miles * 1.609344));
        } else if ($unit == "N") {
            return round(($miles * 0.8684));
        } else {
            return round($miles);
        }
    }

    //calculating distance between two positions on google map  //depricated
    public function distance($lat1, $lng1, $lat2, $lng2, $miles = true){
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;
     
        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;
     
        return ($miles ? ($km * 0.621371192) : $km);
    }

    public function withdrawRequest($id, $type, $lang, $from_date=NULL, $to_date=NULL){
        if($type == 'provider'){
            $table = 'providers';
            $col   = 'providers.provider_id';
            $name  = 'providers.full_name AS name';
        }elseif($type == 'delivery'){
            $table = 'deliveries';
            $col   = 'deliveries.delivery_id';
            $name  = 'deliveries.full_name AS name';
        }elseif($type == 'user'){
            $table = 'users';
            $col   = 'users.user_id';
            $name  = 'users.full_name AS name';
        }else{
            return false;
        }

        if($lang == "ar"){
            $done = 'تم التسليم';
            $wait = 'بإنتظار الرد';
        }else{
            $done = 'Delivered';
            $wait = 'Pending';
        }

        $conditions[] = ['withdraw_balance.actor_id', '=', $id];
        if(!is_null($from_date)){
            $conditions[] = [DB::raw('DATE(withdraw_balance.created_at)'), '>=', $from_date];
            $conditions[] = [DB::raw('DATE(withdraw_balance.created_at)'), '<=', $to_date];
        }
        $requests = DB::table('withdraw_balance')->where($conditions)
                                                 ->join($table, 'withdraw_balance.actor_id' , '=', $col)
                                                 ->select('withdraw_balance.current_balance', 'withdraw_balance.due_balance', DB::raw('(withdraw_balance.current_balance - withdraw_balance.due_balance) AS credit'), $name, DB::raw('IF(withdraw_balance.status = 2, "'.$done.'", "'.$wait.'") AS `status`'), DB::raw('DATE(withdraw_balance.created_at) AS created'))
                                                 ->get();

        return $requests;
    }

    public function publishing($id, $val, $proccess, $col, $table, Request $request){
        $update = DB::table($table)->where($col, $id)
                  ->update([
                        'publish' => $val
                    ]);

        if($update){
            $request->session()->flash('success', $proccess.' successfully');
            return redirect()->back();
        }else{
            $request->session()->flash('failed', $proccess.' failed');
            return redirect()->back();
        }
    }

    public function getCountryCitites(Request $request){
        $lang    = 'ar';
        $country = $request->input('country_id');
        $flag    = $request->input('flag');
        if($lang == "ar"){
            $col = "city.city_ar_name AS city_name";
        }else{
            $col = "city.city_en_name AS city_name";
        }

        if($country == "" || $country == NULL){
            $select = '<select class="city" name="city">
                            <option value="1">Select City</option>
                        </select>';
            $country_code = "";
        }else{
            $countryData = DB::table('country')->where('country_id', $country)->select('country_code')->first();
            if($countryData != NULL){
                $country_code = $countryData->country_code;
            }else{
                $country_code = '000';
            }
            $cities = DB::table('city')->where('city.country_id', $country)
                                       ->join('country', 'city.country_id', '=', 'country.country_id')
                                       ->select('city.city_id', $col, 'country.country_code')->get();

            $select = '<option value="">إختار مدينة</option>';
            if($cities->count()){
                foreach($cities AS $city){
                    $select .= '<option value="'.$city->city_id.'">'.$city->city_name.'</option>';
                }
            }
        }

        return response()->json(['select' => $select, 'country_code' => $country_code]);
    }

    public function voucher($name, $value, $kind){
        return view('cpanel.print', compact('name', 'value', 'kind'));
    }
 
   
   // ahmed emam 

      public function sendActivationPhone($id)
    {
        $user = User::find($id);
        $code = $this->generate_random_number(4);
        $user->activate_phone_hash = json_encode([
            'code'   => $code,
            'expiry' => Carbon::now()->addDays(1)->timestamp,
        ]);
        $user->save();
        $message = (App()->getLocale() == "en")?
            "Your Activation Code is :- " . $code :
            $code . "رقم الدخول الخاص بك هو :- " ;


        (new SmsController())->send($message , $user->phone);
        //\Illuminate\Support\Facades\Mail::to($member)->send(new AccountConfirmation($member));
    }


    function getRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

    public function generate_random_number($digits){
        return rand(pow(10, $digits-1), pow(10, $digits)-1);
    }

        

        //get actor providers,users,delivery id from request token

      public function get_id(Request $request , $table="users" ,$col="id"){
        
            $actor =  DB::table($table)
                        ->where("token" , $request->input('access_token'))
                        ->select($col)
                        ->first();

              if(!$actor){

                   return 0;
              }
                        
            return $actor -> $col;
        }


    


}
