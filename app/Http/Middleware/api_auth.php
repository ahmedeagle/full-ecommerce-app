<?php

namespace App\Http\Middleware;

use Closure;
use DB;
class api_auth
{
    /*
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $method = $request->method();
        if($method != "GET"){
            $api_email    = $request->input('api_email');
            $api_password = $request->input('api_password');
            $get = DB::table('api_users')
                ->where('api_email', $api_email)
                ->where('api_pass', md5($api_password))
                ->first();
            if(!$get || $get == NULL){
                return response()->json(['message' => 'Unauthenticated.']);
            }
        }
        return $next($request);
    }
}
