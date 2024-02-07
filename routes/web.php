<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::resource('things', 'ThingsController');
=
Route::get('/things', 'ThingsController@index');
Route::get('/things/create', 'ThingsController@create');
Route::get('/things/{id}', 'ThingsController@show');
Route::post('/things', 'ThingsController@store');
Route::get('/things/{id}/edit', 'ThingsController@edit');
Route::patch('/things/{id}', 'ThingsController@update');
Route::delete('/things/{id}', 'ThingsController@destroy');
*/

/*use Illuminate\Support\Facades\Hash;*/
Route::get('/mkpw', function() {
    return Hash::make('qwerty1234');
});

Route::get('/', 'HomeController@index')->name('home');
Route::get('/unverified', 'HomeController@unverified')->name('unverified');

Route::get('/cron', function () {
    Artisan::call('queue:work --queue=adviseremails,clientemails --tries=1 --timeout=30 --sleep=5');
    abort(200);
    return "Queue started";
});

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

//Route::get('generate-pdf','PdfController@generatePDF');
Route::get('pdf','PdfController@testGeneratePDF');

Route::get('/admin', function () { return view('auth.login'); })->name('admin');
Route::prefix('admin')->group(function () {
    Auth::routes(['verify' => true]);

    Route::group(['middleware' => 'verified'], function() {

        //DASHBOARD
        Route::get('/dashboard', 'DashboardController@index')->name('admin.dashboard');

        //LEADS
        Route::get('/leads/dashboard', 'LeadController@index')->name('leads.index');
        Route::get('/leads/manage', 'LeadController@manage')->name('leads.manage');
        Route::get('/leads/sources', 'LeadController@sources')->name('leads.sources');

        //USERS
        Route::post('/users/search', 'UserController@index')->name('users.search');
        Route::post('/users/find', 'UserController@find')->name('users.find');
        Route::get('/users/resend-login/{id}', 'UserController@resendUserDetails')->name('users.resend-login')->where('id', '[0-9]+');
        Route::post('/users/check-duplicate', 'UserController@checkDuplicate')->name('users.duplicate');
        Route::get('/users/impersonate/{id}', 'UserController@impersonate')->name('users.impersonate')->where('id', '[0-9]+');
        Route::get('/users/stop-impersonating', 'UserController@stopImpersonating')->name('users.stop-impersonating');
        Route::get('/users/reinstate/{id}', 'UserController@reinstate')->name('users.reinstate')->where('id', '[0-9]+');
        Route::resource('users', 'UserController');

        //CLIENTS
        Route::get('/clients/search', 'ClientsController@index')->name('clients.search');
        Route::post('/clients/find', 'ClientsController@find')->name('clients.find');
        Route::get('/clients/resend-login/{id}', 'ClientsController@resendClientDetails')->name('clients.resend-login')->where('id', '[0-9]+');
        Route::post('/clients/check-linked', 'ClientsController@checkLinked')->name('clients.linked');
        Route::post('/clients/check-duplicate', 'ClientsController@checkDuplicate')->name('clients.duplicate');
        Route::resource('clients', 'ClientsController');

        //GDPR
        Route::post('/gdpr-consent/search', 'GdprConsentController@index')->name('gdpr-consent.search');
        Route::get('/gdpr-consent/resend-client/{id}', 'GdprConsentController@resendClient')->name('gdpr-consent.resend-client')->where('id', '[0-9]+');
        Route::get('/gdpr-consent/resend-adviser/{id}', 'GdprConsentController@resendAdviser')->name('gdpr-consent.resend-adviser')->where('id', '[0-9]+');
        Route::resource('gdpr-consent', 'GdprConsentController');

        //BTL
        Route::post('/btl-consent/search', 'BtlConsentController@index')->name('btl-consent.search');
        Route::get('/btl-consent/resend-client/{id}', 'BtlConsentController@resendClient')->name('btl-consent.resend-client')->where('id', '[0-9]+');
        Route::get('/btl-consent/resend-adviser/{id}', 'BtlConsentController@resendAdviser')->name('btl-consent.resend-adviser')->where('id', '[0-9]+');
        Route::resource('btl-consent', 'BtlConsentController');

        //SDLT
        Route::post('/sdlt-consent/search', 'SdltDisclaimerController@index')->name('sdlt-consent.search');
        Route::get('/sdlt-consent/resend-client/{id}', 'SdltDisclaimerController@resendClient')->name('sdlt-consent.resend-client')->where('id', '[0-9]+');
        Route::get('/sdlt-consent/resend-adviser/{id}', 'SdltDisclaimerController@resendAdviser')->name('sdlt-consent.resend-adviser')->where('id', '[0-9]+');
        Route::resource('sdlt-consent', 'SdltDisclaimerController');

        //TRANSFERS
        Route::post('/transfer-request/search', 'ClientTransferConsentController@index')->name('transfer-request.search');
        Route::get('/transfer-request/resend-client/{id}', 'ClientTransferConsentController@resendClient')->name('transfer-request.resend-client')->where('id', '[0-9]+');
        Route::get('/transfer-request/resend-adviser/{id}', 'ClientTransferConsentController@resendAdviser')->name('transfer-request.resend-adviser')->where('id', '[0-9]+');
        Route::resource('transfer-request', 'ClientTransferConsentController');

        //TERMS
        Route::get('/terms-consent/search', 'TermsConsentController@index')->name('terms-consent.search');
        Route::post('/terms-consent/search', 'TermsConsentController@index')->name('terms-consent.search');
        Route::get('/terms-consent/resend-client/{id}', 'TermsConsentController@resendClient')->name('terms-consent.resend-client')->where('id', '[0-9]+');
        Route::get('/terms-consent/resend-adviser/{id}', 'TermsConsentController@resendAdviser')->name('terms-consent.resend-adviser')->where('id', '[0-9]+');
        Route::get('/terms-consent/create-protection', 'TermsConsentController@createProtection')->name('terms-consent.create-protection');
        Route::resource('terms-consent', 'TermsConsentController');

        //QUOTES
        Route::get('/quote/copy/{id}', 'QuoteController@copy')->name('quote.copy')->where('id', '[0-9]+');
        Route::post('/quote/search', 'QuoteController@index')->name('quote.search');
        Route::get('/quote/resend-client/{id}', 'QuoteController@resendClient')->name('quote.resend-client')->where('id', '[0-9]+');
        Route::get('/quote/resend-adviser/{id}', 'QuoteController@resendAdviser')->name('quote.resend-adviser')->where('id', '[0-9]+');
        Route::resource('quote', 'QuoteController');

        //ELIGIBILITY STATEMENTS
        Route::get('/eligibility-statements/copy/{id}', 'EligibilityStatementController@copy')->name('eligibility-statements.copy')->where('id', '[0-9]+');
        Route::post('/eligibility-statements/search', 'EligibilityStatementController@index')->name('eligibility-statements.search');
        Route::get('/eligibility-statements/resend-client/{id}', 'EligibilityStatementController@resendClient')->name('eligibility-statements.resend-client')->where('id', '[0-9]+');
        Route::get('/eligibility-statements/resend-adviser/{id}', 'EligibilityStatementController@resendAdviser')->name('eligibility-statements.resend-adviser')->where('id', '[0-9]+');
        Route::resource('eligibility-statements', 'EligibilityStatementController');

        //CALCULATORS
        Route::get('/calculators', function () { return view('admin.calcs.index'); })->name('calculators.index');

        //ROLES
        Route::resource('roles', 'RoleController');

        //Route::get('/quote', function () { return view('admin.quote.view'); })->name('quote.index');

    });

});

