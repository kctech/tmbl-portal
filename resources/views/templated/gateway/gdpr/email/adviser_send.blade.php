@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader')
    The consent request(s) have been sent to the client: {{$details['fields']['client']->email}}
@endsection

@section('content')

    <p>Hi {{$details['fields']['adviser']->first_name}}</p>
                                            
    <p>The consent request have been sent to the clients below. If they do not receive them, their unique links are also below:</p>
    
    <p>
        <strong>Type:</strong> {{$details['fields']['record']->consent_type}}<br />
        <strong><u>{{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}</u></strong><br />
        <strong>Email:</strong> {{$details['fields']['client']->email}}<br />
        <strong>Link:</strong> {{route('gdpr-consent.respond',[$details['fields']['client']->uid,$details['fields']['record']->id])}}<br />
    </p>
    
    <br><br>

@endsection

@section('footer')
    Do not reply to this email, it will not be answered.
@endsection
