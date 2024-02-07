<?php

namespace App\Libraries\SSO;

use SocialiteProviders\Manager\Config;

class AzureProvider extends Provider {

    //This needs to match the provider column in sso_client_credentials
    protected $driver = 'AZURE';
    public $testing;

    public function __construct(int $account_id, $driver ='AZURE', $testing = false) {
        $this->driver = $driver;
        $this->testing = $testing;
        parent::__construct($account_id);
    }

    public function getConfig() {
        //Call the parent class
        $credentials = $this->getCredentials();

        //Creates custom payload for Azure
        if(!is_null($credentials)){
            //SocialiteProviders\Manager\Config
            return new Config(
                $credentials->client_id,
                $credentials->client_secret,
                env('APP_URL').'/platform/azure/auth',
                ['tenant' => $credentials->tenant_id ]
            );
        }else{
            return null;
        }

    }

}
