@extends('layouts.email.banner')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | Consent')

@section('preheader', 'Please consent to our Terms of Business')

@section('header', 'Please consent to our Terms of Business ')

@section('link', signedRoute('terms-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('banner_img','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/gateway_header_banner.jpg')

@section('banner_img_m','https://tmblportal.co.uk/img/'.$details['fields']['adviser']->account->viewset.'/email/gateway_header_banner_m.jpg')

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}}</p>

    <p>Please find our "Privacy Notice", "Terms of Business" and "Our Promise to you" within the link below.</p>

    <p>These documents are important as they explain what we do with and how we hold your personal information
        and also confirm the basis on which we will provide you with services. Please keep the Privacy Notice
        and our Terms of Business safe for your records.</p>

    {{--<p>I will need you to read the Privacy Notice, Terms of Business and Our Promise, then confirm you&rsquo;ve
        done so and that you agree via the form below. I&rsquo;d also like you to tell me how best to communicate with you,
        the marketing preferences are optional. If you are not able to complete the form, please contact me and
        I will send through the documents for you to print off and scan back a signed copy.</p>--}}

    <table align="center" width="50%">
        <tr>
            <td style="background-color: #233c7c; border-radius: 5px 5px 5px 5px; padding: 10px; color: #ffffff; text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 12px;" class="button">
                <a href="{{signedRoute('terms-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" style="color: #ffffff; text-decoration:none; font-weight:bold; display: block;">
                    Click here to respond
                </a>
            </td>
        </tr>
    </table>

    {{--<p><strong>YOUR DATA SECURITY IS IMPORTANT TO US</strong></p>

    <p>We use a third party secure email provider called Egress. Once we have provided you with an indication
        of the rates you may have access to we will ask you to provide us with personal information and documentation
        in the form of a factfind. We will send this document to you using our secure email service and to review
        your first email you will need to register with the service. Egress is a free service and you can correspond
        with us with the peace of mind that information passing between us is encrypted for your protection.
        Please ensure that you register when you are prompted to do so.</p>--}}

    <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

    <br><br>

@endsection

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('terms-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
