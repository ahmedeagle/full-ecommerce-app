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

class ProviderController extends Controller
{
	public function __construct(){
		
	}

	public function show(){
		$providers = Providers::where('providers.publish', 1)
							  ->join('city', 'providers.city_id', '=','city.city_id')
							  ->join('country', 'providers.country_id', '=','country.country_id')
							  ->select('providers.*', DB::raw('country.country_en_name AS country'), DB::raw('city.city_en_name AS city'))
							  ->get();
		return view('cpanel.providers.providers', compact('providers'));
	}

	public function create(){
		//get countries
		$countries = DB::table('country')->where('publish', 1)->get();
		//get categories
		$categories = DB::table('categories')->where('publish', 1)->get();
		return view('cpanel.providers.create', compact('countries', 'categories'));
	}

	public function store(Request $request){
	    $messages = [
	        'phone.required' => 'رقم الجوال مطلوب ولا يمكن تركه فارغا',
            'phone.unique'   => 'رقم الجوال مستخدم من قبل',
            'email.required' => 'البريد الإلكترونى مطلوب ولا يمكن تركه فارغا',
            'email.unique'   => 'البريد الإلكترونى مستخدم من قبل',
            'email.email'    => 'خطأ فى صيغة البريد الإلكترونى',
            'maroof_img.mimes'        => 'صورة معروف يجب ان تكون بإمتداتات jpeg, png',
            'health_certific.mimes'   => 'صورة الشهادة الصحية يجب ان تكون بإمتداتات jpeg, png',
            'commercial_record.mimes' => 'صورة السجل التجارى يجب ان تكون بإمتداتات jpeg, png',
            'maroof_date.required_with' => 'تاريخ إنتهاء معروف مطلوب ولا يمكن تركه فارغا',
            'maroof_date.date_format'  => 'تاريخ إنتهاء معروف يجب ان يكون بتنسيق yyyy-mm-dd ',
            'health_certific_date.required_with' => 'تاريخ إنتهاء الشهادة الصحية مطلوب ولا يمكن تركه فارغا',
            'health_certific_date.date_formate'  => 'تاريخ إنتهاء الشهادة الصحية يجب ان يكون بتنسيق yyyy-mm-dd ',
            'commercial_record_date.required_with' => 'تاريخ إنتهاء السجل التجارى مطلوب ولا يمكن تركه فارغا',
            'commercial_record_date.date_formate'  => 'تاريخ إنتهاء السجل التجارى يجب ان يكون بتنسيق yyyy-mm-dd ',
        ];
		$validator = Validator::make($request->all(),[
			'phone'                  => 'required|unique:providers',
			'email'                  => 'required|email|unique:providers',
            'maroof_img'             => 'nullable|mimes:jpeg,png',
            'maroof_date'            => 'nullable|required_with:maroof_img|date_format:Y-m-d',
            'health_certific'        => 'nullable|mimes:jpeg,png',
            'health_certific_date'   => 'nullable|required_with:health_certific|date_format:Y-m-d',
            'commercial_record'      => 'nullable|mimes:jpeg,png',
            'commercial_record_date' => 'nullable|required_with:commercial_record|date_format:Y-m-d'
		], $messages);

		if($validator->fails()){
			return redirect()->back()->with('errors', $validator->errors())->withInput();
		}else{
		    $rateCounter = 0;
			$full_name = $request->input('fname')." ".$request->input('sname')." ".$request->input('tname')." ".$request->input('lname');
			if($request->hasFile('maroof_img')){
			    $rateCounter++;
			    $file = $request->file("maroof_img");
                $fileName = 'maroof-'.time(). $file->getClientOriginalName();
                $path = url('providerProfileImages/'.$fileName);
                $uploaded = $request->file('maroof_img')->move(public_path().'/providerProfileImages/', $fileName);
                if(!$uploaded){
                    $errors = array('فشل فى رفع الصوره');
                    return redirect()->back()->with('errors', $errors)->withInput();
                }else{
                    $maroof = $path;
                }
            }else{
			    $maroof = "";
            }

            if($request->hasFile('health_certific')){
                $rateCounter++;
                $file = $request->file("health_certific");
                $fileName = 'health-'.time().$file->getClientOriginalName();
                $path = url('providerProfileImages/'.$fileName);
                $uploaded = $request->file('health_certific')->move(public_path().'/providerProfileImages/', $fileName);
                if(!$uploaded){
                    $errors = array('فشل فى رفع الصوره');
                    return redirect()->back()->with('errors', $errors)->withInput();
                }else{
                    $health = $path;
                }
            }else{
                $health = "";
            }

            if($request->hasFile('commercial_record')){
                $rateCounter++;
                $file = $request->file("commercial_record");
                $fileName = 'commercial-'.time().$file->getClientOriginalName();
                $path = url('providerProfileImages/'.$fileName);
                $uploaded = $request->file('commercial_record')->move(public_path().'/providerProfileImages/', $fileName);
                if(!$uploaded){
                    $errors = array('فشل فى رفع الصوره');
                    return redirect()->back()->with('errors', $errors)->withInput();
                }else{
                    $commercial = $path;
                }
            }else{
                $commercial = "";
            }

            if($rateCounter == 3){
                $rate = 1;
            }else{
                $rate = 0;
            }
			$id = Providers::insertGetId([
				'full_name'    => $full_name,
				'first_name'   => $request->input('fname'),
				'second_name'  => $request->input('sname'),
				'third_name'   => $request->input('tname'),
				'last_name'    => $request->input('lname'),
				'brand_name'   => $request->input('bname'),
				'country_code' => $request->input('country_code'),
				'phone' 	   => $request->input('phone'),
				'email'        => $request->input('email'),
				'profile_pic'  => url('admin-assets/images/avatar_ic.png'),
				'country_id'   => $request->input('countries'),
				'city_id'      => $request->input('cities'),
				'address'      => $request->input('address'),
				'status'       => 0,
				'password'     => md5($request->input('password')),
                'maroof_img'   => $maroof,
                'health_certific'        => $health,
                'commercial_record'      => $commercial,
                'maroof_date'            => $request->input('maroof_date'),
                'health_certific_date'   => $request->input('health_certific_date'),
                'commercial_record_date' => $request->input('commercial_record_date'),
                'provider_rate'          => $rate
			]);

			if($id){
				if(!empty($request->input('categories'))){
					$cats = $request->input('categories');
					$inserts = array();
					for($i = 0; $i < count($cats); $i++){
						$inserts[$i]['provider_id'] = $id;
						$inserts[$i]['cat_id'] = $cats[$i];
					}

					if(!empty($inserts)){
						DB::table('providers_categories')->insert($inserts);
					}
				}
			}
			$request->session()->flash('success', 'Provider added successfully');
			return redirect()->route('provider.show');
		}
	}

