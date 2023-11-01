@extends('layouts.email.banner')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'TMBL | Thank You')

@section('preheader', 'Thank you for completing your preferences')

@section('header', 'Thank you for completing your preferences')

@section('link', '#')

@section('banner_img','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/tmbl_header_banner_thanks.jpg')

@section('banner_img_m','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/tmbl_header_banner_thanks_m.jpg')

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}}</p>

    <p>Thank you for completing our Terms of Business and preferences form,
        please find attached "Privacy Notice", "Terms of Business" and "Our Promise to you".</p>

    <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

    <br><br>

@endsection

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('terms-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
