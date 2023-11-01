@extends('layouts.frontend')

@section('title') Your Quote @endsection
@section('pagetitle') Your Quote @endsection

@push('css')
<style type="text/css">
    /*LAYOUT*/
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
</style>
@endpush

@section('content')
<div class="container">
    
    <div class="row justify-content-center">

        <div class="col-sm-12">
            <p>Dear {{$record->client->first_name}} {{$record->client->last_name}}</p>

            <p>Thank you for your mortgage enquiry earlier. Based on the information that you provided us with so far – I am pleased to confirm that we have lenders
                who would be happy to consider your mortgage application for the borrowing amount of &pound;{{number_format($record->loan_amnt,0)}}</p>

            @if(count(json_decode($record['options'])) == 1)
                <p>With this in mind, please see detail of the mortgage deal that is available to you based on the information you have given me.
                If you would like me to get you ‘approved in principle’ on any of this mortgage deal let me know – Please note there is NO cost and NO obligation attached to this service.</p>
            @else
                <p>With this in mind, please find details of a number of mortgage deals that could be available to you based on the information you have given me.
                If you would like me to get you ‘approved in principle’ on any of these mortgage deals let me know – Please note there is NO cost and NO obligation attached to this service.</p>
            @endif
            
            <p>The figures below relate to a &pound;{{number_format($record->purchase_val,0)}} property value with a
            &pound;{{number_format($record->loan_amnt,0)}} mortgage
             on 
            @if($record->loan_interest == 0)
                a repayment
            @elseif($record->loan_interest == $record->loan_amnt)
                an interest only
            @else
                a part repayment (&pound;{{number_format(($record->loan_amnt - $record->loan_interest),0)}}), part interest only (&pound;{{number_format($record->loan_interest,0)}})
            @endif
             basis over {{$record->term_yrs}} years {{$record->term_mnth}} months.
            These products are indicative quotes only and do not constitute mortgage advice;</p>
        </div>
        
        @foreach(json_decode($record['options']) as $option)
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">

                @include('templated.'.$record->user->account->viewset.'.quote._partials.option',[
                    'option' => $option
                    , 'loop' => $loop
                    , 'fee_type' => $record->fee_type
                    , 'fee' => $record->fee
                    , 'fee_timing' => $record->fee_timing
                    , 'fee_2_type' => $record->fee_2_type
                    , 'fee_2' => $record->fee_2
                    , 'fee_2_timing' => $record->fee_2_timing]
                )

            </div>
        @endforeach

        @if(!empty($record->message))
            <div class="col-sm-12">
                <p>{!!nl2br($record->message)!!}</p>
            </div>
        @endif

        <div class="col-sm-12">
            <p>
                The Legal Bit:<br />
                These indicative costs do not constitute a mortgage offer and does not entitle its recipients to a mortgage advance from the lender.
                Any offer of a mortgage is subject to the prevailing terms and conditions and prior to the lenders full underwriting assessment.
                Your home may be repossessed if you do not keep up repayments on your mortgage.
            </p>
        </div>

    </div>
</div>

@endsection

@push('js')
    <script>
        app.ticktock_frontend(30,0.5);
    </script>
@endpush