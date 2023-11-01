@extends('layouts.email.banner')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'TMBL | Consent')

@section('title', 'TMBL | Consent')

@section('preheader', 'Please consent to us using your data.')

@section('header', 'Please give us consent...')

@section('link', signedRoute('gdpr-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('banner_img','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/tmbl_header_banner.jpg')

@section('banner_img_m','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/tmbl_header_banner_m.jpg')

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}</p>

    <p>The new data protection laws require us to gain your consent before we pass your details to
        The Mortgage Broker Protection Ltd so they can advise you on protection.</p>

    <p>Please follow the link below to consent to the processing of sensitive personal data as far as it
        is necessary for the services of mortgage and protection.</p>

    <table align="center" width="50%">
        <tr>
            <td style="background-color: #2e96d8; border-radius: 5px 5px 5px 5px; padding: 10px; color: #ffffff; text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 12px;" class="button">
                <a href="{{signedRoute('gdpr-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" style="color: #ffffff; text-decoration:none; font-weight:bold; display: block;">
                    Click here to give us consent
                </a>
            </td>
        </tr>
    </table>

    <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

    <br><br>

@endsection

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('gdpr-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
