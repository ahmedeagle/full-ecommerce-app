<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
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

class Mycron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:timer';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
 
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
         $this -> cron_job();
        
    }
    
     public function cron_job(){

        date_default_timezone_set('Asia/Riyadh');
        $now =  date("H:i:s", time());
        $now = strtotime($now);
        //$now = time();

        //first reset meals quantaties
        $this->resetMealQty($now);

        //second refuse all dismissed orders
        $this->refuseMissedOrders($now);

        //send notification
        $this->provider_evaluation_notification($now);

        Log::debug("crone: Done");
    }

    public function reset_meal_qty_crone(){
        date_default_timezone_set('Asia/Riyadh');
        $now =  date("H:i:s", time());
        $now = strtotime($now);
        //$now = time();
        $this->resetMealQty($now);
    }

    public function refuse_missed_orders_crone(){
        date_default_timezone_set('Asia/Riyadh');
        $now =  date("H:i:s", time());
        $now = strtotime($now);
        //$now = time();
        $this->refuseMissedOrders($now);
    }
    protected function resetMealQty($now){
        //get all meals quantaties
        $meals = Meals::join('providers', 'meals.provider_id', '=', 'providers.provider_id')
            ->select('meals.*', 'providers.allowed_to_time')->get();
        if($meals->count()){
            foreach($meals AS $meal){
                if(strtotime($meal->allowed_to_time) <= $now){
                    Meals::where('meal_id', $meal->meal_id)->update(['avail_number' => $meal->allowed_number]);
                }
            }
        }
    }

    protected function refuseMissedOrders($now){
        //get allowed time in min
        $settings = DB::table('app_settings')->first();
        if($settings != NULL){
            $time_in_min = $settings->time_in_min;
        }else{
            $time_in_min = 15;
        }
        //get all new orders
        $date   = date('Y-m-d', strtotime("-1 days"));
        $orders = DB::table('orders_headers')->where('status_id', 1)
            ->where(DB::raw('DATE(created_at)'), ">=", $date)
            ->select(DB::raw('TIME(created_at) AS created_time'), 'order_id', 'user_id', 'payment_type', 'total_value')
            ->get();

        if($orders->count()){
            foreach($orders AS $order){
                 
                $created_at = strtotime($order->created_time);
                $diff = round(abs($now - $created_at) / 60,2);
                if($diff >= $time_in_min){
                    DB::table("orders_headers")->where('order_id', $order->order_id)->update(['status_id' => 7]);
                    if($order->payment_type != 1 && $order->payment_type != "1"){
                        User::where('user_id', $order->user_id)->update([
                            'points' => DB::raw('points + '.$order->total_value)
                        ]);
                    }
                }
            }
        }
    }

    public function provider_evaluation_notification($now){
        //get data
        $orders = DB::table('provider_evaluation')->select('order_id')->get();
        $data = DB::table('orders_headers')
            ->where('orders_headers.status_id', 4)->where(DB::raw($now), '<=', DB::raw('delivered_at + INTERVAL 1 MINUTE'))
            ->whereNotIn('orders_headers.order_id', $orders)
            ->join('users', 'orders_headers.user_id', '=', 'users.user_id')
            ->select('users.device_reg_id', 'orders_headers.order_id')
            ->get();
        if($data->count()){
            foreach($data AS $row){
                $notif_data = array();
                $notif_data['title']      = 'تقييم مقدم الخدمة';
                $notif_data['message']    = 'من فضلك قم بتقييم مقدم الخدمة';
                $notif_data['order_id']   = $row->order_id;
                $notif_data['notif_type'] = 'provider_evaluate';
                $push_notif 			  = $this->singleSend($row->device_reg_id, $notif_data, $this->user_key);
            }
        }
    }

    // function to change the order status to be not-responded if the time exceed 30 min from Processing
    public function prepare_limit(){
        date_default_timezone_set('Asia/Riyadh');
        $now =  date("H:i:s", time());
        $now = strtotime($now);
        //$now = time();
        return $this->change_status_30_min($now);
    }

    protected function change_status_30_min($now){

        //get allowed time in min
        $settings = DB::table('app_settings')->first();
        if($settings != NULL){
            $max_time_to_process_order = $settings->max_time_to_process_order;
        }else{
            $max_time_to_process_order = 30;
        }
        $date   = date('Y-m-d', strtotime("-1 days"));
        $orders = DB::table('orders_headers')->where('status_id',"!=" , 4)
            ->where(DB::raw('DATE(created_at)'), ">=", $date)
            ->select(DB::raw('TIME(expected_delivery_time) AS created_time'), 'order_id', 'user_id', 'payment_type', 'total_value')
            ->get();
        if($orders->count()){
            foreach($orders AS $order){
                $created_at = strtotime($order->created_time);
                $diff = round(abs($now - $created_at) / 60,2);
                if($diff >= $max_time_to_process_order){
                    DB::table("orders_headers")
                        ->where('order_id', $order->order_id)
                        ->update(['status_id' => 7]);
                    if($order->payment_type != 1 && $order->payment_type != "1"){
                        User::where('user_id', $order->user_id)->update([
                            'points' => DB::raw('points + '.$order->total_value)
                        ]);
                    }
                    // get user info
                    $user_info = DB::table("orders_headers")
                        ->where("order_id" , $order->order_id)
                        ->join("users" , "users.user_id" , "orders_headers.user_id")
                        ->select("device_reg_id")
                        ->first();

                    // get provider data
                    $provider_info = DB::table("orders_headers")
                        ->where("order_id" , $order->order_id)
                        ->join("providers" , "providers.provider_id" , "orders_headers.provider_id")
                        ->select("device_reg_id")
                        ->first();
                    //send notification to the provider
                    $notif_data = array();
                    $notif_data['title']      = 'الاختار بحالة الطلب';
                    $notif_data['message']    = 'لقد تم تحويل هذا الطلب الى الطلبات التى لم يرد عليها حيث تجاوزت عملية التسلم المدة السموح بها';
                    $notif_data['order_id']   = $order->order_id;
                    $notif_data['notif_type'] = 'order_change_status';
                    $push_provider_notif 			  = $this->singleSend($provider_info->device_reg_id, $notif_data, $this->provider_key);

                    // send notification to user
                    $notif_data = array();
                    $notif_data['title']      = 'الرد على الطلب المرسل';
                    $notif_data['message']    = 'لم يتم الرد على طلبك من مقدم الخدمة خلال المدة المسموح بها برجاء اضافة طلب اخر';
                    $notif_data['order_id']   = $order->order_id;
                    $notif_data['notif_type'] = 'order_change_status';
                    $push_user_notif 			  = $this->singleSend($user_info->device_reg_id, $notif_data, $this->user_key);

//                    if($order->payment_type != 1 && $order->payment_type != "1"){
//                        User::where('user_id', $order->user_id)->update([
//                            'points' => DB::raw('points + '.$order->total_value)
//                        ]);
//                    }
                }
            }
        }
    }

    /* function to change the status of the order to be proccessing if
      the delivery exceed the limit
     to accept or reject the order
    */
    public function delivery_accept_limit(Request $request){
        date_default_timezone_set('Asia/Riyadh');
        $now =  date("H:i:s", time());
        $now = strtotime($now);
        //$now = time();
        return $this->change_status_delivery_limit($now);
    }

    protected function change_status_delivery_limit($now){


        //get allowed time in min for delivery to accept or reject
        $settings = DB::table('app_settings')->first();
        if($settings != NULL){
            $max_time_to_accept_order = $settings->max_time_to_accept_order;
        }else{
            $max_time_to_accept_order = 15;
        }

        $date   = date('Y-m-d', strtotime("-1 days"));
        $orders = DB::table('orders_headers')
            ->where('status_id', 3)
            ->where(DB::raw('DATE(transfer_to_delivery_at)'), ">=", $date)
            ->select(DB::raw('TIME(transfer_to_delivery_at) AS transfer_delivery') , "order_id")
            ->get();

        if($orders->count()){

            foreach($orders AS $order){
                $transfer_delivery = strtotime($order->transfer_delivery);
                $date   = date('Y-m-d', strtotime("-1 days"));
                $diff = round(abs($now - $transfer_delivery) / 60,2);
                if($diff >= $max_time_to_accept_order){


                    DB::table("orders_headers")
                        ->where('order_id', $order->order_id)
                        ->update(['status_id' => 2]);

                    // get user info
                    $delivery_info = DB::table("orders_headers")
                        ->where("order_id" , $order->order_id)
                        ->join("deliveries" , "deliveries.delivery_id" , "orders_headers.delivery_id")
                        ->select("device_reg_id")
                        ->first();

                    // get provider data
                    $provider_info = DB::table("orders_headers")
                        ->where("order_id" , $order->order_id)
                        ->join("providers" , "providers.provider_id" , "orders_headers.provider_id")
                        ->select("device_reg_id")
                        ->first();

                    //send notification to the provider
                    $notif_data = array();
                    $notif_data['title']      = 'الاختار بحالة الطلب';
                    $notif_data['message']    ='تم تحويل الطلب الى جارى التجهيز حيث تجاوز الموصل المدة المسموحة لقبول او ارفض الطلب برجاء اختيار موصل اخر';
                    $notif_data['order_id']   = $order->order_id;
                    $notif_data['notif_type'] = 'order_change_status';
                    $push_provider_notif 			  = $this->singleSend($provider_info->device_reg_id, $notif_data, $this->provider_key);

                    // send notification to delivery
                    $notif_data = array();
                    $notif_data['title']      = 'الاختار بحالة الطلب';
                    $notif_data['message']    = 'تم ارجاع الطلب الى المقدم حيث انك تجاوزت المدة المسموحة لقبول او رفض الطلب';
                    $notif_data['order_id']   = $order->order_id;
                    $notif_data['notif_type'] = 'order_change_status';
                    $push_user_notif 			  = $this->singleSend($delivery_info->device_reg_id, $notif_data, $this->delivery_key);

//                    if($order->payment_type != 1 && $order->payment_type != "1"){
//                        User::where('user_id', $order->user_id)->update([
//                            'points' => DB::raw('points + '.$order->total_value)
//                        ]);
//                    }
                }
            }
        }
    }
    
}