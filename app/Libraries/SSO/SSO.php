<?php
namespace App\Libraries\SSO;
use App\Models\SSOClientCredentials;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

/*
    Provides a standardised driver interface so providers can be easily interchanged.
*/

class SSO {

    private Provider $_provider;

    //Provider is injected as a dependency and must extend the Provider class
    public function __construct( Provider $provider ) {
        $this->_provider = $provider;
    }

    //Static functions to do the heavy lifting for certain scenarios

    public static function getDomainConfigurations(array $account_ids = null, $forced='all') {
        $domain = request()->getHost();

        $configurations = SSOClientCredentials::query();

        if(!is_null($account_ids)){
            $configurations = $configurations->whereIn('account_id',$account_ids);
        }else{
            $configurations = $configurations->where('domain', $domain );
        }

        //forced SSO login
        if($forced != 'all'){
            if ($forced == 1) {
                $configurations = $configurations->where('forced', 1);
            }elseif ($forced == 0) {
                $configurations = $configurations->where('forced', 0);
            }
        }

        $configurations = $configurations->where('enabled',1)->where('provider','!=','TEAMS')->orderBy('id','asc')->get();

        if($configurations->count()>0){
            session()->put('sso_account_id',$configurations[0]->account_id);
        }else{
            session()->forget('sso_account_id');
        }
        return $configurations;
    }

    public static function getProviderRedirect($provider) {
        //check account id is set
        if(!empty(session('sso_account_id'))){
            switch( Str::upper($provider) ) {
                case 'AZURE':
                    try {
                        $azure = new AzureProvider(account_id: session('sso_account_id'));
                    }catch(\App\Exceptions\AccountNotConfiguredException $exception) {
                        abort(400, "SSO: Account {$exception->account_id} is not configured to use {$exception->driver}");
                    }catch(\Exception $exception){
                        abort(400, "SSO: " . $exception->getMessage());

                    }
                    $sso = new SSO($azure);
                    return $sso->redirect($provider);
                    break;
                default:
                    return redirect()->route('login');
            }
        }
        //abort(400, "SSO: No account id set");
        return redirect()->route('login');
    }

    public static function getConfigForProvider($provider) {
        //check account id is set

        SSO::getDomainConfigurations(); //set the account id

        switch( Str::upper($provider) ) {
            case 'AZURE':
                try {
                    $azure = new AzureProvider(account_id: session('sso_account_id'));
                }catch(\App\Exceptions\AccountNotConfiguredException $exception) {
                    abort(400, "SSO: Account {$exception->account_id} is not configured to use {$exception->driver}");
                }catch(\Exception $exception){
                    abort(400, "SSO: " . $exception->getMessage());

                }
                $sso = new SSO($azure);
                return $sso->getConfig();
                break;
            default:
                return null;
                break;
        }
    }


    //Instance methods

    public function redirect( $provider ){
        $provider = Str::lower($provider);
        $config = $this->getConfig();
        return Socialite::driver($provider)->setConfig($config)->redirect();
    }

    /**
     * This function returns the configuration of the provider
     *
     * @return The config object | null.
     */
    public function getConfig() {
        return $this->_provider->getConfig();
    }

    public function getClientId() {
        return $this->_provider->getClientId();
    }

    public function getClientSecret() {
        return $this->_provider->getClientSecret();
    }

    public function getTenantId() {
        return $this->_provider->getTenantId();
    }

    public function getRedirectURL() {
        return $this->_provider->getRedirectURL();
    }

}
