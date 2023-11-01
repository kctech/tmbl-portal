<?php

namespace App\Providers;

use App\User;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\GdprConsent' => 'App\Policies\GdprConsentPolicy'
         ,'App\BtlConsent' => 'App\Policies\BtlConsentPolicy'
         ,'App\SdltDisclaimer' => 'App\Policies\SdltDisclaimerPolicy'
         ,'App\Client' => 'App\Policies\ClientPolicy'
         ,'App\ClientTransferConsent' => 'App\Policies\ClientTransferConsentPolicy'
         ,'App\TermsConsent' => 'App\Policies\TermsConsentPolicy'
         ,'App\Quote' => 'App\Policies\QuotePolicy'
         ,'App\User' => 'App\Policies\UserPolicy'
         ,'App\EligibilityStatement' => 'App\Policies\EligibilityStatementPolicy'
         //,'App\AccountModule' => 'App\Policies\AccountModulePolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //allow admins to skip any auth() checks
        //dev only
        Gate::before(function ($user, $ability) {
            //return $user->role->permissions == 'sudo';
            if($user->role->permissions == 'sudo') {
                //return true;
            }
        });

        Gate::define('impersonate', function ($user) {
            return $user->role->level <= 1;
        });

        //Gate::define('module', 'App\Policies\AccountModulePolicy@module');
        Gate::define('users', function ($user) {
            return $user->role->level <= 1;
            //return checkModulePermission($user, 'users');
        });
        Gate::define('clients', function ($user) {
            return checkModulePermission($user, 'clients');
        });
        Gate::define('gdprconsents', function ($user) {
            return checkModulePermission($user, 'gdprconsents');
        });
        Gate::define('transferrequests', function ($user) {
            return checkModulePermission($user, 'transferrequests');
        });
        Gate::define('btlconsents', function ($user) {
            return checkModulePermission($user, 'btlconsents');
        });
        Gate::define('sdltdisclaimers', function ($user) {
            return checkModulePermission($user, 'sdltdisclaimers');
        });
        Gate::define('businessterms', function ($user) {
            return checkModulePermission($user, 'businessterms');
        });
        Gate::define('businesstermsprotection', function ($user) {
            return checkModulePermission($user, 'businesstermsprotection');
        });
        Gate::define('quotes', function ($user) {
            return checkModulePermission($user, 'quotes');
        });
        Gate::define('calculators', function ($user) {
            return checkModulePermission($user, 'calculators');
        });
        Gate::define('eligibilitystatements', function ($user) {
            return checkModulePermission($user, 'eligibilitystatements');
        });
    }
}
