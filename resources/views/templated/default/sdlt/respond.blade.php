@extends('layouts.frontend')

@section('title') SDLT Disclaimer @endsection
@section('pagetitle') Please confirm your understanding @endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <p>Dear {{ $record->client->first_name }},</p>

                    <p>Please confirm that you have read and understood the following by digitally signing abd submitting below.</p>

                    <p>
                        <h3>Your Adviser</h3>
                        <strong>{{ $record->user->first_name }} {{ $record->user->last_name }}</strong><br />
                        <small>{{ $record->user->email }}</small>
                    </p>
                    <br />

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h3>There's a probelm with your submission:</h3>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ signedRoute('sdlt-consent.respond-save', ['code'=>$record->client->uid, 'id'=>$record->id]) }}">
                        @csrf
                        {{ method_field('POST') }}
                        <input name="user_id" type="hidden" value="{{ $record->user_id }}" />
                        <input name="client_id" type="hidden" value="{{ $record->client_id }}" />
                        <input name="consent_additional" type="hidden" value="" />

                        <div class="row mb-3 d-flex align-items-stretch">

                            <div class="col-sm-12">
                                <div class="card h-100">
                                    <div class="card-body h-100">
                                        <h2>SDLT Disclaimer</h2>
                                        <p>Please be aware that the government Stamp Duty incentive has been extended until 30<sup>th</sup> June 2021.</p>
                                        <p>Due to the unpredictable demand in the current mortgage market and longer processing times with Lenders, mortgage applications are subject to longer completion times. We are always working hard to ensure a speedy process but are not responsible for lender capacity and delays.</p>
                                        <p>Therefore, should for any reason your mortgage application take you past the 30<sup>th</sup> June 2021 deadline, then you will be liable for the new Stamp Duty Tax rates effective from this date.</p>
                                        <p>These can be found via the following link:<br /><a href="https://www.tax.service.gov.uk/calculate-stamp-duty-land-tax/#/intro" target="_blank" rel="noopener">https://www.tax.service.gov.uk/calculate-stamp-duty-land-tax/#/intro</a></p>
                                        <p>All mortgage application costs you have paid will be non-refundable, even in the scenario that you are not able to pay for the total Stamp Duty to complete your purchase.</p>
                                        <p>As we get closer to the deadline, you may need to consider making provisions for any additional costs should the deadline be missed.</p>
                                        <p class="mb-0">For further information please contact your solicitor or a member of our team.</p>
                                    </div>  
                                    <div class="card-footer bg-info text-white">
                                        <div class="form-group mb-0 py-3 {{ $errors->has('consent') ? ' is-invalid' : '' }}">
                                            <div class="custom-control custom-control-lg custom-radio">
                                                <input type="radio" name="consent" id="consent" class="custom-control-input" value="Y"" /> 
                                                <label class="custom-control-label" for="consent">I understand.</label>
                                            </div>
                                            @if ($errors->has('consent'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('consent') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{--<div class="row mb-3">

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card card-body bg-light">
                                        <h3>Please can we stay in touch</h3>
                                        <p>We at The Mortgage Broker would like to stay in touch with you about products, news and special offers, which we do via services like our monthly newsletter.</p>
                                        <p>Please select from the following options how best to stay in touch.</p>
                                        <div class="form-group {{ $errors->has('mkt_email_consent') ? ' is-invalid' : '' }}">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="mkt_email_consent" id="mkt_email_consent" class="custom-control-input" value="Y" /> 
                                                <label class="custom-control-label" for="mkt_email_consent">Consent to emails.</label>
                                            </div>

                                            @if ($errors->has('mkt_email_consent'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mkt_email_consent') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group {{ $errors->has('mkt_phone_consent') ? ' is-invalid' : '' }}">

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="mkt_phone_consent" id="mkt_phone_consent" class="custom-control-input" value="Y" /> 
                                                <label class="custom-control-label" for="mkt_phone_consent">Consent to phone calls.</label>
                                            </div>

                                            @if ($errors->has('mkt_phone_consent'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mkt_phone_consent') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group {{ $errors->has('mkt_sms_consent') ? ' is-invalid' : '' }}">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="mkt_sms_consent" id="mkt_sms_consent" class="custom-control-input" value="Y" /> 
                                                <label class="custom-control-label" for="mkt_sms_consent">Consent to SMS messages.</label>
                                            </div>

                                            @if ($errors->has('mkt_sms_consent'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mkt_sms_consent') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group {{ $errors->has('mkt_post_consent') ? ' is-invalid' : '' }}">
    
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="mkt_post_consent" id="mkt_post_consent" class="custom-control-input" value="Y" /> 
                                                <label class="custom-control-label" for="mkt_post_consent">Consent to post.</label>
                                            </div>

                                            @if ($errors->has('mkt_post_consent'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mkt_post_consent') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>--}}

                        <div class="form-group row mb-0">
                            <div class="col-md-4 offset-md-4">
                                <button type="submit" class="btn btn-lg btn-block btn-primary">
                                    {{ __('Submit Response') }}
                                </button>
                            </div>
                        </div>

                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        app.ticktock_frontend(30,0.5);
    </script>
@endpush
