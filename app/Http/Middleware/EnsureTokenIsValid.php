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
        $token = $request->input('api_token') ?? $request->get('api_token') ?? $request->header('x-api-token') ?? null;

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
