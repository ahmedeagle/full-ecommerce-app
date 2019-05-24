<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Page;
use DB;
use Validator;
class PageController extends Controller
{
    public function get_pages(Request $request){

        $lang = $request->input('lang');
         
        $name = ($lang  == 'ar') ? 'ar' : 'en' ;

        $pages = Page::select(
                    "id"
                    ,$name . "_title AS title")
                ->get();

        return response()->json(['status' => true , "errNum" => 0 , "msg" => trans("messages.success"), "page" => $pages]);
    }


    public function get_usage_agreement_page(Request $request){

        $lang = $request->input('lang');
         
        $name = ($lang  == 'ar') ? 'ar' : 'en' ;

        $pages = Page::where('id' , 1)->select("id"
            ,$name . "_title AS title"
            ,$name . "_content AS content")
            ->first();

        return response()->json(['status' => true , "errNum" => 0 , "msg" => trans("messages.success"), "page" => $pages]);
    }
    public function get_page(Request $request){
        $lang = $request->input('lang');
         
        $name = ($lang  == 'ar') ? 'ar' : 'en' ;

        $rules      = [
            "id" => "required|exists:pages,id",
        ];
        $messages   = [
            "required"   => 1,
            "exists"     => 2
        ];
        $msg        = [
            1  => 'جميع الحقول مطلوبة ',
            2  =>  'الصفحه غير موجوده ',
            3  => "تمت العملية بنجاح ",
        ];
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }
         $id   = $request->input('id');
        $pages = Page::where('id' , $id)->select(
            "id"
            ,$name . "_title AS title"
            ,$name . "_content AS content")
            ->first();
        return response()->json(['status' => true , "errNum" => 0 , "msg" => trans("messages.success"), "page" => $pages]);
    }
}
