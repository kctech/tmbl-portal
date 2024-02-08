<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Libraries\SSO\SSO;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $maxAttempts = 5; // Default is 5
    protected $decayMinutes = 1; // Default is 1

    //tells laravel what the name of the column in the database is for a user
    public function username(){
        return 'email';
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function ssoAuthenticate($provider) {

        $config = SSO::getConfigForProvider($provider);

        if(is_null($config)){
            abort(400,'Sign in attempt on disabled or unconfigured azure driver');
        }

        try{
            $user = Socialite::driver(Str::lower($provider))->setConfig($config)->user();
        } catch (\Exception $exception) {
            return view('auth.sso_no_user')->with('error','ERR:1');
        }

        if(!$user){
            //abort(403);
            return view('auth.sso_no_user')->with('error', 'ERR:2');
        }

        $email = $user->email;
        //session('sso_account_id') is set in the call to getConfigForProvider above
        $tmbl_user = User::where('emailAddress',$email)->where('accountId',session('sso_account_id'))->where('status',0)->first();

        if(!is_null($tmbl_user)){
            Auth::login($tmbl_user);
            session()->regenerate();
            return redirect()->to($this->redirectTo);
        }else{
            return view('auth.sso_no_user')->with('error', 'ERR:3');
            //dd('User not found');
        }
    }

}
