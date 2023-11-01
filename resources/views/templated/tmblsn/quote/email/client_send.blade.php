@extends('layouts.email.default')

@section('logo', asset('img/'.$details['fields']['adviser']->account->viewset.'/'.$details['fields']['adviser']->account->logo_frontend))

@section('title', 'TMBL | Quote')

@section('preheader', 'Your Mortgage Quote')

@section('header', 'Your Mortgage Quote')

@push('css')
    /*LAYOUT*/
    .productColumnContainer {
        width: 24% !important;
    }
    .productColumnSpacer {
        width: 1%;
    }

    .productTable {
        font-size: 9.0pt;
    }
    .productTable td {
        padding: 10px;
    }
    .oddRow {
        background:#f3f3f3;
    }
    .detailsBlock {
        line-height: 115%;
    }

    /*COLOURS*/
    .primaryColor1 {
        color:#55b154;
    }
    .primaryBackground1 {
        background-color:#55b154;
        color: #ffffff;
        text-align:center;
        line-height:130%;
    }
    .primaryColor2 {
        color:#3f9ad3;
    }
    .primaryBackground2 {
        background-color:#3f9ad3;
        color: #ffffff;
        text-align:center;
        line-height:130%;
    }
    .primaryColor3 {
        color:#3c3c3b;
    }
    .primaryBackground3 {
        background-color:#3c3c3b;
        color: #ffffff;
        text-align:center;
        line-height:130%;
    }
    .primaryColor4 {
        color:#6f42c1;
    }
    .primaryBackground4 {
        background-color:#6f42c1;
        color: #ffffff;
        text-align:center;
        line-height:130%;
    }

    /*RESPONSIVE*/
    @media only screen and (max-width: 800px){
        #productColumns {
            width:600px !important;
        }

        .productColumnContainer {
            display:inline-block !important;
            float: left;
            width: 48% !important;
        }

        .productColumnSpacer {
            display:inline-block !important;
            float: left;
            width: 2% !important;
        }
    }

    @media only screen and (max-width: 480px){
        #productColumns {
            width:100% !important;
        }

        .productColumnContainer,
        .productColumnSpacer {
            display:block !important;
            width: 100% !important;
        }
    }
@endpush

@section('content')

<table border="0" align="center" cellpadding="10" cellspacing="0" width="100%">
    <tr>
        <td>

            <p>Dear {{$details['fields']['client']->first_name}}</p>

            <p>{{$details['fields']['record']->email_intro}}. Based on the information that you provided us with so far – I am pleased to confirm that we have lenders
                who would be happy to consider your mortgage application for the borrowing amount of &pound;{{number_format($details['fields']['record']->loan_amnt,0)}}</p>

            @if(count(json_decode($details['fields']['record']->options)) == 1)
                <p>With this in mind, please see detail of the mortgage deal that is available to you based on the information you have given me.
                If you would like me to get you ‘approved in principle’ on any of this mortgage deal let me know – Please note there is NO cost and NO obligation attached to this service.</p>
            @else
                <p>With this in mind, please find details of a number of mortgage deals that could be available to you based on the information you have given me.
                If you would like me to get you ‘approved in principle’ on any of these mortgage deals let me know – Please note there is NO cost and NO obligation attached to this service.</p>
            @endif
            
            <p>The figures below relate to a &pound;{{number_format($details['fields']['record']->purchase_val,0)}} property value with a
            &pound;{{number_format($details['fields']['record']->loan_amnt,0)}} mortgage
             on 
            @if($details['fields']['record']->loan_interest == 0)
                a repayment
            @elseif($details['fields']['record']->loan_interest == $details['fields']['record']->loan_amnt)
                an interest only
            @else
                a part repayment (&pound;{{number_format(($details['fields']['record']->loan_amnt - $details['fields']['record']->loan_interest),0)}}), part interest only (&pound;{{number_format($details['fields']['record']->loan_interest,0)}})
            @endif
             basis over {{$details['fields']['record']->term_yrs}} years {{$details['fields']['record']->term_mnth}} months.
            These products are indicative quotes only and do not constitute mortgage advice;</p>

            @if(!empty($details['fields']['record']->message))
                <p>{!! nl2br(e($details['fields']['record']->message)) !!}</p>
            @endif

            <table border="0" align="center" cellpadding="0" cellspacing="0" width="1280" id="productColumns">
                <tr>

                @foreach(json_decode($details['fields']['record']->options) as $option)

                    
                    <td align="center" valign="top" class="productColumnContainer">

                        @include('templated.'.$details['fields']['adviser']->account->viewset.'.quote._partials.option',[
                            'option' => $option
                            , 'loop' => $loop
                            , 'fee_type' => $details['fields']['record']->fee_type
                            , 'fee' => $details['fields']['record']->fee
                            , 'fee_timing' => $details['fields']['record']->fee_timing
                            , 'fee_2_type' => $details['fields']['record']->fee_2_type
                            , 'fee_2' => $details['fields']['record']->fee_2
                            , 'fee_2_timing' => $details['fields']['record']->fee_2_timing]
                        )

                    </td>
                    <td class="productColumnSpacer">&nbsp;</td>
                @endforeach
                </tr>
            </table>

            <p>
                The Legal Bit:<br />
                These indicative costs do not constitute a mortgage offer and does not entitle its recipients to a mortgage advance from the lender.
                Any offer of a mortgage is subject to the prevailing terms and conditions and prior to the lenders full underwriting assessment.
                Your home may be repossessed if you do not keep up repayments on your mortgage.
            </p>

            <p>This has been sent on behalf of {{$details['fields']['adviser']->first_name}} {{$details['fields']['adviser']->last_name}} (<a href="mailto:{{$details['fields']['adviser']->email}}">{{$details['fields']['adviser']->email}}</a>)</p>

            <br><br>

        </td>
    </tr>
</table>

@endsection

@section('link', signedRoute('quote.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id]))

@section('footer')
    Trouble displaying this email? <a href="{{signedRoute('quote.respond',['code'=>$details['fields']['client']->uid, 'id'=>$details['fields']['record']->id])}}" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
@endsection
