@extends('layouts.frontend')

@section('title') Business Terms Consent @endsection
@section('pagetitle') Please complete the form below... @endsection

@section('content')
<div class="container">

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

    @if($record->signature == "")

        <div class="row d-flex align-items-stretch mb-3">
            <div class="col-md-9">
                <div class="card h-100">
                    <div class="card-body h-100">

                        <p>Dear {{ $record->client->first_name }} {{ $record->client->last_name }},</p>

                        <p>Please find below our Privacy Notice, Terms of Business and Our Promise to You.</p>

                        <p>These documents are important as they explain what we do with and how we hold your personal information
                            and also confirm the basis on which we will provide you with services.</p>

                        <p>I will need you to read Privacy Notice, Terms of Business and Our Promise,
                            then confirm you've done so and that you agree via the form below.
                            I'd also like you to tell me how best to communicate with you, the marketing preferences are optional.
                            If you are not able to complete the form, please contact me and I will send through the documents
                            for you to print off and scan back a signed copy.</p>

                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Your Adviser</h4>
                    </div>
                    <div class="card-body h-100 text-center">
                        <span class="fa-stack fa-3x">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-user-tie fa-stack-1x fa-inverse"></i>
                        </span>

                        <h3>{{ $record->user->first_name }} {{ $record->user->last_name }}</h3>
                        <strong>{{ $record->user->email }}</strong><br />
                        <strong>{{ $record->user->tel }}</strong>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">

            <!-- Multi step form -->
            <section id="multi_step_form" class="multi_step_form">

                <form id="msform" method="POST" action="{{ signedRoute('terms-consent.respond-save', ['code'=>$record->client->uid, 'id'=>$record->id]) }}">
                    @csrf
                    {{ method_field('POST') }}
                    <input name="user_id" type="hidden" value="{{ $record->user_id }}" />
                    <input name="client_id" type="hidden" value="{{ $record->client_id }}" />

                    <!-- progressbar -->
                    <ul id="progressbar" class="w-100 d-flex flex-fill p-0 mb-4">
                        <li class="active flex-grow-1"><i class="icon far fa-shield-check"></i>Privacy Notice</li>
                        <li class="flex-grow-1"><i class="icon far fa-briefcase"></i>Terms of Businesss</li>
                        <li class="flex-grow-1"><i class="icon far fa-tasks"></i>Our Promise</li>
                        {{--<li><i class="icon far fa-user-check"></i>Your Response</li>--}}
                    </ul>

                    <div class="card-body" style="position: relative;">

                        <!-- privacy notice -->
                        <fieldset>
                            <div class="w-100 d-flex flex-column justify-content-center align-items-center mb-3">
                                <h2>Privacy Notice</h2>
                                <h6>Please read this, scroll to the bottom to proceed to the next step.</h6>
                            </div>

                            <div class="card bg-light mb-3">
                                <div class="card-body scroll-box">

                                    @include('templated.'.$record->user->account->viewset.'.terms.blocks.privacy')

                                    <div class="card my-3">
                                        <div class="card-body bg-primary text-white">
                                            <div class="form-group mb-0 py-3 {{ $errors->has('privacy_consent') ? ' is-invalid' : '' }}">
                                                <div class="custom-control custom-control-lg custom-checkbox">
                                                    <input type="checkbox" name="privacy_consent" id="privacy_consent" class="custom-control-input" value="Y" required />
                                                    <label class="custom-control-label" for="privacy_consent">I agree to the privacy notice.</label>
                                                </div>
                                                @if ($errors->has('privacy_consent'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('privacy_consent') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="w-100 d-flex justify-content-center">
                                <button type="button" class="btn btn-lg btn-primary next privacy-next">Accept &amp; Continue</button>
                            </div>
                        </fieldset>

                        <!-- terms of business -->
                        <fieldset>
                            <div class="w-100 d-flex flex-column justify-content-center align-items-center mb-3">
                                <h2>Terms of Business</h2>
                                <h6>Our Terms of Business document outlines the services we will provide. It is an important document, so please ensure that you take time to read it carefully.</h6>
	                            <p>If there is anything in this document that you are unsure about, please don’t hesitate to get in touch with your adviser.</p>

                            </div>

                            <div class="card bg-light mb-3">
                                <div class="card-body no-scroll-box">

                                    @include('templated.'.$record->user->account->viewset.'.terms.blocks.business', $record)

                                </div>
                            </div>

                            <div class="w-100 d-flex justify-content-center">
                                <button type="button" class="btn btn-lg btn-outline-dark previous">Back</button>
                                &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-lg btn-primary next">Continue</button>
                            </div>
                        </fieldset>

                        <!-- our primise to you -->
                        <fieldset>
                            <div class="w-100 d-flex flex-column justify-content-center align-items-center mb-3">
                                <h3>Our Promise to You</h3>
                                <h6>This establishes the fees and our terms &amp; conditions that are relevant to the work we will do for you.</h6>
                            </div>

                            <div class="card bg-light mb-3">
                                <div class="card-body no-scroll-box">

                                    @include('templated.'.$record->user->account->viewset.'.terms.blocks.promise', $record)

                                    <hr class="my-4" />

                                    <div class="row">
                                        <div class="@if($linked->count()>0) col-md-6 @else col-md-12 @endif">
                                            <div class="card mb-3 bg-primary text-white">
                                                <div class="card-header">
                                                    <h3 class="mb-0">
                                                        Communication Preferences
                                                        @if($linked->count()>0)
                                                            <br />For {{$record->client->first_name}} {{$record->client->last_name}}
                                                        @endif
                                                    </h3>
                                                </div>
                                                <div class="card-body h-100">
                                                    <p class="card-text">It is important for us to be able to contact you throughout your application.</p>
                                                    <p class="card-text">Please indicate your preferred method(s) of contact:</p>
                                                </div>
                                                <ul class="list-group list-group-flush">

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('comm_phone_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="comm_phone_consent" id="comm_phone_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->comm_phone_consent)}} />
                                                                <label class="custom-control-label" for="comm_phone_consent">Telephone</label>
                                                            </div>

                                                            @if ($errors->has('comm_phone_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('comm_phone_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('comm_face_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="comm_face_consent" id="comm_face_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->comm_face_consent)}} />
                                                                <label class="custom-control-label" for="comm_face_consent">Face to face</label>
                                                            </div>

                                                            @if ($errors->has('comm_face_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('comm_face_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('comm_sms_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="comm_sms_consent" id="comm_sms_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->comm_sms_consent)}} />
                                                                <label class="custom-control-label" for="comm_sms_consent">SMS / Text Message</label>
                                                            </div>

                                                            @if ($errors->has('comm_sms_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('comm_sms_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('comm_email_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="comm_email_consent" id="comm_email_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->comm_email_consent)}} />
                                                                <label class="custom-control-label" for="comm_email_consent">Email</label>
                                                            </div>

                                                            @if ($errors->has('comm_email_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('comm_email_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('comm_thirdparty_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="comm_thirdparty_consent" id="comm_thirdparty_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->comm_thirdparty_consent)}} />
                                                                <label class="custom-control-label" for="comm_thirdparty_consent">Third Party Intermediaries</label>
                                                            </div>

                                                            @if ($errors->has('comm_thirdparty_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('comm_thirdparty_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('comm_other_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="comm_other_consent" id="comm_other_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->comm_other_consent)}} />
                                                                <label class="custom-control-label" for="comm_other_consent">Other</label>
                                                            </div>

                                                            @if ($errors->has('comm_other_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('comm_other_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="card mb-3 bg-primary text-white">
                                                <div class="card-header">
                                                    <h3 class="mb-0">
                                                        Marketing Preferences
                                                        @if($linked->count()>0)
                                                            <br />For {{$record->client->first_name}} {{$record->client->last_name}}
                                                        @endif
                                                    </h3>
                                                </div>
                                                <div class="card-body h-100">
                                                    <p class="card-text">We believe it is important to provide you with an ongoing service. Part of the service we offer is to send information that
                                                        may be of interest to you. If you would like to take advantage of this aspect of our service please confirm,
                                                        by ticking the following type(s) of contact acceptable to you:</p>
                                                </div>
                                                <ul class="list-group list-group-flush">

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_phone_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_phone_consent" id="mkt_phone_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_phone_consent)}} />
                                                                <label class="custom-control-label" for="mkt_phone_consent">Telephone</label>
                                                            </div>

                                                            @if ($errors->has('mkt_phone_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_phone_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_face_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_face_consent" id="mkt_face_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_face_consent)}} />
                                                                <label class="custom-control-label" for="mkt_face_consent">Face to face</label>
                                                            </div>

                                                            @if ($errors->has('mkt_face_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_face_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_sms_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_sms_consent" id="mkt_sms_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_sms_consent)}} />
                                                                <label class="custom-control-label" for="mkt_sms_consent">SMS / Text Message</label>
                                                            </div>

                                                            @if ($errors->has('mkt_sms_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_sms_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_email_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_email_consent" id="mkt_email_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_email_consent)}} />
                                                                <label class="custom-control-label" for="mkt_email_consent">Email</label>
                                                            </div>

                                                            @if ($errors->has('mkt_email_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_email_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_thirdparty_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_thirdparty_consent" id="mkt_thirdparty_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_thirdparty_consent)}} />
                                                                <label class="custom-control-label" for="mkt_thirdparty_consent">Third Party Intermediaries</label>
                                                            </div>

                                                            @if ($errors->has('mkt_thirdparty_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_thirdparty_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_other_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_other_consent" id="mkt_other_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_other_consent)}} />
                                                                <label class="custom-control-label" for="mkt_other_consent">Other</label>
                                                            </div>

                                                            @if ($errors->has('mkt_other_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_other_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_post_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_post_consent" id="mkt_post_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_post_consent)}} />
                                                                <label class="custom-control-label" for="mkt_post_consent">Post</label>
                                                            </div>

                                                            @if ($errors->has('mkt_post_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_post_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    <li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_automatedcall_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_automatedcall_consent" id="mkt_automatedcall_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_automatedcall_consent)}} />
                                                                <label class="custom-control-label" for="mkt_automatedcall_consent">Automated Call</label>
                                                            </div>

                                                            @if ($errors->has('mkt_automatedcall_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_automatedcall_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>

                                                    {{--<li class="list-group-item bg-primary">
                                                        <div class="form-group mb-0 {{ $errors->has('mkt_web_consent') ? ' is-invalid' : '' }}">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="mkt_web_consent" id="mkt_web_consent" class="custom-control-input" value="Y" {{checked('Y', $record->client->mkt_web_consent)}} />
                                                                <label class="custom-control-label" for="mkt_web_consent">Website Enquiry</label>
                                                            </div>

                                                            @if ($errors->has('mkt_web_consent'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('mkt_web_consent') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>--}}

                                                </ul>
                                            </div>
                                        </div>
                                        @if($linked->count()>0)
                                            @foreach($linked as $link)
                                                <div class="col-md-6">
                                                    <input name="client_id_2" type="hidden" value="{{ $link->id }}" />
                                                    <div class="card mb-3 bg-primary text-white">
                                                        <div class="card-header">
                                                            <h3 class="mb-0">
                                                                Communication Preferences
                                                                <br />For {{$link->first_name}} {{$link->last_name}}
                                                            </h3>
                                                        </div>
                                                        <div class="card-body h-100">
                                                            <p class="card-text">It is important for us to be able to contact you throughout your application.</p>
                                                            <p class="card-text">Please indicate your preferred method(s) of contact:</p>
                                                        </div>
                                                        <ul class="list-group list-group-flush">

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('comm_phone_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="comm_phone_consent_2" id="comm_phone_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->comm_phone_consent)}} />
                                                                        <label class="custom-control-label" for="comm_phone_consent_2">Telephone</label>
                                                                    </div>

                                                                    @if ($errors->has('comm_phone_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('comm_phone_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('comm_face_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="comm_face_consent_2" id="comm_face_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->comm_face_consent)}} />
                                                                        <label class="custom-control-label" for="comm_face_consent_2">Face to face</label>
                                                                    </div>

                                                                    @if ($errors->has('comm_face_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('comm_face_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('comm_sms_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="comm_sms_consent_2" id="comm_sms_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->comm_sms_consent)}} />
                                                                        <label class="custom-control-label" for="comm_sms_consent_2">SMS / Text Message</label>
                                                                    </div>

                                                                    @if ($errors->has('comm_sms_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('comm_sms_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('comm_email_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="comm_email_consent_2" id="comm_email_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->comm_email_consent)}} />
                                                                        <label class="custom-control-label" for="comm_email_consent_2">Email</label>
                                                                    </div>

                                                                    @if ($errors->has('comm_email_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('comm_email_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('comm_thirdparty_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="comm_thirdparty_consent_2" id="comm_thirdparty_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->comm_thirdparty_consent)}} />
                                                                        <label class="custom-control-label" for="comm_thirdparty_consent_2">Third Party Intermediaries</label>
                                                                    </div>

                                                                    @if ($errors->has('comm_thirdparty_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('comm_thirdparty_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('comm_other_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="comm_other_consent_2" id="comm_other_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->comm_other_consent)}} />
                                                                        <label class="custom-control-label" for="comm_other_consent_2">Other</label>
                                                                    </div>

                                                                    @if ($errors->has('comm_other_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('comm_other_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="card mb-3 bg-primary text-white">
                                                        <div class="card-header">
                                                            <h3 class="mb-0">
                                                                Marketing Preferences
                                                                <br />For {{$link->first_name}} {{$link->last_name}}
                                                            </h3>
                                                        </div>
                                                        <div class="card-body h-100">
                                                            <p class="card-text">We believe it is important to provide you with an ongoing service. Part of the service we offer is to send information that
                                                                may be of interest to you. If you would like to take advantage of this aspect of our service please confirm,
                                                                by ticking the following type(s) of contact acceptable to you:</p>
                                                        </div>
                                                        <ul class="list-group list-group-flush">


                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_face_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_face_consent_2" id="mkt_face_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_face_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_face_consent_2">Face to face</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_face_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_face_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_phone_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_phone_consent_2" id="mkt_phone_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_phone_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_phone_consent_2">Telephone</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_phone_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_phone_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_sms_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_sms_consent_2" id="mkt_sms_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_sms_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_sms_consent_2">SMS / Text Message</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_sms_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_sms_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_email_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_email_consent_2" id="mkt_email_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_email_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_email_consent_2">Email</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_email_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_email_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_thirdparty_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_thirdparty_consent_2" id="mkt_thirdparty_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_thirdparty_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_thirdparty_consent_2">Third Party Intermediaries</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_thirdparty_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_thirdparty_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_other_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_other_consent_2" id="mkt_other_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_other_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_other_consent_2">Other</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_other_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_other_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_post_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_post_consent_2" id="mkt_post_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_post_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_post_consent_2">Post</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_post_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_post_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_automatedcall_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_automatedcall_consent_2" id="mkt_automatedcall_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_automatedcall_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_automatedcall_consent_2">Automated Call</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_automatedcall_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_automatedcall_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>

                                                            {{--<li class="list-group-item bg-primary">
                                                                <div class="form-group mb-0 {{ $errors->has('mkt_web_consent_2') ? ' is-invalid' : '' }}">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="mkt_web_consent_2" id="mkt_web_consent_2" class="custom-control-input" value="Y" {{checked('Y', $link->mkt_web_consent)}} />
                                                                        <label class="custom-control-label" for="mkt_web_consent_2">Website Enquiry</label>
                                                                    </div>

                                                                    @if ($errors->has('mkt_web_consent_2'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('mkt_web_consent_2') }}</strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </li>--}}

                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <hr class="my-4" />

                                    <h2>Agreement to ongoing service and Re-engagement</h2>
                                    <p>We would also like to keep in touch to review your mortgage, insurance needs and current arrangements,
                                        in particular when your mortgage product is nearing expiry. This is important as, for example, it will
                                        be an opportunity to check that you are not paying more than you need to and whether your existing arrangements
                                        are still appropriate as your circumstances and needs change.</p>

                                    <p>You may withdraw from these arrangements at any time by contacting us by the
                                        following e-mail or in writing at the address shown overleaf.</p>

                                    <table class="table table-bordered bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bg-dark text-white">
                                                    Email
                                                </td>
                                                <td>
                                                    {{ $record->user->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-dark text-white">
                                                    Address
                                                </td>
                                                <td>
                                                    8 Steel Close, Eaton Socon, St Neots, PE19 8TT
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <hr class="my-4" />

                                    <h2>Fees and Costs explained</h2>
                                    <table class="table table-bordered bg-white">
                                        <tbody>
                                            <tr>
                                                <td class="bg-dark text-white">
                                                    Brief outline of service provided
                                                </td>
                                                <td>
                                                    {{ $record->description }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-dark text-white">
                                                    Method of payment for arranging this mortgage
                                                </td>
                                                <td>
                                                    @if($record->amount == 0)
                                                        Commission from lender
                                                    @else
                                                        Both commission from lender and fee paid by you
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-dark text-white">
                                                    Fee paid by you for arranging this mortgage
                                                </td>
                                                <td>
                                                    @if($record->amount == 0)
                                                        No fee
                                                    @else
                                                        @if($record->type == 'Percentage')
                                                            {{ $record->amount }}%
                                                        @else
                                                            &pound;{{ number_format($record->amount, 2, '.', '') }}
                                                        @endif
                                                        @if($record->timing == 'Application')
                                                            paid on application of the mortgage
                                                        @elseif($record->timing == 'Offer')
                                                            paid on receipt of the mortgage offer
                                                        @elseif($record->timing == 'Completion')
                                                            paid on completion of the mortgage (i.e. when the funds are drawn down)
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-dark text-white">
                                                    Fee paid by you for any Protection Advice
                                                </td>
                                                <td>
                                                    No fee is charged to you
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="card mb-3 bg-primary text-white">
                                        <div class="card-header">
                                            <ul>
                                                <li>I / We confirm that I / we have received a copy of the Terms of Business document, the Client Privacy Notice, any relevant guides, and agree to the terms therein</li>
                                                <li>I / We give you authority to act on my/our behalf as per the terms & conditions defined.</li>
                                                @if($record->amount > 0)
                                                    <li>I authorise {{$record->user->account->name }} to send instructions to the financial institution that issued my card to take payments from my card account,
                                                    in accordance with the terms of my agreement with you.</li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-0 py-3 {{ $errors->has('terms_consent') ? ' is-invalid' : '' }}">
                                                <div class="custom-control custom-control-lg custom-checkbox">
                                                    <input type="checkbox" name="terms_consent" id="terms_consent" class="custom-control-input" value="Y" required />
                                                    <label class="custom-control-label" for="terms_consent">I agree to the terms above and the fee structure.</label>
                                                </div>
                                                @if ($errors->has('terms_consent'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('terms_consent') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 bg-primary text-white">
                                        <div class="card-header">
                                            {{ $record->client->first_name }} {{ $record->client->last_name }}
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-0 py-3 {{ $errors->has('consent') ? ' is-invalid' : '' }}">
                                                <label for="signature"><h3 class="mb-0">Your Signature</h3></label>
                                                <input type="text" name="signature" id="signature" class="form-control" required />
                                                <small id="signatureHelpBlock" class="form-text">
                                                    By typing your name, this acts as your signature
                                                </small>
                                                @if ($errors->has('signature'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('signature') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            {{ date('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                </div>
                            </div>



                            <div class="w-100 d-flex justify-content-center">
                                <button type="button" class="btn btn-lg btn-outline-dark previous">Back</button>
                                &nbsp;&nbsp;&nbsp;
                                <button type="submit" class="btn btn-lg btn-primary">
                                    {{ __('Submit Response') }}
                                </button>
                            </div>
                        </fieldset>

                    </div>

                </form>
            </section>
            <!-- End Multi step form -->

        </div>

    @else

        <div class="row d-flex align-items-stretch mb-3">
            <div class="col-md-9">
                <div class="card h-100">
                    <div class="card-body h-100">

                        <p>Looks like you've already responded to this request, if you think this is an error please contact your adviser</p>

                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Your Adviser</h4>
                    </div>
                    <div class="card-body h-100 text-center">
                        <span class="fa-stack fa-3x">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fas fa-user-tie fa-stack-1x fa-inverse"></i>
                        </span>

                        <h3>{{ $record->user->first_name }} {{ $record->user->last_name }}</h3>
                        <strong>{{ $record->user->email }}</strong><br />
                        <strong>{{ $record->user->tel }}</strong>

                    </div>
                </div>
            </div>
        </div>

    @endif
</div>
@endsection

@push('js')
<script>
    app.ticktock_frontend(30,0.5);
    $(document).ready(function(){

        $('.privacy-next').click(function(e){
            $('#privacy_consent').prop('checked', true);
        });

        /*Function Calls*/
        verificationForm ();

        $('input[name="consent"]').change(function(){
            $('input[name="consent_additional"]').val($(this).data("consent-selection"));
        });


    });


    //* Form js
    function verificationForm(){
        //jQuery time
        var current_fs, next_fs, previous_fs; //fieldsets
        var left, opacity, scale; //fieldset properties which we will animate
        var animating; //flag to prevent quick multi-click glitches

        $(".next").click(function () {
            if (animating) return false;
            animating = true;

            current_fs = $(this).parent().parent();
            next_fs = $(this).parent().parent().next();

            //activate next step on progressbar using the index of next_fs
            $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

            //move to window top
            smoothScroll('#multi_step_form');

            //show the next fieldset
            next_fs.show();
            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function (now, mx) {
                    //as the opacity of current_fs reduces to 0 - stored in "now"
                    //1. scale current_fs down to 80%
                    scale = 1 - (1 - now) * 0.2;
                    //2. bring next_fs from the right(50%)
                    left = (now * 50) + "%";
                    //3. increase opacity of next_fs to 1 as it moves in
                    opacity = 1 - now;
                    current_fs.css({
                        'transform': 'scale(' + scale + ')',
                        'position': 'absolute'
                    });
                    next_fs.css({
                        'left': left,
                        'opacity': opacity,
                    });
                },
                duration: 800,
                complete: function () {
                    current_fs.hide();
                    animating = false;
                    current_fs.css({
                        'position': 'relative'
                    });
                },
                //this comes from the custom easing plugin
                easing: 'swing'
            });
        });

        $(".previous").click(function () {
            if (animating) return false;
            animating = true;

            current_fs = $(this).parent().parent();
            previous_fs = $(this).parent().parent().prev();

            //de-activate current step on progressbar
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //move to window top
            smoothScroll('#multi_step_form');

            //show the previous fieldset
            previous_fs.show();
            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function (now, mx) {
                    //as the opacity of current_fs reduces to 0 - stored in "now"
                    //1. scale previous_fs from 80% to 100%
                    scale = 0.8 + (1 - now) * 0.2;
                    //2. take current_fs to the right(50%) - from 0%
                    left = ((1 - now) * 50) + "%";
                    //3. increase opacity of previous_fs to 1 as it moves in
                    opacity = 1 - now;
                    current_fs.css({
                        'left': left
                    });
                    previous_fs.css({
                        'transform': 'scale(' + scale + ')',
                        'opacity': opacity,
                        'position': 'absolute'
                    });
                },
                duration: 800,
                complete: function () {
                    current_fs.hide();
                    animating = false;
                    current_fs.css({
                        'position': 'relative'
                    });
                    previous_fs.css({
                        'position': 'relative'
                    });
                },
                //this comes from the custom easing plugin
                easing: 'swing'
            });
        });
    };

    function smoothScroll(target){
        var $target = $(target);
        $('html, body').animate({
            scrollTop: $target.offset().top
        }, 1000);
    }

</script>
@endpush
