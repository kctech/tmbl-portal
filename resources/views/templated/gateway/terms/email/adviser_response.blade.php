@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader')
    Reponse from {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}} has come in
@endsection

@section('content')

    <p>Hi {{$details['fields']['adviser']->first_name}}</p>
                                            
    <p>Business Terms Response from {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}} has come in:</p>
    
    <p>
        <strong>Privacy Notice:</strong> {{$details['fields']['record']->privacy_consent}}<br />
        <strong>Business Terms:</strong> {{$details['fields']['record']->terms_consent}}<br />
        <strong>Signature:</strong> {{$details['fields']['record']->signature}}<br />
        <strong>Response Date:</strong> {{$details['fields']['record']->updated_at}}<br />
    </p>
    
    <p>
        <strong>Client Info</strong>
    </p>
    <p>
        <strong>Email Address:</strong> {{$details['fields']['client']->email}}<br />
        @if($details['fields']['client']->tel != "") <strong>Tel:</strong> {{$details['fields']['client']->tel}}<br /> @endif
    </p>
    
    <p>
        <strong>Communication Preferences</strong>
    </p>
    <p>
        <strong>Phone:</strong> {{$details['fields']['client']->comm_phone_consent}}<br />
        <strong>Face-to-face:</strong> {{$details['fields']['client']->comm_face_consent}}<br />
        <strong>SMS:</strong> {{$details['fields']['client']->comm_sms_consent}}<br />
        <strong>Email:</strong> {{$details['fields']['client']->comm_email_consent}}<br />
        <strong>3rd Party:</strong> {{$details['fields']['client']->comm_thirdparty_consent}}<br />
        <strong>Other:</strong> {{$details['fields']['client']->comm_other_consent}}<br />
    </p>
    
    <p>
        <strong>Marketing Preferences</strong>
    </p>
    <p>
        <strong>Accept Phone:</strong> {{$details['fields']['client']->mkt_phone_consent}}<br />
        <strong>Accept Face-to-face:</strong> {{$details['fields']['client']->mkt_face_consent}}<br />
        <strong>Accept SMS:</strong> {{$details['fields']['client']->mkt_sms_consent}}<br />
        <strong>Accept Email:</strong> {{$details['fields']['client']->mkt_email_consent}}<br />
        <strong>Accept 3rd Party:</strong> {{$details['fields']['client']->mkt_thirdparty_consent}}<br />
        <strong>Accept Other:</strong> {{$details['fields']['client']->mkt_other_consent}}<br />
        <strong>Accept Post:</strong> {{$details['fields']['client']->mkt_post_consent}}<br />
        <strong>Accept Automated Call:</strong> {{$details['fields']['client']->mkt_automatedcall_consent}}<br />
        {{--<strong>Accept Web:</strong> {{$details['fields']['client']->mkt_web_consent}}<br />--}}
    </p>
    
    <br><br>

@endsection

@section('footer')
    You can reply to this email and it will be sent to Client {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}  ({{$details['fields']['client']->email}}).
@endsection