Route::prefix('clients')->group(function () {

    Route::get('/', function () { return view('clients.dashboard'); })->name('clients.dashboard');

});

Route::get('/thanks', function () { return view('generic.thanks'); })->name('thanks');
Route::get('/sorry', function () { return view('generic.sorry'); })->name('sorry');
Route::get('/idle', function () { return view('generic.idle'); })->name('idle');

//signed routes
Route::group(['middleware' => 'signed'], function() {
    //Route::get('/gdpr-consent/respond/{code}/{id}', 'GdprConsentController@respond')->name('gdpr-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/gdpr-consent/respond/{code}/{id}', 'GdprConsentController@respond')->name('gdpr-consent.respond-save')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');

    //Route::get('/btl-consent/respond/{code}/{id}', 'BtlConsentController@respond')->name('btl-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/btl-consent/respond/{code}/{id}', 'BtlConsentController@respond')->name('btl-consent.respond-save')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');

    //Route::get('/sdlt-consent/respond/{code}/{id}', 'SdltDisclaimerController@respond')->name('sdlt-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/sdlt-consent/respond/{code}/{id}', 'SdltDisclaimerController@respond')->name('sdlt-consent.respond-save')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');

    //Route::get('/transfer-request/respond/{code}/{id}', 'ClientTransferConsentController@respond')->name('transfer-request.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/transfer-request/respond/{code}/{id}', 'ClientTransferConsentController@respond')->name('transfer-request.respond-save')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');

    //Route::get('/terms-consent/respond/{code}/{id}', 'TermsConsentController@respond')->name('terms-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/terms-consent/respond/{code}/{id}', 'TermsConsentController@respond')->name('terms-consent.respond-save')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/terms-consent/download/{code}', 'TermsConsentController@downloadPDF')->name('terms-consent.download')->where('code', '[A-za-z0-9]+');

    //Route::get('/quote/respond/{code}/{id}', 'QuoteController@respond')->name('quote.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
    Route::post('/quote/download/{code}', 'QuoteController@downloadPDF')->name('quote.download')->where('code', '[A-za-z0-9]+');
});

//temporarily allow unsigned routes
Route::get('/gdpr-consent/respond/{code}/{id}', 'GdprConsentController@respond')->name('gdpr-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
Route::get('/btl-consent/respond/{code}/{id}', 'BtlConsentController@respond')->name('btl-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
Route::get('/sdlt-consent/respond/{code}/{id}', 'SdltDisclaimerController@respond')->name('sdlt-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
Route::get('/transfer-request/respond/{code}/{id}', 'ClientTransferConsentController@respond')->name('transfer-request.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
Route::get('/terms-consent/respond/{code}/{id}', 'TermsConsentController@respond')->name('terms-consent.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');
Route::get('/quote/respond/{code}/{id}', 'QuoteController@respond')->name('quote.respond')->where('code', '[A-za-z0-9]+')->where('id', '[0-9]+');

//SSO
Route::get('/platform/{provider}/auth','\App\Http\Controllers\Auth\LoginController@ssoAuthenticate')->where('provider','[A-Za-z]+');
