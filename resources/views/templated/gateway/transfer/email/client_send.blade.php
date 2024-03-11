@extends('layouts.email.banner')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader', 'Please consent to us using your data.')

@section('header', 'Please give us consent...')

@section('link', signedRoute('transfer-request.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('banner_img','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/gateway_header_banner.jpg')

@section('banner_img_m','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/gateway_header_banner_m.jpg')

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}}</p>

    <p>Thank you for letting us provide you with some new mortgage options ahead of your current mortgage
    deal coming to an end. You may not be aware that Gateway Mortgages Ltd has moved mortgage networks
    from Openwork to Mortgage Advice Bureau. With new GDPR & Data Protection rules we just need your consent
    for us to continue providing advice to you and to facilitate this, consent to the sharing of your client
    data that we currently hold with Mortgage Advice Bureau in order for them to provide the regulatory
    services and legal obligation towards the Financial Conduct Authority.</p>

    <p>This change will not affect the quality of service we provide to you, and we would really like to keep
    in contact with you about your mortgage requirements. However if you do not wish us to transfer your
    data in this way, that is fine – we will make a note of this, but we hope that you will come back to
    us in the future when you need any advice regarding your mortgage.</p>

    <table align="center" width="50%">
        <tr>
            <td style="background-color: #233c7c; border-radius: 5px 5px 5px 5px; padding: 10px; color: #ffffff; text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 12px;" class="button">
                <a href="{{signedRoute('transfer-request.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" style="color: #ffffff; text-decoration:none; font-weight:bold; display: block;">
                    Yes, Please provide me with new quotes
                </a>
            </td>
        </tr>
    </table>

    <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

    <br><br>

@endsection

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('transfer-request.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
