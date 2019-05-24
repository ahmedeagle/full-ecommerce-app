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

class CommentsController extends Controller
{
	public function __construct(){
		
	}

	public function getComments(){
		$users = User::select('user_id', 'full_name')->get();
		$comments = DB::table('meal_comments')
					->join('users', 'meal_comments.user_id', '=', 'users.user_id')
					->join('meals', 'meal_comments.meal_id', '=', 'meals.meal_id')
					->select(
							'meal_comments.id', 
							'meal_comments.comment', 
							DB::raw('DATE(meal_comments.created_at) AS created'), 
							'users.full_name', 
							DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
							'meals.meal_name', 
							'meals.main_image',
							DB::raw('DATE(meal_comments.created_at) AS created')
							)
					->orderBy('meal_comments.id', 'DESC')
					->paginate(40);
		return view('cpanel.comments.comments', compact('users', 'comments'));
	}

	public function search($from, $to, $user, $phone){
		$conditions = [];
		if(!in_array($from, ["null", null, ""]) && !in_array($to, ["null", null, ""])){
			$conditions[] = [DB::raw('DATE(meal_comments.created_at)'), '<=', $to];
			$conditions[] = [DB::raw('DATE(meal_comments.created_at)'), '>=', $from];
		}

		if(!in_array($user, ["null", null, ""])){
			$conditions[] = ['meal_comments.user_id', '=', $user];
		}

		if(!in_array($phone, ["null", null, ""])){
			$conditions[] = ['meal_comments.user_id', 'LIKE', "%".$phone];
		}

		$users = User::select('user_id', 'full_name')->get();
		if(!empty($conditions)){
			$comments = DB::table('meal_comments')
						->where($conditions)
						->join('users', 'meal_comments.user_id', '=', 'users.user_id')
						->join('meals', 'meal_comments.meal_id', '=', 'meals.meal_id')
						->select(
								'meal_comments.id', 
								'meal_comments.comment', 
								DB::raw('DATE(meal_comments.created_at) AS created'), 
								'users.full_name', 
								DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
								'meals.meal_name', 
								'meals.main_image',
								DB::raw('DATE(meal_comments.created_at) AS created')
								)
						->orderBy('meal_comments.id', 'DESC')
						->paginate(40);
		}else{
			$comments = DB::table('meal_comments')
						->join('users', 'meal_comments.user_id', '=', 'users.user_id')
						->join('meals', 'meal_comments.meal_id', '=', 'meals.meal_id')
						->select(
								'meal_comments.id', 
								'meal_comments.comment', 
								DB::raw('DATE(meal_comments.created_at) AS created'), 
								'users.full_name', 
								DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
								'meals.meal_name', 
								'meals.main_image',
								DB::raw('DATE(meal_comments.created_at) AS created')
								)
						->orderBy('meal_comments.id', 'DESC')
						->paginate(40);
		}

		return view('cpanel.comments.comments', compact('users', 'comments'));
	}

	public function delete($id, Request $request){
		$check = DB::table('meal_comments')->where('id', $id)->delete();
		if($check){
			$request->session()->flash('success', 'Deleted successfully');
		}else{
			$request->session()->flash('err', 'Failed to delete please try again later');
		}

		return redirect()->back();
	}

	public function today(){
		$comments = DB::table('meal_comments')
					->where(DB::raw('DATE(meal_comments.created_at)'), date('Y-m-d', time()))
					->join('users', 'meal_comments.user_id', '=', 'users.user_id')
					->join('meals', 'meal_comments.meal_id', '=', 'meals.meal_id')
					->select(
							'meal_comments.id', 
							'meal_comments.comment', 
							DB::raw('DATE(meal_comments.created_at) AS created'), 
							'users.full_name', 
							DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
							'meals.meal_name', 
							'meals.main_image',
							DB::raw('DATE(meal_comments.created_at) AS created')
							)
					->orderBy('meal_comments.id', 'DESC')
					->paginate(40);
		return view('cpanel.comments.today', compact('comments'));
	}
}
