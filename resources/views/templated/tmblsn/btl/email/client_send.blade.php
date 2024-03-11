@extends('layouts.email.banner')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'TMBL | Consent')

@section('preheader', 'Please consent to us using your data.')

@section('header', 'Please give us consent...')

@section('link', signedRoute('btl-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('banner_img','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/tmbl_header_banner.jpg')

@section('banner_img_m','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/tmbl_header_banner_m.jpg')

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}}</p>

    <p>Thank you for discussing your mortgage requirements with me today. As discussed, I am unable to offer you tax advice therefore
    I suggest you seek independent tax advice to clearly understand your tax position for your buy to let purchase / remortgage.</p>

    <p>Before I can proceed with your mortgage, please confirm which of the following is applicable to you:</p>

    <table align="center" width="50%">
        <tr>
            <td style="background-color: #233c7c; border-radius: 5px 5px 5px 5px; padding: 10px; color: #ffffff; text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 12px;" class="button">
                <a href="{{signedRoute('btl-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" style="color: #ffffff; text-decoration:none; font-weight:bold; display: block;">
                    Click here to make your selection
                </a>
            </td>
        </tr>
    </table>

    <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

    <br><br>

@endsection

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('btl-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
