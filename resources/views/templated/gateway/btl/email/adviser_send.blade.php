@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader')
    The BTL Confirmation has been sent to the client: {{$details['fields']['client']->email}}
@endsection

@section('content')

    <p>Hi {{$details['fields']['adviser']->first_name}}</p>
                                            
    <p>The BTL Confirmation has been sent to the client below. If they do not receive it, their unique links are also below:</p>
    
    <p>
        <strong>Type:</strong> {{$details['fields']['record']->consent_type}}<br />
        <strong><u>{{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}</u></strong><br />
        <strong>Email:</strong> {{$details['fields']['client']->email}}<br />
        <strong>Link:</strong> {{signedRoute('gdpr-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}<br />
    </p>
    
    <br><br>

@endsection

@section('footer')
    Do not reply to this email, it will not be answered.
@endsection
