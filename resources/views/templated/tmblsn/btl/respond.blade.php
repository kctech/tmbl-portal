@extends('layouts.frontend')

@section('title') Give Your Consent @endsection
@section('pagetitle') Give Your Consent @endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <p>Dear {{ $record->client->first_name }},</p>

                    <p>Thank you for discussing your mortgage requirements with me today. As discussed, I am unable to offer you tax advice therefore
                    I suggest you seek independent tax advice to clearly understand your tax position for your buy to let purchase / remortgage.</p>

                    <p>Before I can proceed with your mortgage, please confirm which of the following is applicable to you:</p>

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

                    <form method="POST" action="{{ signedRoute('btl-consent.respond-save', ['code'=>$record->client->uid, 'id'=>$record->id]) }}">
                        @csrf
                        {{ method_field('POST') }}
                        <input name="user_id" type="hidden" value="{{ $record->user_id }}" />
                        <input name="client_id" type="hidden" value="{{ $record->client_id }}" />
                        <input name="consent_additional" type="hidden" value="unknown" />

                        <div class="row mb-3 d-flex align-items-stretch">

                            <div class="col-sm-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body h-100">
                                        <h2>Buy to let in your Personal Name</h2>

                                        <p class="mb-0">I understand that The Mortgage Broker Group cannot provide me with tax advice, and I have sought advice (or will be seeking advice)
                                        elsewhere relating to my buy to let purchase / remortgage. I am satisfied I clearly understand the implications to me
                                        (and the risks If I don’t seek advice) and am happy to proceed with the mortgage option in my personal name.</p>
                                    </div>  
                                    <div class="card-footer bg-info text-white">
                                        <div class="form-group mb-0 py-3 {{ $errors->has('consent') ? ' is-invalid' : '' }}">
                                            <div class="custom-control custom-control-lg custom-radio">
                                                <input type="radio" name="consent" id="consent_personal" class="custom-control-input" value="Y" data-consent-selection="Personal Name" /> 
                                                <label class="custom-control-label" for="consent_personal">I understand.</label>
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

                            <div class="col-sm-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body h-100">
                                        <h2>Buy to let in a Limited Company Name</h2>

                                        <p class="mb-0">I understand that The Mortgage Broker Group cannot provide me with tax advice, and I have sought advice (or will be seeking advice)
                                        elsewhere relating to my buy to let purchase / remortgage. I am satisfied I clearly understand the implications to me (and the risks If I don’t seek advice),
                                        and am happy to proceed with the mortgage option in my limited company name.</p>
                                    </div>
                                    <div class="card-footer bg-info text-white">
                                        <div class="form-group mb-0 py-3 {{ $errors->has('consent') ? ' is-invalid' : '' }}">
                                            <div class="custom-control custom-control-lg custom-radio">
                                                <input type="radio" name="consent" id="consent_business" class="custom-control-input" value="Y" data-consent-selection="Limited Company Name" /> 
                                                <label class="custom-control-label" for="consent_business">I understand.</label>
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
        $(document).ready(function(){
            $('input[name="consent"]').change(function(){
                console.log('click');
                $('input[name="consent_additional"]').val($(this).data("consent-selection"));
            });
        });
    </script>
@endpush
