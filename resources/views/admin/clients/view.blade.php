@extends('layouts.app')

@section('title') Client Information: {{ $client->first_name }} {{ $client->last_name }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('clients') }}
@endsection

@section('content')

<div class="row">

    <div class="col-md-6">

        <div class="card mb-3">
            <div class="card-header text-white bg-dark">
                Business Terms Requests
                <a href="{{ route('terms-consent.create',['client' => $client->id])}}" class="btn btn-sm btn-light float-right" data-toggle="tooltip" data-placement="right" title="Add New Business Terms Requests"><i class="far fa-plus"></i> Add New</a>
            </div>
            <ul class="list-group list-group-flush">
                @if($client->terms_consents->count()==0)
                    <li class="list-group-item">{{ __('Zero results.') }}</li>
                @endif
                @foreach($client->terms_consents as $request)
                    <li class="list-group-item @if($request->user_id != Session::get('user_id',auth()->id())) list-group-item-warning @endif">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('terms-consent.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit request"><i class="far fa-edit"></i> {{ $request->id }}</a>
                            </div>
                            <div class="col-sm-3">
                                {{ $request->service }} {{ $request->type }}
                                {{-- ({{ $request->amount }}) {{ _('on') }} {{ $request->timing }} --}}
                            </div>
                            <div class="col-sm-3">
                                @if($request->privacy_consent == 'N' || $request->terms_consent == 'N')
                                    <a href="{{signedRoute('terms-consent.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-sm btn-danger" target="_blank" data-toggle="tooltip" data-placement="right" title="Open client response page">No Reponse <i class="far fa-external-link"></i></a>
                                    <button type="button" data-href="{{route('terms-consent.resend-client', $request->id)}}" class="btn btn-sm btn-warning action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to client"><i class="far fa-sync fa-spin"></i>&nbsp;<i class="far fa-envelope"></i></button>
                                @else
                                    <button type="button" data-href="{{route('terms-consent.resend-adviser', $request->id)}}" class="btn btn-block btn-sm btn-success action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to yourself">Responded <i class="far fa-sync"></i>&nbsp;<i class="far fa-envelope"></i></button>
                                @endif
                            </div>
                            <div class="col-sm-4">
                                {{\Carbon\Carbon::parse($request->updated_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($request->updated_at)->diffForHumans()}}</span>
                                @if($request->user_id != Session::get('user_id',auth()->id()))
                                    <br />By {{$request->user->first_name ?? '** User Deleted **'}} {{$request->user->last_name ?? ''}}
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="list-group list-group-horizontal mt-2" aria-label="PDF Downloads">
                                    <button type="button" class="list-group-item list-group-item-action list-group-item-primary dl-btn" data-id="{{$request->id}}" data-action="{{ signedRoute('terms-consent.download',['code'=>'promise']) }}"><i class="far fa-download"></i> Download Our Promise</button>
                                    <button type="button" class="list-group-item list-group-item-action list-group-item-primary dl-btn" data-id="{{$request->id}}" data-action="{{ signedRoute('terms-consent.download',['code'=>'terms']) }}"><i class="far fa-download"></i> Download Business Terms</button>
                                    <button type="button" class="list-group-item list-group-item-action list-group-item-primary dl-btn" data-id="{{$request->id}}" data-action="{{ signedRoute('terms-consent.download',['code'=>'privacy']) }}"><i class="far fa-download"></i> Download Privacy Policy</button>
                                </ul>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card mb-3">
            <div class="card-header text-white bg-dark">
                Quotes
                <a href="{{ route('quote.create',['client' => $client->id])}}" class="btn btn-sm btn-light float-right" data-toggle="tooltip" data-placement="right" title="Add New Quote"><i class="far fa-plus"></i> Add New</a>
            </div>
            <ul class="list-group list-group-flush">
                @if($client->quotes->count()==0)
                    <li class="list-group-item">{{ __('Zero results.') }}</li>
                @endif
                @foreach($client->quotes as $request)
                    <li class="list-group-item @if($request->user_id != Session::get('user_id',auth()->id())) list-group-item-warning @endif">
                        <div class="row">
                            <div class="col-sm-3">
                                <a href="{{ route('quote.edit',$request->id)}}" class="btn btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit quote"><i class="far fa-edit"></i> {{ $request->id }}</a>
                                <a href="{{ route('quote.copy',$request->id)}}" class="btn btn-sm btn-info text-white" data-toggle="tooltip" data-placement="right" title="Copy quote"><i class="far fa-copy"></i></a>
                                <button type="button" class="btn btn-sm btn-danger dl-btn d-inline-block" data-toggle="tooltip" data-placement="right" title="Download quote" data-id="{{$request->id}}" data-action="{{ signedRoute('quote.download',['code'=>'quote']) }}"><i class="far fa-download"></i></button>
                            </div>
                            <div class="col-sm-3">
                                &pound;{{ $request->fee }},&nbsp;
                                &pound;{{ $request->loan_amnt }}
                                <button type="button" data-href="{{route('quote.resend-client', $request->id)}}" class="btn btn-sm btn-warning action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to client"><i class="far fa-sync fa-spin"></i></button>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{signedRoute('quote.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-block btn-sm btn-success" target="_blank" data-toggle="tooltip" data-placement="right" title="Open quote view page">View <i class="far fa-external-link"></i></a>
                            </div>
                            <div class="col-sm-4">
                                {{\Carbon\Carbon::parse($request->updated_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($request->updated_at)->diffForHumans()}}</span>
                                @if($request->user_id != Session::get('user_id',auth()->id()))
                                    <br />By {{$request->user->first_name ?? '** User Deleted **'}} {{$request->user->last_name ?? ''}}
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card mb-3">
            <div class="card-header text-white bg-dark">
                BTL Consent Requests
                <a href="{{ route('btl-consent.create',['client' => $client->id])}}" class="btn btn-sm btn-light float-right" data-toggle="tooltip" data-placement="right" title="Add New Business Terms Requests"><i class="far fa-plus"></i> Add New</a>
            </div>
            <ul class="list-group list-group-flush">
                @if($client->btl_consents->count()==0)
                    <li class="list-group-item">{{ __('Zero results.') }}</li>
                @endif
                @foreach($client->btl_consents as $request)
                    <li class="list-group-item @if($request->user_id != Session::get('user_id',auth()->id())) list-group-item-warning @endif">
                        <div class="row">
                            <div class="col-sm-4">
                                <a href="{{ route('btl-consent.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit request"><i class="far fa-edit"></i> {{ $request->id }}</a>
                            </div>
                            <div class="col-sm-4">
                                @if($request->consent == 'N')
                                    <a href="{{signedRoute('btl-consent.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-block btn-sm btn-danger" target="_blank" data-toggle="tooltip" data-placement="right" title="Open client response page">No Reponse (copy link <i class="far fa-external-link"></i>)</a>
                                @else
                                    <strong>Type:</strong> {{ $request->consent_additional }}
                                    <button type="button" data-href="{{route('btl-consent.resend-adviser', $request->id)}}" class="btn btn-block btn-sm btn-success action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to yourself">Responded <i class="far fa-sync"></i> Adviser <i class="far fa-envelope"></i></button>
                                @endif
                            </div>
                            <div class="col-sm-4">
                                {{\Carbon\Carbon::parse($request->updated_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($request->updated_at)->diffForHumans()}}</span>
                                @if($request->user_id != Session::get('user_id',auth()->id()))
                                    <br />By {{$request->user->first_name}} {{$request->user->last_name}}
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card mb-3">
            <div class="card-header text-white bg-dark">
                Transfer Requests (from Openwork)
                <a href="{{ route('transfer-request.create',['client' => $client->id])}}" class="btn btn-sm btn-light float-right" data-toggle="tooltip" data-placement="right" title="Add New Business Terms Requests"><i class="far fa-plus"></i> Add New</a>
            </div>
            <ul class="list-group list-group-flush">
                @if($client->transfer_requests->count()==0)
                    <li class="list-group-item">{{ __('Zero results.') }}</li>
                @endif
                @foreach($client->transfer_requests as $request)
                    <li class="list-group-item @if($request->user_id != Session::get('user_id',auth()->id())) list-group-item-warning @endif">
                        <div class="row">
                            <div class="col-sm-4">
                                <a href="{{ route('transfer-request.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit request"><i class="far fa-edit"></i> {{ $request->id }}</a>
                            </div>
                            <div class="col-sm-4">
                                @if($request->consent == 'N')
                                    <a href="{{signedRoute('transfer-request.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-block btn-sm btn-danger" target="_blank" data-toggle="tooltip" data-placement="right" title="Open client response page">No Reponse (copy link <i class="far fa-external-link"></i>)</a>
                                @else
                                    {{ $request->consent_additional }}
                                    <button type="button" data-href="{{route('transfer-request.resend-adviser', $request->id)}}" class="btn btn-block btn-sm btn-success action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to yourself">Responded <i class="far fa-sync"></i> Adviser <i class="far fa-envelope"></i></button>
                                @endif
                            </div>
                            <div class="col-sm-4">
                                {{\Carbon\Carbon::parse($request->updated_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($request->updated_at)->diffForHumans()}}</span>
                                @if($request->user_id != Session::get('user_id',auth()->id()))
                                    <br />By {{$request->user->first_name}} {{$request->user->last_name}}
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>

    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        General Information
                    </div>
                    <div class="card-body">
                        {{ $client->first_name }} {{ $client->last_name }}
                        <br />
                        <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                        <br />
                        <a href="tel:{{ $client->tel }}">{{ $client->tel }}</a>
                        <br />
                        UID: {{ $client->uid }}</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Adviser Information</div>
                    <div class="card-body">
                        @if($client->user)
                            {{ $client->user->first_name }} {{ $client->user->last_name }}
                            <br />
                            <a href="mailto:{{ $client->user->email }}">{{ $client->user->email }}</a>
                            <br />
                            <a href="tel:{{ $client->user->tel }}">{{ $client->user->tel }}</a>
                        @else
                            ** Adviser Deleted **
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                @if($linked->count()>0)
                    <div class="card mb-3">
                        <div class="card-header">Linked To</div>
                        <ul class="list-group list-group-flush">
                            @foreach($linked as $link)
                                <li class="list-group-item">
                                    <a href="{{ route('clients.show',$link->id)}}" class="btn btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="{{ __('View linked clients details and requests') }}"><i class="far fa-search"></i> {{ $link->first_name }} {{ $link->last_name }}</a>
                                    <br />
                                    <a href="mailto:{{ $link->email }}">{{ $link->email }}</a>
                                    <br />
                                    <a href="tel:{{ $link->tel }}">{{ $link->tel }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header">Communication Preferences</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Email</div>
                                <div class="col-sm-6">@if($client->comm_email_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Phone</div>
                                <div class="col-sm-6">@if($client->comm_phone_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">SMS</div>
                                <div class="col-sm-6">@if($client->comm_sms_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Face-to-face</div>
                                <div class="col-sm-6">@if($client->comm_face_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">3<sup>rd</sup> Party</div>
                                <div class="col-sm-6">@if($client->comm_thirdparty_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Other</div>
                                <div class="col-sm-6">@if($client->comm_other_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Marketing Preferences</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Email</div>
                                <div class="col-sm-6">@if($client->mkt_email_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Phone</div>
                                <div class="col-sm-6">@if($client->mkt_phone_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">SMS</div>
                                <div class="col-sm-6">@if($client->mkt_sms_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Face-to-face</div>
                                <div class="col-sm-6">@if($client->mkt_face_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">3<sup>rd</sup> Party</div>
                                <div class="col-sm-6">@if($client->mkt_thirdparty_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Post</div>
                                <div class="col-sm-6">@if($client->mkt_post_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Automated Call</div>
                                <div class="col-sm-6">@if($client->mkt_automatedcall_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                        {{--<li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Web</div>
                                <div class="col-sm-6">@if($client->mkt_web_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>--}}
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">Other</div>
                                <div class="col-sm-6">@if($client->mkt_other_consent == 'Y') Yes @else No @endif</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<form target="_blank" id="download" method="POST" action="/">
    @csrf
    <input id="record_id" name="record_id" type="hidden" value="" />
    <input name="uid" type="hidden" value="{{$client->uid}}" />
</form>

@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.action-btn').on('click', function(){
            var link = $(this).data('href');
            console.log(link);
            $.get(link, function( response ) {
                app.alerts.response(response.title,response.message,response.status);
            });
        });

        $(".dl-btn").click(function(){
            $("#record_id").val($(this).data("id"));
            $("form#download").attr("action",$(this).data("action"));
            $("form#download").submit();
        });
    });
</script>
@endpush
