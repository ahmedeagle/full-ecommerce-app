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
use App\Marketers;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

class MarketerController extends Controller
{
	public function __construct(){
		
	}

	public function show(){
		$marketers = Marketers::where('marketers.publish', 1)
							  ->join('city', 'marketers.city_id', '=','city.city_id')
							  ->join('country', 'marketers.country_id', '=','country.country_id')
							  ->select('marketers.*', DB::raw('country.country_ar_name AS country'), DB::raw('city.city_ar_name AS city'))
							  ->get();
		return view('cpanel.marketers.marketers', compact('marketers'));
	}

	public function create(){
		//get countries
		$countries = DB::table('country')->where('publish', 1)->get();
		//get categories
		$categories = DB::table('categories')->where('publish', 1)->get();
		return view('cpanel.marketers.create', compact('countries', 'categories'));
	}
	public function randomString($length = 6) {
		$str = "";
		$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}
	public function store(Request $request){
		$validator = Validator::make($request->all(),[
			'phone' => 'required|unique:providers',
			'email' => 'required|email|unique:providers'
		]);

		if($validator->fails()){
			return redirect()->back()->with('errors', $validator->errors())->withInput();
		}else{
			$full_name = $request->input('fname')." ".$request->input('sname')." ".$request->input('tname')." ".$request->input('lname');
			// $marketer_code = $this->randomString(8);
			$id = Marketers::insertGetId([
				'full_name'    => $full_name,
				'first_name'   => $request->input('fname'),
				'second_name'  => $request->input('sname'),
				'third_name'   => $request->input('tname'),
				'last_name'    => $request->input('lname'),
				'country_code' => $request->input('country_code'),
				'phone' 	   => $request->input('phone'),
				'email'        => $request->input('email'),
				'profile_pic'  => url('admin-assets/images/avatar_ic.png'),
				'country_id'   => $request->input('countries'),
				'city_id'      => $request->input('cities'),
				'address'      => $request->input('address'),
				'marketer_code' => str_random(7),
				'status'       => 0,
				'password'     => md5($request->input('password'))
			]);

			$request->session()->flash('success', 'Marketer added successfully');
			return redirect()->route('marketers.show');
		}
	}

	public function edit($id){
		//get Provider data 
		$marketer   = Marketers::where('marketer_id', $id)->first();
		$city_id    = $marketer->city_id;
		$country_id = $marketer->country_id;

		//get countries
		$countries = DB::table('country')->get();

		//get country cities
		$cities = DB::table('city')->where('country_id', $country_id)->get();

		return view('cpanel.marketers.edit', compact('marketer', 'cities', 'countries', 'city_id', 'country_id'));
	}

	public function update(Request $request){
		$validator = Validator::make($request->all(),[
			'phone' => 'required|unique:marketers,phone,'.$request->input('marketer_id').',marketer_id',
			'email' => 'required|email|unique:marketers,email,'.$request->input('marketer_id').',marketer_id'
		]);

		if($validator->fails()){
			return redirect()->back()->with('errors', $validator->errors())->withInput();
		}else{
			$full_name = $request->input('fname')." ".$request->input('sname')." ".$request->input('tname')." ".$request->input('lname');
			if(empty($request->input('upassword'))){
				$update = Marketers::where('marketer_id', $request->input('marketer_id'))->update([
					'full_name'    => $full_name,
					'first_name'   => $request->input('fname'),
					'second_name'  => $request->input('sname'),
					'third_name'   => $request->input('tname'),
					'last_name'    => $request->input('lname'),
					'country_code' => $request->input('country_code'),
					'phone' 	   => $request->input('phone'),
					'email'        => $request->input('email'),
					'country_id'   => $request->input('countries'),
					'city_id'      => $request->input('cities'),
					'address'      => $request->input('address')
				]);
			}else{
				$update = Marketers::where('marketer_id', $request->input('marketer_id'))->update([
					'full_name'    => $full_name,
					'first_name'   => $request->input('fname'),
					'second_name'  => $request->input('sname'),
					'third_name'   => $request->input('tname'),
					'last_name'    => $request->input('lname'),
					'country_code' => $request->input('country_code'),
					'phone' 	   => $request->input('phone'),
					'email'        => $request->input('email'),
					'country_id'   => $request->input('countries'),
					'city_id'      => $request->input('cities'),
					'address'      => $request->input('address'),
					'password'     => md5($request->input('upassword'))
				]);
			}

			
			$request->session()->flash('success', 'Marketer updated successfully');
			return redirect()->route('marketers.show');
		}
	}


	public function getMarketerIncomeView(){
		$marketers = Marketers::get();
		return view('cpanel.marketers.income', compact('marketers'));
	}

	public function incomeSearch(Request $request){
	    
        $conditions[] = ['orders_headers.status_id' , '=', 4];
        if(!empty($request->input('from')) && !empty($request->input('to'))){
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '>=', $request->input('from')];
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '<=', $request->input('to')];
        }

        $code = $request->input('code');
	    $result = DB::table('orders_headers')->where(function ($q) use ($code){
											    	$q->where('provider_marketer_code', $code)
											    	  ->orWhere('delivery_marketer_code', $code);
												})
										    ->where($conditions)
										    ->join('order_details', 'orders_headers.order_id', '=', 'order_details.order_id')
										    ->select(
										    	'orders_headers.order_code', 
										   		'orders_headers.total_qty', 
										   		'orders_headers.total_value', 
										   		'orders_headers.order_id AS invo_no',
										   		DB::raw('(orders_headers.marketer_value + orders_headers.marketer_delivery_value) AS credit'), 
										   		'marketer_value',
										   		'marketer_delivery_value',
										   		DB::raw('COUNT(order_details.meal_id) AS mealsCount'), 
										   		'orders_headers.order_id',
										   		'orders_headers.balance_status'
										    )
										    ->groupBy('orders_headers.order_id')
										    ->get();

	    $total = 0;
	    $data  = '';
	   	if(!empty($result)){
	   		foreach($result AS $row){
	   			$total += $row->credit;
	   			$data .= '<tr>
                                <td>'.$row->invo_no.'</td>
                                <td>'.$row->order_code.'</td>
                                <td>'.ROUND($row->total_value,2).'</td>
                                <td>'.ROUND($row->credit,2).'</td>
                                <td>'.ROUND($row->marketer_value,2).'</td>
                                <td>'.ROUND($row->marketer_delivery_value,2).'</td>
                                <td>'.(($row->balance_status == 1)? 'Pending' : 'Done').'</td>
                            </tr>';
	   		}
	   	}

	   	return response()->json(['data'=>$data, 'total'=>round($total,2)]);
	}
}
