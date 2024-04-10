<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\ApiKey;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tokenValid = false;

        //get first non-empty source - allows for x-api-token to be always sent as an auth, and then overridden by form post vars for specific source assignment
        //$token = $request->input('api_token') ?? $request->get('api_token') ?? $request->header('x-api-token') ?? null;
        $token = null;
        if(!empty(trim($request->input('api_token')))){
            $token = trim($request->input('api_token'));
        }elseif(!empty(trim($request->get('api_token')))){
            $token = trim($request->get('api_token'));
        }elseif(!empty(trim($request->header('x-api-token')))){
            $token = trim($request->header('x-api-token'));
        }

        if(!is_null($token)){
            $tokenValid = ApiKey::where('api_token', $token)->where('status', ApiKey::ACTIVE)->first();
        }

        if (!$tokenValid) {
            return response()->json('Unauthorized', 401);
        }else{
            $tokenValid->last_login_at = date('Y-m-d H:i:s');
            $tokenValid->last_login_ip = $request->ip();
            $tokenValid->save();
            session()->put('account_id', $tokenValid->account_id);
            session()->put('source_id', $tokenValid->id);
        }

        return $next($request);
    }
}
