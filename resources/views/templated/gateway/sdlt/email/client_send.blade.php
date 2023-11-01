@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'Gateway Mortgages | SDLT Disclaimer')

@section('preheader', 'Please confirm your understanding.')

@section('header', 'Please confirm your understanding...')

@section('link', signedRoute('sdlt-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('content')

    <p>Dear {{$details['fields']['client']->first_name}}</p>

    <p>Please be aware that the government Stamp Duty incentive has been extended until 30<sup>th</sup> June 2021.</p>
    <p>Due to the unpredictable demand in the current mortgage market and longer processing times with Lenders, mortgage applications are subject to longer completion times. We are always working hard to ensure a speedy process but are not responsible for lender capacity and delays.</p>
    <p>Therefore, should for any reason your mortgage application take you past the 30<sup>th</sup> June 2021 deadline, then you will be liable for the new Stamp Duty Tax rates effective from this date.</p>
    <p>These can be found via the following link:<br /><a href="https://www.tax.service.gov.uk/calculate-stamp-duty-land-tax/#/intro" target="_blank" rel="noopener">https://www.tax.service.gov.uk/calculate-stamp-duty-land-tax/#/intro</a></p>
    <p>All mortgage application costs you have paid will be non-refundable, even in the scenario that you are not able to pay for the total Stamp Duty to complete your purchase.</p>
    <p>As we get closer to the deadline, you may need to consider making provisions for any additional costs should the deadline be missed.</p>
    <p>For further information please contact your solicitor or a member of our team.</p>

    <table align="center" width="50%">
        <tr>
            <td style="background-color: #2e96d8; border-radius: 5px 5px 5px 5px; padding: 10px; color: #ffffff; text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 12px;" class="button">
                <a href="{{signedRoute('sdlt-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" style="color: #ffffff; text-decoration:none; font-weight:bold; display: block;">
                    Click here to confirm your understanding
                </a>
            </td>
        </tr>
    </table>

    <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

    <br><br>

@endsection

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('sdlt-consent.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