	public function edit($id){
		//get Provider data 
		$provider   = Providers::where('provider_id', $id)->first();
		$city_id    = $provider->city_id;
		$country_id = $provider->country_id;

		//get countries
		$countries = DB::table('country')->get();

		//get country cities
		$cities = DB::table('city')->where('country_id', $country_id)->get();

		//get selected cats 
		$selectedCats = DB::table('providers_categories')->where('provider_id', $provider->provider_id)
														 ->select('cat_id')->get();
		$selectedCats = json_decode(json_encode($selectedCats), true);
		$selectedCats = array_map('current', $selectedCats);
		//get categories
		$categories = DB::table('categories')->where('publish', 1)
											 ->orWhere(function ($query) use($selectedCats) {
											        $query->whereIn('cat_id', $selectedCats);
											 })
											 ->orWhereIn('cat_id', $selectedCats)->get();
		return view('cpanel.providers.edit', compact('provider', 'cities', 'countries', 'city_id', 'country_id', 'categories', 'selectedCats'));
	}

	public function update(Request $request){
        $messages = [
            'phone.required' => 'رقم الجوال مطلوب ولا يمكن تركه فارغا',
            'phone.unique'   => 'رقم الجوال مستخدم من قبل',
            'email.required' => 'البريد الإلكترونى مطلوب ولا يمكن تركه فارغا',
            'email.unique'   => 'البريد الإلكترونى مستخدم من قبل',
            'email.email'    => 'خطأ فى صيغة البريد الإلكترونى',
            'maroof_img.mimes'        => 'صورة معروف يجب ان تكون بإمتداتات jpeg, png',
            'health_certific.mimes'   => 'صورة الشهادة الصحية يجب ان تكون بإمتداتات jpeg, png',
            'commercial_record.mimes' => 'صورة السجل التجارى يجب ان تكون بإمتداتات jpeg, png',
            'maroof_date.required_with' => 'تاريخ إنتهاء معروف مطلوب ولا يمكن تركه فارغا',
            'maroof_date.date_format'  => 'تاريخ إنتهاء معروف يجب ان يكون بتنسيق yyyy-mm-dd ',
            'health_certific_date.required_with' => 'تاريخ إنتهاء الشهادة الصحية مطلوب ولا يمكن تركه فارغا',
            'health_certific_date.date_formate'  => 'تاريخ إنتهاء الشهادة الصحية يجب ان يكون بتنسيق yyyy-mm-dd ',
            'commercial_record_date.required_with' => 'تاريخ إنتهاء السجل التجارى مطلوب ولا يمكن تركه فارغا',
            'commercial_record_date.date_formate'  => 'تاريخ إنتهاء السجل التجارى يجب ان يكون بتنسيق yyyy-mm-dd ',
        ];
        $validator = Validator::make($request->all(),[
            'phone'                  => 'required|unique:providers,phone,'.$request->input('provider_id').',provider_id',
            'email'                  => 'required|email|unique:providers,email,'.$request->input('provider_id').',provider_id',
            'maroof_img'             => 'nullable|mimes:jpeg,png',
            'maroof_date'            => 'nullable|required_with:maroof_img|date_format:Y-m-d',
            'health_certific'        => 'nullable|mimes:jpeg,png',
            'health_certific_date'   => 'nullable|required_with:health_certific|date_format:Y-m-d',
            'commercial_record'      => 'nullable|mimes:jpeg,png',
            'commercial_record_date' => 'nullable|required_with:commercial_record|date_format:Y-m-d'
        ], $messages);
//		$validator = Validator::make($request->all(),[
//			'phone' => 'required|unique:providers,phone,'.$request->input('provider_id').',provider_id',
//			'email' => 'required|email|unique:providers,email,'.$request->input('provider_id').',provider_id'
//		]);

		if($validator->fails()){
			return redirect()->back()->with('errors', $validator->errors())->withInput();
		}else{
			$full_name = $request->input('fname')." ".$request->input('sname')." ".$request->input('tname')." ".$request->input('lname');
			if($request->hasfile("maroof_img")){
                $file = $request->file("maroof_img");
                $fileName = 'maroof-'.time(). $file->getClientOriginalName();
                $path = url('providerProfileImages/'.$fileName);
                $uploaded = $request->file('maroof_img')->move(public_path().'/providerProfileImages/', $fileName);
                if(!$uploaded){
                    $errors = array('فشل فى رفع الصوره');
                    return redirect()->back()->with('errors', $errors)->withInput();
                }else{
                    $updates['maroof_img'] = $path;
                }
            }

            if($request->hasFile('health_certific')){
                $file = $request->file("health_certific");
                $fileName = 'health-'.time().$file->getClientOriginalName();
                $path = url('providerProfileImages/'.$fileName);
                $uploaded = $request->file('health_certific')->move(public_path().'/providerProfileImages/', $fileName);
                if(!$uploaded){
                    $errors = array('فشل فى رفع الصوره');
                    return redirect()->back()->with('errors', $errors)->withInput();
                }else{
                    $updates['health_certific'] = $path;
                }
            }

            if($request->hasFile('commercial_record')){
                $file = $request->file("commercial_record");
                $fileName = 'commercial-'.time().$file->getClientOriginalName();
                $path = url('providerProfileImages/'.$fileName);
                $uploaded = $request->file('commercial_record')->move(public_path().'/providerProfileImages/', $fileName);
                if(!$uploaded){
                    $errors = array('فشل فى رفع الصوره');
                    return redirect()->back()->with('errors', $errors)->withInput();
                }else{
                    $updates['commercial_record'] = $path;
                }
            }

            $updates['full_name']    = $full_name;
            $updates['first_name']   = $request->input('fname');
            $updates['second_name']  = $request->input('sname');
            $updates['third_name']   = $request->input('tname');
            $updates['last_name']    = $request->input('lname');
            $updates['brand_name']   = $request->input('bname');
            $updates['country_code'] = $request->input('country_code');
            $updates['phone'] 	     = $request->input('phone');
            $updates['email']        = $request->input('email');
            $updates['country_id']   = $request->input('countries');
            $updates['city_id']      = $request->input('cities');
            $updates['address']      = $request->input('address');
			if(!empty($request->input('upassword'))) {
                $updates['password'] = md5($request->input('upassword'));
            }
//
//            if(!empty($request->file('maroof_date'))){
//			    $updates['maroof_date'] = $request->input('maroof_date');
//            }
//
//            if(!empty($request->file('health_certific_date'))){
//                $updates['health_certific_date'] = $request->input('health_certific_date');
//            }
//
//            if(!empty($request->file('commercial_record_date'))){
//                $updates['commercial_record_date'] = $request->input('commercial_record_date');
//            }
            $update = Providers::where('provider_id', $request->input('provider_id'))->update($updates);
			if($update){
				if(!empty($request->input('categories'))){
					$cats = $request->input('categories');
					DB::table('providers_categories')->where('provider_id', $request->input('provider_id'))->delete();
					$inserts = array();
					for($i = 0; $i < count($cats); $i++){
						$inserts[$i]['provider_id'] = $request->input('provider_id');
						$inserts[$i]['cat_id'] = $cats[$i];
					}

					if(!empty($inserts)){
						DB::table('providers_categories')->insert($inserts);
					}
				}
			}
			$request->session()->flash('success', 'Provider updated successfully');
			return redirect()->route('provider.show');
		}
	}

