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

class CategoriesController extends Controller
{
	public function __construct()
	{
		
	}

	public function getCats()
	{
		$categories = Categories::get();
		return view('cpanel.categories.categories', compact('categories'));
	}

	public function createCat()
	{
		return view('cpanel.categories.create');
	}

	public function storeCat(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'en_name'      => 'required|unique:categories,cat_en_name',
            'ar_name'      => 'required|unique:categories,cat_ar_name',
            'category_img' => 'required|mimes:jpeg,png,jpg'
        ]);

        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        if($request->hasFile('category_img')){
            $fileName = 'category-'.time().'.jpg';
            $filePath = url('categoriesImages/'.$fileName);
            $uploaded = $request->file('category_img')->move(public_path().'/categoriesImages/', $fileName);
            // $path = $request->file->storeAs('categoriesImg', 'category-'.time().'.jpg');
            if(!$uploaded){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $errors = array('You must upload category image');
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $insert = Categories::insert([
            'cat_en_name' => $request->input('en_name'),
            'cat_ar_name' => $request->input('ar_name'),
            'cat_img'     => $filePath
        ]);

        if($insert){
            $request->session()->flash('success', 'Category has been added successfully');
            return redirect()->route('category.show');
        }else{
            $errors = array('Failed to add the category');
            return redirect()->back()->withInput()->withErrors($errors);
        }
	}

	public function editCat($id)
	{
		$category = Categories::where('cat_id', $id)->first();
		return view('cpanel.categories.edit', compact('category'));
	}

	public function updateCat(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id'           => 'required',
            'en_name'      => 'required|unique:categories,cat_en_name,'.$request->input('id').',cat_id',
            'ar_name'      => 'required|unique:categories,cat_ar_name,'.$request->input('id').',cat_id',
            'category_img' => 'nullable|mimes:jpeg,png,jpg'
        ]);

        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        if($request->hasFile('category_img')){
            $fileName = 'category-'.time().'.jpg';
            $filePath = url('categoriesImages/'.$fileName);
            $uploaded = $request->file('category_img')->move(public_path().'/categoriesImages/', $fileName);
            // $path = $request->file->storeAs('categoriesImg', 'category-'.time().'.jpg');
            if(!$uploaded){
                $errors = array('Failed to upload the image');
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }else{
            $filePath = "";
        }

        if($filePath != ""){
        	$update = Categories::where('cat_id', $request->input('id'))
        		      ->update([
				            'cat_en_name' => $request->input('en_name'),
				            'cat_ar_name' => $request->input('ar_name'),
				            'cat_img'     => $filePath
	        			]);
        }else{
        	$update = Categories::where('cat_id', $request->input('id'))
        		      ->update([
				            'cat_en_name' => $request->input('en_name'),
				            'cat_ar_name' => $request->input('ar_name')
	        			]);
        }

        if($update){
            $request->session()->flash('success', 'Category has been updated successfully');
            return redirect()->route('category.show');
        }else{
            $errors = array('Failed to update the category');
            return redirect()->back()->withInput()->withErrors($errors);
        }
	}
}
