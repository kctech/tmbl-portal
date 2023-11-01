@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader')
    (from Openwork)Reponse from {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}} has come in
@endsection

@section('content')

    <p>Hi {{$details['fields']['adviser']->first_name}}</p>
                                            
    <p>Client Transfer (from Gateway Mortgages Ltd) reponse from {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}} has come in:</p>
    
    <p>&ldquo;I hereby authorise you to move the data and information you hold for me / us under Openwork
     to company Gateway Mortgages Ltd with The Mortgage Advice Bureau&ldquo;</p>

    <p>
        <strong>Consent Given:</strong> {{$details['fields']['record']->consent}}<br />
        <strong>Response Date:</strong> {{$details['fields']['record']->updated_at}}<br />
    </p>
    
    <p>
        <strong>Client Info</strong>
    </p>
    <p>
        <strong>Email Address:</strong> {{$details['fields']['client']->email}}<br />
        @if($details['fields']['client']->tel != "") <strong>Tel:</strong> {{$details['fields']['client']->tel}}<br /> @endif
    </p>
    
    {{--<p>
        <strong>Marketing Preferences</strong>
    </p>
    <p>
        <strong>Accept Email:</strong> {{$details['fields']['client']->mkt_email_consent}}<br />
        <strong>Accept Phone:</strong> {{$details['fields']['client']->mkt_phone_consent}}<br />
        <strong>Accept SMS:</strong> {{$details['fields']['client']->mkt_sms_consent}}<br />
        <strong>Accept Post:</strong> {{$details['fields']['client']->mkt_post_consent}}<br />
    </p>--}}
    
    <br><br>

@endsection

@section('footer')
    You can reply to this email and it will be sent to Client {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}  ({{$details['fields']['client']->email}}).
@endsection