	public function getOrderProperties($provider_id){
		//get data
		$getProviderData = Providers::where('provider_id', $provider_id)
									->select('allowed_from_time', 'allowed_to_time', 'delivery_price','receive_orders', 'current_orders', 'future_orders', DB::raw('DATE(avail_date) AS avail_date'), DB::raw('DATE(updated_at) AS last_updated'))
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
		$getTimes = DB::table("providers_order_timelines")->where('provider_id', $provider_id)
														  ->select('allowed_from_time', 'allowed_to_time')->get();

		//get deliveries
		$deliveries = DB::table("delivery_methods")->select('method_id','method_en_name',
															DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$provider_id.') > 0, 1, 0) AS chosen'))
												   ->get();
		return view('cpanel.providers.properties', compact('deliveries', 'getTimes', 'getProviderData', 'avail_date', 'updated_at', 'today', 'provider_id'));
	}

	public function saveOrderProperties(Request $request){
		$provider_id               = $request->input('provider_id');
		$data['delivery_price']    = $request->input('price');
		$data['allowed_from_time'] = $request->input('from');
		$data['allowed_to_time']   = $request->input('to');
		$data['current_orders']    = $request->input('current_orders');
		$data['future_orders']     = $request->input('future_orders');
		$data['receive_orders']    = $request->input('receive_orders');
		$data['avail_date']        = $request->input('avail_date');
		$exitTime1                 = $request->input('exitTimes1');
		$exitTime2                 = $request->input('exitTimes2');
		$exitTime3                 = $request->input('exitTimes3');
		$deliveries                = $request->input('deliveries');
		$exit_order_times = array();
		if($exitTime1 != NULL){
			array_push($exit_order_times, $exitTime1);
		}
		if($exitTime2 != NULL){
			array_push($exit_order_times, $exitTime2);
		}
		if($exitTime3 != NULL){
			array_push($exit_order_times, $exitTime3);
		}
		if(!empty($exit_order_times)){
			$exitTimes = array_values(array_unique($exit_order_times));
			if(count($exitTimes) != count($exit_order_times)){
				$request->session()->flash('errors', 'Order exit time can\'t be repeated');
				return redirect()->back()->withInput();
			}
		}else{
			$request->session()->flash('errors', 'You must provide at least one order exit time');
			return redirect()->back()->withInput();
		}

		if(count($deliveries) == NULL || empty($deliveries)){
			$request->session()->flash('errors', 'You must choose at least one delivery method');
			return redirect()->back()->withInput();
		}

		if($data['receive_orders'] == NULL){
			$data['receive_orders'] = 0;
		}

		if($data['current_orders'] == NULL){
			$data['current_orders'] = 0;
		}

		if($data['future_orders'] == NULL){
			$data['future_orders'] = 0;
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
						$inserts[$i]['allowed_time']    = $exit_order_times[$i];
					}
					DB::table('providers_order_timelines')->insert($inserts);
				}
			});	
			$request->session()->flash('success', 'Properties has been set successfully');
			return redirect()->route('provider.show');
		} catch (Exception $e) {
			$request->session()->flash('errors', 'Something is wrong');
			return redirect()->back()->withInput();
		}
	}

	public function getProviderIncomeView(){
		$providers = Providers::get();
		return view('cpanel.providers.income', compact('providers'));
	}

	public function incomeSearch(Request $request){
		$type = $request->input('type');
		if($type == 'provider'){
            $table = 'providers';
            $col   = 'providers.provider_id';
            $cond  = 'orders_headers.provider_id';
            $money = '(orders_headers.net_value - marketer_value)AS credit';
            $app   = 'orders_headers.app_value AS app';
            $marketer = 'orders_headers.marketer_value AS marketer';
        }elseif($type == 'delivery'){
            $table = 'deliveries';
            $col   = 'deliveries.delivery_id';
            $cond  = 'orders_headers.delivery_id';
            $money = '(orders_headers.delivery_price - orders_headers.delivery_app_value) AS credit';
            $app   = 'orders_headers.delivery_app_value AS app';
            $marketer = 'orders_headers.marketer_delivery_value AS marketer';
        }else{
            return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
        }


        $conditions[] = [$cond, '=', $request->input('id')];
        $conditions[] = ['orders_headers.status_id' , '=', 4];
        if(!empty($request->input('from')) && !empty($request->input('to'))){
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '>=', $request->input('from')];
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '<=', $request->input('to')];
        }

        // var_dump($conditions);
        // die();
	    $result = DB::table('orders_headers')->where($conditions)
	        						         ->join($table, $cond, '=', $col)
	        						         ->join('order_details', 'orders_headers.order_id', '=', 'order_details.order_id')
	        						         ->select(
			        						   		'orders_headers.order_code', 
			        						   		'orders_headers.total_qty', 
			        						   		'orders_headers.total_value', 
			        						   		'orders_headers.order_id AS invo_no',
			        						   		DB::raw($money), 
			        						   		DB::raw($app), 
			        						   		DB::raw($marketer), 
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
                                <td>'.ROUND($row->app,2).'</td>
                                <td>'.ROUND($row->marketer,2).'</td>
                                <td>'.(($row->balance_status == 1)? 'Pending' : 'Done').'</td>
                            </tr>';
	   		}
	   	}

	   	return response()->json(['data'=>$data, 'total'=>round($total,2)]);
	}
}
