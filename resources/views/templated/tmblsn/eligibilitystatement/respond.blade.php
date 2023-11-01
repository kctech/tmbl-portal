@extends('layouts.frontend')

@section('title') Give Your Consent @endsection
@section('pagetitle') Give Your Consent @endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <p>Thanks you for letting us provide you with some new mortgage options ahead of your current mortgage deal coming to an end.
                    With new GDPR & Data Protection rules we just need your consent for us to transfer your client data that we currently hold under
                    The Mortgage Broker (St Neots) Ltd to The Mortgage Broker Group Ltd as we have had an internal restructuring of the group.</p>

                    <p>Transferring your data in this way will allow us to speed up the process of generating mortgage options for you. However
                    if you do not wish us to transfer your data in this way, that is fine – we will just need to request additional details from you,
                    and we will be in touch with you shortly to discuss this.</p>

                    <p>Further information about how we process client data can be found in our 
                    <a href="https://themortgagebroker.co.uk/links/legal/privacy-and-cookies.html" target="_blank">privacy notice</a></p>

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

                    <form method="POST" action="{{ signedRoute('transfer-request.respond-save', ['code'=>$record->client->uid, 'id'=>$record->id]) }}">
                        @csrf
                        {{ method_field('POST') }}
                        <input name="user_id" type="hidden" value="{{ $record->user_id }}" />
                        <input name="client_id" type="hidden" value="{{ $record->client_id }}" />

                        <div class="row mb-3">

                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card card-body bg-info text-white">
                                        <div class="form-group mb-0 {{ $errors->has('consent') ? ' is-invalid' : '' }}">
                                            <div class="custom-control custom-control-lg custom-checkbox">
                                                <input type="checkbox" name="consent" id="consent" class="custom-control-input" value="Y" /> 
                                                <label class="custom-control-label" for="consent">I give consent.</label>
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