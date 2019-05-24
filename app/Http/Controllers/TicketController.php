<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use DB;
class TicketController extends Controller
{

    public function get_ticket_types(Request $request){
       
        $lang = $request->input('lang');

        $messages = array(
            'access_token.required' => 1
        );

        $validator = Validator::make($request->all(), [
            'access_token' => 'required',
        ], $messages);



        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
        }

 
        $name  = ($lang == 'ar') ? 'ar' : 'en' ;

        $types = DB::table("ticket_types")
                    ->select(
                        "ticket_types.id AS type_id",
                        "ticket_types.". $name ."_name AS type_name"
                    )
                    ->get();

        return response()->json(['status' => true, 'errNum' => 0, 'msg' => trans('success') , "types" => $types]);

    }
    public function add_ticket(Request $request){
        
        $lang = $request -> lang;

        $name  = ($lang == 'ar') ? 'ar' : 'en' ;

        if($lang == 'ar'){
                 

         $msg        = [
                1   => 'جميع الحقول مطلوبة ',
                2   =>'نوع التذكره غير موجود ',
                3   =>'تم انشاء التذكره بنجاح ',
                5   => 'حدث خطا في انشاء التذكره ',
                6 => ' عدد احرف عنوان التذكره لابد الا يزيد عن  100 حرف  '
            ];

        }else{

            $msg        = [
            1   => 'All fields required',
            2   => 'Ticket type not exists',
            3   => 'ticket created successfully',
            5   => 'fail to add successfully',
            6 => 'max characters is 100 '
        ];


        }

        $rules      = [
            "title"       => "required|max:100",
            "description" => "required",
            "type"        => "required|exists:ticket_types,id",
        ];
        $messages   = [
            "required"       => 1,
            "exists"         => 2,
            "max"            =>6
        ];
        

        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }

 
           $user_id     = $this -> get_id($request,'users','user_id');

           $provider_id = $this -> get_id($request,'providers','provider_id');

           $delivery_id = $this -> get_id($request,'deliveries','delivery_id');

            
            if($user_id &&  $user_id != 0){

                   $actor_type = "user";
                   $actor_id   = $user_id;
                   $fromUser   = "1" ;

            }elseif($provider_id && $provider_id != 0){


                   $actor_type = "provider";
                   $actor_id   = $provider_id;
                   $fromUser   = "2" ;


            }elseif($delivery_id && $delivery_id != 0){
                     

                    $actor_type = "delivery"; 
                    $actor_id   = $delivery_id;
                    $fromUser   = "3" ;

            }else{
                  

                   return response()->json(['status' => false, 'errNum' => 3, 'msg' => 'المستخدم غير موجود ' ]);
 
            }
 
  
    
        $title = $request->input("title");
        $desc  = $request->input("description");
        $type  = $request->input("type");

        $ticket_id = DB::table("tickets")
                        ->insertGetId([
                           "title"       => $title,
                           "type_id"     => $type,
                           "actor_id"    => $actor_id,
                           "actor_type"  => $actor_type
                        ]);


                DB::table("ticket_replies")
                        ->insert([
                            "ticket_id" =>$ticket_id,
                            "reply"     => $desc,
                            "FromUser"  => $fromUser
                         ]);

        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3],'ticket_id' => $ticket_id]);

    }


    public function get_tickets(Request $request){
 
        $lang = $request->input('lang');

        $messages = array(
            'access_token.required' => 1
        );

        $validator = Validator::make($request->all(), [
            'access_token' => 'required',
        ], $messages);



        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
        }


         

           $user_id     = $this -> get_id($request,'users','user_id');

           $provider_id = $this -> get_id($request,'providers','provider_id');

           $delivery_id = $this -> get_id($request,'deliveries','delivery_id');

            
            if($user_id &&  $user_id != 0){

                   $actor_type = "user";
                   $actor_id   = $user_id;
                   $fromUser   = 1 ;

            }elseif($provider_id && $provider_id != 0){


                   $actor_type = "provider";
                   $actor_id   = $provider_id;
                   $fromUser   = 2 ;


            }elseif($delivery_id && $delivery_id != 0){
                     

                    $actor_type = "delivery"; 
                    $actor_id   = $delivery_id;

                    $fromUser   = 3 ;

            }else{
                  

                   return response()->json(['status' => false, 'errNum' => 3, 'msg' => 'المستخدم غير موجود ' ]);
 
            }
 

       $name  = ($lang == 'ar') ? 'ar' : 'en' ;
 

         $tickets = DB::table("tickets")
                    ->where("actor_id" ,$actor_id)
                    ->where("actor_type" ,$actor_type)
                    ->join('ticket_types','tickets.type_id','=','ticket_types.id')
                    ->select(
                            "tickets.id",
                            "tickets.title",
                            "solved",
                            "ticket_types.".$name."_name AS ticket_Type" ,
                            DB::raw("DATE(tickets.created_at) AS create_date"),
                            DB::raw("Time(tickets.created_at) AS create_time")
                        )
                    ->orderBy("tickets.id" , "DESC")
                    ->get();




        return response()->json(['status' => true, 'errNum' => 0, 'msg' => trans('success') , "tickets" => $tickets]);


    }

    public function get_ticket_messages(Request $request){
         

        $lang = $request -> lang;

        $name  = ($lang == 'ar') ? 'ar' : 'en' ;

      
      if($lang == 'ar') 
      {
        $msg        = [
            1   => 'جميع الحقول مطلوبة  ',
            2   => 'رقم التذكره غير موجود  ',
            3   => 'تمت  العملية نجاح ',
            5   => 'فشل في جلب المحادثة ',
        ];
    }else{

        $msg        = [
            1   => 'All Fields Required',
            2   => 'ticket id not exists',
            3   => 'successfully',
            5   => 'Failed To get Conversation',
        ];


    }
 
        $messages   = [
            "required"       => 1,
            "exists"         => 2,
        ];


        $rules      = [
            "id"    => "required|exists:tickets,id",
        ];


        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }



           $user_id     = $this -> get_id($request,'users','user_id');

           $provider_id = $this -> get_id($request,'providers','provider_id');

           $delivery_id = $this -> get_id($request,'deliveries','delivery_id');

            
            if($user_id &&  $user_id != 0){
                   $actor_id   = $user_id;
                   $actor_type = "user";
                   $fromUser   = "1";
                   $table      ="users";
                   $col        ="user_id";
            }elseif($provider_id && $provider_id != 0){
                    $actor_id   = $provider_id;
                    $actor_type = "provider";
                    $fromUser   = "2";
                    $table      ="providers";
                    $col        ="provider_id";
            }elseif($delivery_id && $delivery_id != 0){                    
                     $actor_id   = $delivery_id; 
                     $actor_type = "delivery";
                     $fromUser   = "3";
                     $table      ="deliveries";
                     $col        ="delivery_id";
            }else{
                   
                   return response()->json(['status' => false, 'errNum' => 3, 'msg' => 'المستخدم غير موجود ' ]);
            }



        $id = $request->input("id");

        $ticket_info = DB::table("tickets")
                        ->where("tickets.id" ,$id)
                        ->join('ticket_types','tickets.type_id','=','ticket_types.id')
                        ->select(
                            "tickets.id",
                            "tickets.title",
                            "tickets.type_id",
                            "tickets.solved",
                            "ticket_types.".$name."_name AS ticket_Type" ,
                            DB::raw("DATE(tickets.created_at) AS create_date"),
                            DB::raw("Time(tickets.created_at) AS create_time")
                        )
                        ->first();

 


      /*  $ticketTypes = DB::table("ticket_types")
                        ->select(
                             "id",
                             $name . "_name AS type"
                            )
                        ->get();

        foreach($ticketTypes as $type){

            if($type->id == $ticket_info->type_id){
                $type->selected = true;
            }else{
                $type->selected = false;
            }




        }*/

        $replies = DB::table("ticket_replies")
                        ->join("tickets" , "tickets.id" , "ticket_replies.ticket_id")
                        ->join($table, $table.'.'.$col , "tickets.actor_id")
                        ->join("ticket_types" , "ticket_types.id" , "tickets.type_id")
                        ->where("ticket_replies.ticket_id" , $request->input("id"))
                        ->where("tickets.actor_id" , $actor_id)
                        ->where("tickets.actor_type" , $actor_type)
                        ->select(
                                   $table.".full_name AS actor_name",
                                   "ticket_replies.reply",
                                   "ticket_replies.FromUser",
                                   DB::raw("DATE(ticket_replies.created_at) AS reply_create_date"),
                                   DB::raw("TIME(ticket_replies.created_at) AS reply_create_time")
                                )
                        ->orderBy("ticket_replies.id" , "DESC")
                        ->paginate(10);

           // update all message from admin to seen 
        DB::table("ticket_replies")
                        ->where("ticket_id" , $id)
                        ->where("FromUser", "0")
                        ->update(["seen" => "1"]);

 
            return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3], "ticket_info" => $ticket_info  ,"replies" => $replies]);
         
    }



    public function add_message(Request $request){
         $lang = $request -> lang;
        $name  = ($lang == 'ar') ? 'ar' : 'en' ;
         if($lang == 'ar')
         {
                
         $msg = [
                    1   => 'جميع الحقول مطلوبة ',
                    2   => 'التذكرة  غير موجوده ',
                    3   => 'تم الارسال بنجاح ',
                    5   => 'فشل في الارسال ',
                ];

         }else{
             
        $msg = [
                1   => 'All Fields Required',
                2   => 'Ticket Not Exists',
                3   => 'Message successfully Added',
                5   => 'Faild To Send Message',
            ];

         }
 
         $messages   = [
            "required"       => 1,
            "exists"         => 2,
        ];


        $rules      = [
            "id"        => "required|exists:tickets,id",
            "message"   => "required",
        ];


        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){

            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);

        }



        $id      = $request->input("id");
        $message = $request->input("message");


           $user_id     = $this -> get_id($request,'users','user_id');

           $provider_id = $this -> get_id($request,'providers','provider_id');

           $delivery_id = $this -> get_id($request,'deliveries','delivery_id');

            
            if($user_id &&  $user_id != 0){
                   $actor_id   = $user_id;
                   $actor_type = "user";
                   $fromUser   = "1";
            }elseif($provider_id && $provider_id != 0){
                    $actor_id   = $provider_id;
                    $actor_type = "provider";
                    $fromUser   = "2";
            }elseif($delivery_id && $delivery_id != 0){                    
                     $actor_id   = $delivery_id; 
                     $actor_type = "delivery";
                     $fromUser   = "3";
            }else{
                   
                   return response()->json(['status' => false, 'errNum' => 3, 'msg' => 'المستخدم غير موجود ' ]);
            }


        $ticket = DB::table("tickets")
                        ->where("id" ,$id)
                        ->where("actor_type" ,$actor_type)
                        ->select("actor_id")
                        ->first();


        if($ticket){

            if($ticket->actor_id != $actor_id){
                return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
            }
        }

        DB::table("ticket_replies")
                ->insert([
                   "reply" => $message,
                   "ticket_id" => $id,
                   "FromUser" => $fromUser
                ]);
        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3]]);
    }
}
