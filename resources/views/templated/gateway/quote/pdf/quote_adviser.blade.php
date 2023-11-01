@extends('layouts.pdf.default')

@section('logo', imgBase64('img/'.$record->user->account->viewset.'/'.$record->user->account->logo_pdf))
@section('title', 'Quote: '.$record->client->first_name.' '.$record->client->last_name .' [ID '.$record->id.']')
@section('pagetitle', 'Quote: '.$record->client->first_name.' '.$record->client->last_name .' [ID '.$record->id.']')

@push('css')
    /*LAYOUT*/
    .productColumnContainer {
        width: 49% !important;
    }
    .productColumnSpacer {
        width: 2%;
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
@endpush

@section('content')

<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" id="productColumns">

	@foreach(json_decode($record->options) as $option)
		@if($loop->first) <tr> @endif
		<td align="center" valign="top" class="productColumnContainer">
			<div class="no-break">
                @include('templated.'.$record->user->account->viewset.'.quote._partials.option_adviser',[
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
		</td>
		<td class="productColumnSpacer">&nbsp;</td>
		@if($loop->iteration % 2 == 0) </tr><tr> @endif
		@if($loop->last) </tr> @endif
	@endforeach
</table>

<div class="no-break">
	@if(!empty($record->message))
		<p>{!! nl2br(e($record->message)) !!}</p>
	@endif
</div>

@endsection