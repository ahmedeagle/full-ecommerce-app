<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Order_header extends Model
{
     protected $table = 'orders_headers';
    
 
 
    protected static function boot()
        {
            parent::boot();
         
            
        }
        
        public function scopeNotExpire($query , $time_counter_in_min)
        {
              
                 
                             return $query 
                                 
                                 -> where(
                                     'orders_headers.created_at', '>=', Carbon::now()->subMinutes($time_counter_in_min)
                                     
                                     ) ;
                             
                              
                          
                        
        
         
       }



    
}
