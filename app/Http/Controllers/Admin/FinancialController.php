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

class FinancialController extends Controller
{
	public function getRequestsFilter(){
		$table = "(SELECT provider_id AS id, full_name, phone, country_code, 'provider' AS type FROM providers";
		$table .= " UNION ";
		$table .= "SELECT user_id AS id, full_name, phone, country_code, 'user' AS type FROM users";
		$table .= " UNION ";
		$table .= "SELECT delivery_id AS id, full_name, phone, country_code, 'delivery' AS type FROM deliveries) AS person";
		$requests = DB::table('withdraw_balance')
					  ->orderBy('withdraw_balance.id', 'DESC')
					  ->join(DB::raw($table), function($join){
					  		$join->on('withdraw_balance.actor_id', '=', 'person.id');
					  		$join->on('withdraw_balance.type', '=', 'person.type');
					  })
					  ->select('withdraw_balance.*', 'person.full_name', 'person.country_code', 'person.phone AS person_phone')
					  ->get();
		return view('cpanel.financial.requests', compact('requests'));
	}

	public function requestsSearch(Request $request){
		$table = "(SELECT provider_id AS id, full_name, phone, country_code, 'provider' AS type FROM providers";
		$table .= " UNION ";
		$table .= "SELECT user_id AS id, full_name, phone, country_code, 'user' AS type FROM users";
		$table .= " UNION ";
		$table .= "SELECT delivery_id AS id, full_name, phone, country_code, 'delivery' AS type FROM deliveries) AS person";
		$conditions = array();
		if(!is_null($request->input('from')) && !is_null($request->input('to'))){
			$conditions[] = [DB::raw('withdraw_balance.created_at'), '>=', $request->input('from')];
			$conditions[] = [DB::raw('withdraw_balance.created_at'), '<=', $request->input('to')];
		}

		if(!is_null($request->input('name'))){
			$conditions[] = ['person.full_name', 'like', '%'.$request->input('name').'%'];
		}

		if(!is_null($request->input('status'))){
			$conditions[] = ['withdraw_balance.status', '=', $request->input('status')];
		}

		if(!is_null($request->input('job'))){
			$conditions[] = ['withdraw_balance.type', '=', $request->input('job')];
		}

		if(!is_null($request->input('phone'))){
			$phone = $request->input('phone');
			if(!empty($conditions)){
				$requests = DB::table('withdraw_balance')
							  ->where($conditions)
							  ->where(function($query) use ($phone){
							  	 $query->where('person.phone', '=', $phone);
							  	 $query->orWhere(DB::raw('CONCAT(person.country_code,person.phone)'), '=', $phone);
							  })
							  ->orderBy('withdraw_balance.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('withdraw_balance.actor_id', '=', 'person.id');
							  		$join->on('withdraw_balance.type', '=', 'person.type');
							  })
							  ->select('withdraw_balance.*', 'person.full_name', 'person.country_code', 'person.phone AS person_phone')
							  ->get();
			}else{
				$requests = DB::table('withdraw_balance')
							  ->where('person.phone', '=', $phone)
							  ->orWhere(DB::raw('CONCAT(person.country_code,person.phone)'), '=', $phone)
							  ->orderBy('withdraw_balance.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('withdraw_balance.actor_id', '=', 'person.id');
							  		$join->on('withdraw_balance.type', '=', 'person.type');
							  })
							  ->select('withdraw_balance.*', 'person.full_name', 'person.country_code', 'person.phone AS person_phone')
							  ->get();
			}
		}else{
			if(!empty($conditions)){
				$requests = DB::table('withdraw_balance')
							  ->where($conditions)
							  ->orderBy('withdraw_balance.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('withdraw_balance.actor_id', '=', 'person.id');
							  		$join->on('withdraw_balance.type', '=', 'person.type');
							  })
							  ->select('withdraw_balance.*', 'person.full_name', 'person.country_code', 'person.phone AS person_phone')
							  ->get();
			}else{
				$requests = DB::table('withdraw_balance')
							  ->orderBy('withdraw_balance.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('withdraw_balance.actor_id', '=', 'person.id');
							  		$join->on('withdraw_balance.type', '=', 'person.type');
							  })
							  ->select('withdraw_balance.*', 'person.full_name', 'person.country_code', 'person.phone AS person_phone')
							  ->get();
			}
		}
		$data = '';
		if(!empty($requests)){
			foreach($requests AS $request){
				$total = $request->current_balance - $request->due_balance - $request->forbidden;
				if($request->status == 1){
					$status = 'Pending';
					$action = '<a href="http://zad.al-yasser.info/public/zad-cpanel/financialPanel/execute_withdraw/'.$request->id.'" class="custom-btn blue-bc">
			                    <i class="fa fa-return"></i>تنفيذ
			                  </a>';
				}else{
					$status = 'Done';
					$action = 'Done';
				}
				$data .= '<tr>
                                <td>'.$request->full_name.'</td>
                                <td>'.$request->country_code.$request->phone.'</td>
                                <td>'.$request->due_balance.'</td>
                                <td>'.$request->current_balance.'</td>
                                <td>'.$request->forbidden.'</td>
                                <td>'.$total.'</td>
                                <td>'.((is_null($request->name))? "" : $request->name).'</td>
                                <td>'.((is_null($request->bank_name))? "" : $request->bank_name).'</td>
                                <td>'.((is_null($request->account_num))? "" : $request->account_num).'</td>
                                <td>'.((is_null($request->phone))? "" : $request->phone).'</td>
                                <td>'.$request->type.'</td>
                                <td>'.$status.'</td>
                                <td>'.$action.'</td>
                          </tr>';
			}
		}

		echo $data;
	}

	public function executeWithdraw($id, Request $request){
		$requestData = DB::table('withdraw_balance')->where('id', $id)->first();
		if(!is_null($requestData)){
			$current = $requestData->current_balance;
			$due     = $requestData->due_balance;
			$forbidden = $requestData->forbidden;
			$actor   = $requestData->actor_id;
			$type    = $requestData->type;
			try {
				DB::transaction(function() use ($current, $due, $actor, $type, $id, $forbidden){
					DB::table('withdraw_balance')->where('id', $id)->update(['status' => 2]);
					if($type != 'user'){
						DB::table('balances')->where('actor_id', $actor)
											 ->where('type', $type)
											 ->update([
											     'current_balance'    => DB::raw('current_balance - '.$current),
                                                 'due_balance'        => DB::raw('due_balance - '.$due),
                                                 'forbidden_balance'  => DB::raw('forbidden_balance - '.$forbidden)
											 ]);
					}else{
						DB::table('users')->where('user_id', $id)->update(['points' => DB::raw('points - '.$current)]);
					}

				});
				$request->session()->flash('success', 'Process done successfully');
				if($current >= $due){
					$kind = 'سند صرف';
					$value = $current - ($due + $forbidden);
				}else{
					$kind = 'سند قبض';
					$value = ($due + $forbidden) - $current;
				}

				if($type == "provider"){
					$data = DB::table('providers')->where('provider_id', $actor)->select('full_name')->first();
				}elseif($type == "delivery"){
					$data = DB::table('deliveries')->where('delivery_id', $actor)->select('full_name')->first();
				}elseif($type == 'user'){
					$data = DB::table('users')->where('user_id', $actor)->select('full_name')->first();
				}else{
					$data = DB::table('marketers')->where('marketer_id', $actor)->select('full_name')->first();
				}

				if($data != NULL){
					$name = $data->full_name;
				}else{
					$name = "";
				}
				return redirect()->route('print', ['name' => $name, 'value' => $value, 'kind' => $kind]);
			} catch (Exception $e) {
				$request->session()->flash('err', 'Process failed');
				return redirect()->back();
			}
		}else{
			$request->session()->flash('err', 'Process failed');
			return redirect()->back();
		}
	}

	public function getTodayRequests(){
		$table = "(SELECT provider_id AS id, full_name, phone, country_code, 'provider' AS type FROM providers";
		$table .= " UNION ";
		$table .= "SELECT user_id AS id, full_name, phone, country_code, 'user' AS type FROM users";
		$table .= " UNION ";
		$table .= "SELECT delivery_id AS id, full_name, phone, country_code, 'delivery' AS type FROM deliveries) AS person";
		$requests = DB::table('withdraw_balance')
					  ->where(DB::raw('DATE(withdraw_balance.created_at)'), date('Y-m-d', time()))
					  ->orderBy('withdraw_balance.id', 'DESC')
					  ->join(DB::raw($table), function($join){
					  		$join->on('withdraw_balance.actor_id', '=', 'person.id');
					  		$join->on('withdraw_balance.type', '=', 'person.type');
					  })
					  ->select('withdraw_balance.*', 'person.full_name', 'person.country_code', 'person.phone')
					  ->get();
		return view('cpanel.financial.today', compact('requests'));
	}

	public function getbalancesFilter(){
		$table = "(SELECT provider_id AS id, full_name, phone, country_code, 'provider' AS type FROM providers";
		$table .= " UNION ";
		$table .= "SELECT delivery_id AS id, full_name, phone, country_code, 'delivery' AS type FROM deliveries) AS person";
		$balances = DB::table('balances')
					  ->orderBy('balances.id', 'DESC')
					  ->join(DB::raw($table), function($join){
					  		$join->on('balances.actor_id', '=', 'person.id');
					  		$join->on('balances.type', '=', 'person.type');
					  })
					  ->select('balances.*', 'person.full_name', 'person.country_code', 'person.phone')
					  ->get();
		return view('cpanel.financial.balances', compact('balances'));
	}

	public function balancesSearch(Request $request){
		$table = "(SELECT provider_id AS id, full_name, phone, country_code, 'provider' AS type FROM providers";
		$table .= " UNION ";
		$table .= "SELECT delivery_id AS id, full_name, phone, country_code, 'delivery' AS type FROM deliveries) AS person";
		$conditions = array();

		if(!is_null($request->input('name'))){
			$conditions[] = ['person.full_name', 'like', '%'.$request->input('name').'%'];
		}

		if(!is_null($request->input('job'))){
			$conditions[] = ['balances.type', '=', $request->input('job')];
		}

		if(!is_null($request->input('phone'))){
			$phone = $request->input('phone');
			if(!empty($conditions)){
				$balances = DB::table('balances')
							  ->where($conditions)
							  ->where(function($query) use ($phone){
							  	 $query->where('person.phone', '=', $phone);
							  	 $query->orWhere(DB::raw('CONCAT(person.country_code,person.phone)'), '=', $phone);
							  })
							  ->orderBy('balances.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('balances.actor_id', '=', 'person.id');
							  		$join->on('balances.type', '=', 'person.type');
							  })
							  ->select('balances.*', 'person.full_name', 'person.country_code', 'person.phone')
							  ->get();
			}else{
				$balances = DB::table('balances')
							  ->where('person.phone', '=', $phone)
							  ->orWhere(DB::raw('CONCAT(person.country_code,person.phone)'), '=', $phone)
							  ->orderBy('balances.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('balances.actor_id', '=', 'person.id');
							  		$join->on('balances.type', '=', 'person.type');
							  })
							  ->select('balances.*', 'person.full_name', 'person.country_code', 'person.phone')
							  ->get();
			}
		}else{
			if(!empty($conditions)){
				$balances = DB::table('balances')
							  ->where($conditions)
							  ->orderBy('balances.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('balances.actor_id', '=', 'person.id');
							  		$join->on('balances.type', '=', 'person.type');
							  })
							  ->select('balances.*', 'person.full_name', 'person.country_code', 'person.phone')
							  ->get();
			}else{
				$balances = DB::table('balances')
							  ->orderBy('balances.id', 'DESC')
							  ->join(DB::raw($table), function($join){
							  		$join->on('balances.actor_id', '=', 'person.id');
							  		$join->on('balances.type', '=', 'person.type');
							  })
							  ->select('balances.*', 'person.full_name', 'person.country_code', 'person.phone')
							  ->get();
			}
		}
		$data = '';
		if(!empty($balances)){
			foreach($balances AS $request){
				$total = $request->current_balance - $request->due_balance;
				$data .= '<tr>
                                <td>'.$request->full_name.'</td>
                                <td>'.$request->country_code.$request->phone.'</td>
                                <td>'.$request->due_balance.'</td>
                                <td>'.$request->current_balance.'</td>
                                <td>'.$total.'</td>
                                <td>'.$request->type.'</td>
                          </tr>';
			}
		}

		echo $data;
	}

	public function getAppIncome(){
		$getTotalIncome = DB::table('orders_headers')->where('status_id', 4)
													 ->select(DB::raw('(SUM(app_value) + SUM(delivery_app_value)) AS total'))
													 ->first();
		$getDetails     = DB::table('orders_headers')->where('status_id', 4)
													 ->select('order_id', 'order_code', 'total_value', 'app_value', 'delivery_app_value', 'marketer_value', 'marketer_delivery_value','net_value', 'delivery_price', 'total_qty', 'balance_status')
													 ->get();
		return view('cpanel.financial.appincome', compact('getTotalIncome', 'getDetails'));
	}

	public function searchAppIncome(Request $request){
		$from = $request->input('from');
		$to   = $request->input('to');

		if(!is_null($from) && !is_null($to)){
			$getTotalIncome = DB::table('orders_headers')->where('status_id', 4)
														 ->where(DB::raw('DATE(created_at)'), '>=', $from)
														 ->where(DB::raw('DATE(created_at)'), '<=', $to)
														 ->select(DB::raw('(SUM(app_value) + SUM(delivery_app_value)) AS total'))
														 ->first();
			if($getTotalIncome != NULL){
				$total = $getTotalIncome->total;
			}else{
				$total = 0;
			}
			$getDetails     = DB::table('orders_headers')->where('status_id', 4)
														 ->where(DB::raw('DATE(created_at)'), '>=', $from)
														 ->where(DB::raw('DATE(created_at)'), '<=', $to)
														 ->select('order_id', 'order_code', 'total_value', 'app_value', 'delivery_app_value', 'marketer_value', 'marketer_delivery_value','net_value', 'delivery_price', 'total_qty', 'balance_status')
														 ->get();
			$data = '';
			if(!empty($getDetails)){
				foreach($getDetails AS $row){
					$data .= '<tr>
                                <td>'.$row->order_code.'</td>
                                <td>'.$row->total_value.'</td>
                                <td>'.$row->net_value.'</td>
                                <td>'.$row->app_value.'</td>
                                <td>'.$row->delivery_price.'</td>
                                <td>'.$row->delivery_app_value.'</td>
                                <td>'.$row->marketer_value.'</td>
                                <td>'.$row->marketer_delivery_value.'</td>
                                <td>'.(($row->balance_status == 1)? 'Pending' : 'Done').'</td>
                            </tr>';
				}
			}
		}else{
			$data  = '';
			$total = 0;
		}

		return response()->json(['total'=>ROUND($total,2), 'data'=>$data]);
	}

	public function getInvoices(){
		$invoices = DB::table('invoices')->get();
		return view('cpanel.financial.invoices', compact('invoices'));
	}

	public function searchInvoices(Request $request){
		$from  = $request->input('from');
		$to    = $request->input('to');
		$type  = $request->input('type');
		$name  = $request->input('name');
		$phone = $request->input('phone');

		$conditions = array();
		if(!is_null($from) && !is_null($to)){
			$conditions[] = [DB::raw('DATE(created_at)'), '>=', $from];
			$conditions[] = [DB::raw('DATE(created_at)'), '<=', $to];
		}

		if(!is_null($type)){
			$conditions[] = ['type', '=', $type];
		}

		if(!is_null($name)){
			$conditions[] = ['name', '=', $name];
		}

		if(!is_null($phone)){
			$conditions[] = ['phone', '=', $phone];
		}
		if(!empty($conditions)){
			$invoices = DB::table('invoices')->where($conditions)
										 	 ->get();
		}else{
			$invoices = DB::table('invoices')->get();
		}
		$data = '';
		if(!empty($invoices)){
			foreach($invoices AS $invoice){
				$data .= '<tr>
                            <td>'.$invoice->invo_id.'</td>
                            <td>'.$invoice->name.'</td>
                            <td>'.$invoice->phone.'</td>
                            <td>'.$invoice->value.'</td>
                            <td>'.(($invoice->type == 1)? 'Sale' : 'Return').'</td>
                            <td>'.$invoice->invo_desc.'</td>
                            <td>'.date('Y-m-d', strtotime($invoice->created_at)).'</td>
                        </tr>';
			}
		}

		echo $data;
	}

	public function createInvoice(){
		return view('cpanel.financial.create_invoice');
	}

	public function storeInvoice(Request $request){
		$validator = Validator::make($request->all(), [
			'name'  => 'required',
			'phone' => 'required', 
			'value' => 'required|numeric',
			'desc'  => 'required',
			'type'  => 'required|in:1,2'
		]);

		if($validator->fails()){
			$errors = $validator->errors();
			return redirect()->back()->withErrors($errors)->withInput();
		}

		$insert = DB::table('invoices')->insert([
			'name' 		=> $request->input('name'),
			'phone' 	=> $request->input('phone'),
			'value' 	=> $request->input('value'),
			'invo_desc' => $request->input('desc'),
			'type'      => $request->input('type')
		]);

		if($insert){
			$request->session()->flash('success', 'Invoice created successfully');
			return redirect()->route('invoices.filter');
		}else{
			$errors = array('Failed to create the invoice');
			return redirect()->back()->withErrors($errors);
		}
	}

	public function getReceipt(){
		return view('cpanel.financial.receipts');
	}
}
