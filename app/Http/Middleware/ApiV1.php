<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use DB;
class ApiV1
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiKey = $request->bearerToken();
        
        $user = User::selectRaw('id, whitelist_ip')->whereRaw('BINARY api_key = ?',[$apiKey])->first();
        
        if(empty($apiKey) || !$user){
            abort(401);
        }

        $whitelistIp = !empty($user->whitelist_ip) ? explode(',',$user->whitelist_ip) : [];

        if(!empty($whitelistIp) && !in_array($request->ip(),$whitelistIp)){
            return response()->json([
                'success'=>false,
                'message'=>'Alamat IP Anda tidak memiliki izin untuk mengakses API ini. Tambahkan IP '.$request->ip().' ke daftar whitelist IP dan coba lagi'
            ]);
        }
        
        return $next($request);
    }
}
