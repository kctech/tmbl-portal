@extends('layouts.email.banner')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader', 'Please consent to us using your data.')

@section('header', 'Please give us consent...')

@section('link', signedRoute('gdpr-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('banner_img','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/gateway_header_banner.jpg')

@section('banner_img_m','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/gateway_header_banner_m.jpg')

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}} {{$details['fields']['client']->last_name}}</p>

    <p>Data Protection laws have changed.</p>

    <p>The link below gives us consent to the processing of sensitive personal data as far as it is necessary for the services
    of mortgage and protection. (So we can provide you quotes based on the information you provide)</p>

    <p>We have updated our <a href="https://www.gatewaymortgagesuk.com/about-us/privacy-policy/" target="_blank">Privacy Notice</a> which can be found on our website.
    This provides information about the nature of our personal and sensitive data processing activities and includes details of
    our retention and deletion policies as well as your rights of access to the personal information that we hold on you.</p>

    <p>Please press the button below to consent to the processing of sensitive personal data as far as it is necessary
    for the services of mortgage and protection.</p>

    <table align="center" width="50%">
        <tr>
            <td style="background-color: #233c7c; border-radius: 5px 5px 5px 5px; padding: 10px; color: #ffffff; text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 12px;" class="button">
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
