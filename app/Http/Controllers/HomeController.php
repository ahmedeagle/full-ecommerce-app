<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class HomeController extends Controller
{
     
    
     
    public function getProvidersList(Request $request,$name){
        $providers =  DB::table("providers")
                        ->join("images" , "images.id" ,"providers.image_id")
                        // ->where("providers.phoneactivated" , "1")
                        ->where("providers.accountactivated" , "1")
                        ->select(
                            "providers.id AS provider_id",
                            "providers." . $name . "_name AS name",
                            DB::raw("CONCAT('". url('/') ."','/storage/app/public/providers/', images.name) AS image_url")
                        )
                        ->take(7)
                        ->get();
        $this->filter_providers_branches_by_distance($request,$name ,$providers);
        return $providers;
    }


         //filter result by distance or rate 
    public function filter_providers(Request $request ,$name,$providers ,$type = 0){

        if(isset($providers) && $providers -> count() > 0){
          
        foreach($providers as $key => $provider){
             
           
                $rates = DB::table('providers_rates')
                    ->where('providers_rates.provider_id' , $provider->provider_id)
                    ->select(
                        DB::raw("COUNT(providers_rates.id) AS number_of_rates"),
                        DB::raw("SUM(providers_rates.rates) AS sum_of_rates")
                     )
                    ->first();
                $numberOfRates = $rates->number_of_rates;
                $sumRate   = $rates->sum_of_rates;
                 if($numberOfRates != 0 && $numberOfRates != null){
                    $totalAverage  = $sumRate/$numberOfRates;
                }else{
                    $totalAverage = 0;
                }
               
                if($request->input('latitude') && $request->input('longitude')){
                    $latitude = $request->input('latitude');
                    $longitude = $request->input('longitude');
                    $distance =$this->getDistance($provider->longitude,$provider->latitude ,$longitude,$latitude,"KM");
                }else{
                    $distance = -1;
                }
                $provider->distance         = $distance;
                $provider->averageRate      = $totalAverage;

                unset($provider -> provider_rate);
                
            
        }

        }

    }

}
