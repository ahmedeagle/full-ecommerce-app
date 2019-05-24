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

class ComplainsController extends Controller
{
	public function __construct(){
		
	}

	public function getComplains()
	{
		$attaches = array();
		$providers  = Providers::select('full_name', 'provider_id')->get();
		$deliveries = DB::table('deliveries')->select('full_name', 'delivery_id')->get();
		$users      = DB::table('users')->select('full_name', 'user_id')->get();

		$complains  = DB::table('complains')
					  ->leftJoin('users', 'complains.user_id', '=', 'users.user_id')
					  ->leftJoin('providers', 'complains.provider_id', '=', 'providers.provider_id')
					  ->leftJoin('deliveries', 'complains.delivery_id', '=', 'deliveries.delivery_id')
					  ->select(
					  			DB::raw('IFNULL(users.full_name, "") as user'), 
					  			DB::raw('IFNULL(providers.full_name, "") as provider'), 
					  			DB::raw('IFNULL(deliveries.full_name, "") as delivery'), 
					  			'complains.complain',
					  			'complains.order_id',
					  			'complains.attach_no',
					  			'complains.app',
					  			DB::raw('DATE(complains.created_at) AS created')
					  		  )
					  ->orderBy('complains.id', 'DESC')
					  ->paginate(20);
		if($complains->count()){
			foreach($complains AS $complain){
				if(!in_array($complain->attach_no, [0, "0", "", NULL])){
					$attaches[] = DB::table('attachments')->where('attach_id', $complain->attach_no)->get();
				}
			}
		}
		return view('cpanel.complains.complains', compact('providers', 'deliveries', 'users', 'complains', 'attaches'));
	}

	public function search($from, $to, $user, $provider, $delivery, $app, Request $request){
		$conditions = [];
		$attaches = array();
		$providers  = Providers::select('full_name', 'provider_id')->get();
		$deliveries = DB::table('deliveries')->select('full_name', 'delivery_id')->get();
		$users      = DB::table('users')->select('full_name', 'user_id')->get();
		if($from != 'null' && $from != '' && $from != NULL){
			if($to != 'null' && $to != '' && $to != NULL){
				$conditions[] = [DB::raw('DATE(complains.created_at)'), '>=', $from];
				$conditions[] = [DB::raw('DATE(complains.created_at)'), '<=', $to];
			}
		}

		if($user != 'null' && $user != '' && $user != NULL){
			$conditions[] = ['complains.user_id', '=', $user];
		}

		if($provider != 'null' && $provider != '' && $provider != NULL){
			$conditions[] = ['complains.provider_id', '=', $provider];
		}

		if($delivery != 'null' && $delivery != '' && $delivery != NULL){
			$conditions[] = ['complains.delivery_id', '=', $delivery];
		}

		if($app != 'null' && $app != '' && $app != NULL){
			$conditions[] = ['complains.app', '=', $app];
		}
		if(!empty($conditions)){
			$complains  = DB::table('complains')
						  ->where($conditions)
						  ->leftJoin('users', 'complains.user_id', '=', 'users.user_id')
						  ->leftJoin('providers', 'complains.provider_id', '=', 'providers.provider_id')
						  ->leftJoin('deliveries', 'complains.delivery_id', '=', 'deliveries.delivery_id')
						  ->select(
						  			DB::raw('IFNULL(users.full_name, "") as user'), 
						  			DB::raw('IFNULL(providers.full_name, "") as provider'), 
						  			DB::raw('IFNULL(deliveries.full_name, "") as delivery'), 
						  			'complains.complain',
						  			'complains.order_id',
						  			'complains.attach_no',
						  			'complains.app',
						  			DB::raw('DATE(complains.created_at) AS created')
						  		  )
						  ->orderBy('complains.id', 'DESC')
						  ->paginate(20);
		}else{
			$complains  = DB::table('complains')
						  ->leftJoin('users', 'complains.user_id', '=', 'users.user_id')
						  ->leftJoin('providers', 'complains.provider_id', '=', 'providers.provider_id')
						  ->leftJoin('deliveries', 'complains.delivery_id', '=', 'deliveries.delivery_id')
						  ->select(
						  			DB::raw('IFNULL(users.full_name, "") as user'), 
						  			DB::raw('IFNULL(providers.full_name, "") as provider'), 
						  			DB::raw('IFNULL(deliveries.full_name, "") as delivery'), 
						  			'complains.complain',
						  			'complains.order_id',
						  			'complains.attach_no',
						  			'complains.app',
						  			DB::raw('DATE(complains.created_at) AS created')
						  		  )
						  ->orderBy('complains.id', 'DESC')
						  ->paginate(20);
		}
		
		if($complains->count()){
			foreach($complains AS $complain){
				if(!in_array($complain->attach_no, [0, "0", "", NULL])){
					$attaches[] = DB::table('attachments')->where('attach_id', $complain->attach_no)->get();
				}
			}
		}

		return view('cpanel.complains.complains', compact('complains', 'attaches', 'users', 'providers', 'deliveries'));
	}

	public function getTodayComplains()
	{

		$complains  = DB::table('complains')
					  ->where(DB::raw('DATE(complains.created_at)'), date('Y-m-d', time()))
					  ->leftJoin('users', 'complains.user_id', '=', 'users.user_id')
					  ->leftJoin('providers', 'complains.provider_id', '=', 'providers.provider_id')
					  ->leftJoin('deliveries', 'complains.delivery_id', '=', 'deliveries.delivery_id')
					  ->select(
					  			DB::raw('IFNULL(users.full_name, "") as user'), 
					  			DB::raw('IFNULL(providers.full_name, "") as provider'), 
					  			DB::raw('IFNULL(deliveries.full_name, "") as delivery'), 
					  			'complains.complain',
					  			'complains.order_id',
					  			'complains.attach_no',
					  			'complains.app',
					  			DB::raw('DATE(complains.created_at) AS created')
					  		  )
					  ->orderBy('complains.id', 'DESC')
					  ->paginate(20);
		if($complains->count()){
			foreach($complains AS $complain){
				if(!in_array($complain->attach_no, [0, "0", "", NULL])){
					$attaches[] = DB::table('attachments')->where('attach_id', $complain->attach_no)->get();
				}
			}
		}
		return view('cpanel.complains.today', compact('complains', 'attaches'));
	}
	
}
