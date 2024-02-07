<?php

namespace App\Libraries\SSO;

use App\Models\SSOCredentials;
use App\Exceptions\AccountNotConfiguredException;
use App\Exceptions\DriverNotSetException;

abstract class Provider {

    // Base class that providers extend
    protected int $account_id;
    private $_credentials; //cache

    public function __construct( int $account_id ) {
        $this->account_id = $account_id;
        /* This is a check to make sure that the driver is set on the custom provider class. */
        if(!isset($this->driver)){
            throw new DriverNotSetException();
        }
        //Check to see if the account provided has a provider record in sso_client_credentials
        //This has the knock on effect of caching the information to prevent further DB queries
        if(is_null($this->getCredentials())) {
            throw new AccountNotConfiguredException( $account_id, $this->driver );
        }
    }

    //This method should be added to your custom provider class to uniquely
    //construct that provider's config payload from the base credentials
    abstract public function getConfig();

    /**
     * Checks a local cache variable for the data, and loads it if not present
     * to avoid multiple lookups
     *
     * @return The credentials for the account | null.
     */
    public function getCredentials() {
        //Check to see if a copy of the credentials have been cached and load if not
        if(is_null($this->_credentials)){
            $this->loadData();
        }
        // return the cached copy
        return $this->_credentials;
    }

    private function loadData() {
        $credentials = SSOCredentials::where('account_id', $this->account_id)->where('provider', $this->driver);
        if(!($this->testing ?? false)){
           $credentials = $credentials->where('enabled', 1);
        }
        $this->_credentials = $credentials->first();
    }

    public function getClientId() {
        if(is_null($this->_credentials)){
            $this->loadData();
        }else{
            return $this->_credentials->client_id;
        }
    }

    public function getClientSecret() {
        if(is_null($this->_credentials)){
            $this->loadData();
        }else{
            return $this->_credentials->client_secret;
        }
    }

    public function getTenantId() {
        if(is_null($this->_credentials)){
            $this->loadData();
        }else{
            return $this->_credentials->tenant_id;
        }
    }

    public function getRedirectURL() {
        if(is_null($this->_credentials)){
            $this->loadData();
        }else{
            return $this->_credentials->redirect_url;
        }
    }

}
