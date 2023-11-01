@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'TMBL | Consent')

@section('preheader')
    Reponse from {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}} has come in
@endsection

@section('body')

    <p>Hi {{$details['fields']['adviser']->first_name}}</p>
                                            
    <p>Reponse from {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}} has come in:</p>

    <p>&ldquo;I have read and understood the provisions set out in the Data Protection leaflet
    provided to me by my adviser. Where my health information has been collected for the purposes
    of the provision of life or sickness insurance, I consent to my Adviser and Openwork collecting
    it and processing it in accordance with the provisions set out in that leaflet.&ldquo;</p>
    
    <p>
        <strong>Consent to pass to Openwork:</strong> {{$details['fields']['record']->consent}}<br />
        <strong>Response Date:</strong> {{$details['fields']['record']->updated_at}}<br />
    </p>

    <p>
        <strong>Consent to refer to The Mortgage Broker Ltd:</strong> {{$details['fields']['record']->consent_additional}}<br />
    </p>
    
    <p>
        <strong>Client Info</strong>
    </p>
    <p>
        <strong>Email Address:</strong> {{$details['fields']['client']->email}}<br />
        @if($details['fields']['client']->tel != "") <strong>Tel:</strong> {{$details['fields']['client']->tel}}<br /> @endif
    </p>
    
    <p>
        <strong>Marketing Preferences</strong>
    </p>
    <p>
        <strong>Accept Email:</strong> {{$details['fields']['client']->mkt_email_consent}}<br />
        <strong>Accept Phone:</strong> {{$details['fields']['client']->mkt_phone_consent}}<br />
        <strong>Accept SMS:</strong> {{$details['fields']['client']->mkt_sms_consent}}<br />
        <strong>Accept Post:</strong> {{$details['fields']['client']->mkt_post_consent}}<br />
    </p>
    
    <br><br>

@endsection

@section('footer')
    You can reply to this email and it will be sent to Client {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}  ({{$details['fields']['client']->email}}).
@endsection
