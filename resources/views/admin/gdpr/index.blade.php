@extends('layouts.app')

@section('title') GDPR Requests @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('gdpr-consent') }}
    <a href="{{ route('gdpr-consent.create') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus-circle"></i> Add New</a>
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>
        <form class="d-flex align-items-center justify-content-start" method="post" action="{{ route('gdpr-consent.search') }}">
            @csrf
            <div class="d-flex align-self-center">
                <div class="card">
                    <div class="card-body bg-light d-flex align-items-center">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="consent_status_all" name="consent_status" class="custom-control-input" value="" {{checked('default', $consent_status)}}>
                            <label class="custom-control-label" for="consent_status_all">All Consents</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="consent_status_no" name="consent_status" class="custom-control-input" value="N" {{checked('N', $consent_status)}}>
                            <label class="custom-control-label" for="consent_status_no">No Consent Only</label>
                        </div>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <input class="form-control" placeholder="Client Surname" name="client_surname" value="{{$client_surname}}" />
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <select name="consent_type" id="consent_type"  class="form-control">
                            <option value="" {{selected('default', $consent_type)}}>Consent Type (All)</option>    
                            <option value="DA" {{selected('DA', $consent_type)}}>Directly Authorised</option>
                            <option value="OPW" {{selected('OPW', $consent_type)}}>Openwork</option>
                            <option value="PR" {{selected('PR', $consent_type)}}>Protection Referral</option>
                            <option value="EA" {{selected('EA', $consent_type)}}>Estate Agent</option>
                            <option value="TMH" {{selected('TMH', $consent_type)}}>The Money Hub</option>
                            <option value="LW" {{selected('LW', $consent_type)}}>Loan Warehouse</option>
                            <option value="SM" {{selected('SM', $consent_type)}}>Smart Money</option>
                            <option value="VR" {{selected('VR', $consent_type)}}>Viva Retirement</option>
                            <option value="BNPF" {{selected('BNPF', $consent_type)}}>BNP Finance</option>
                        </select>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="far fa-sort-alpha-down"></i></div>
                            </div>
                            <select name="sort" id="sort"  class="form-control">
                                <option value="recent" {{selected('recent', $sort)}}>Recently Updated</option>
                                <option value="newest_first" {{selected('newest_first', $sort)}} {{selected('default', $sort)}}>Newset First</option>    
                                <option value="oldest_first" {{selected('oldest_first', $sort)}}>Oldest First</option>
                                <option value="surname_az" {{selected('surname_az', $sort)}}>Surname A-Z</option>
                                <option value="surname_za" {{selected('surname_za', $sort)}}>Surname Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-auto">
                <a href="{{ route('gdpr-consent.index') }}" class="btn btn-outline-dark btn-lg">Clear</a>
                <button type="submit" class="btn btn-dark btn-lg ml-3">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover text-sm">
            <thead class="thead-dark text-center">
                <tr>
                    <th rowspan=2>ID</th>
                    <th colspan=2>Consent</th>
                    <th colspan=2>Client</th>
                    <th colspan=2>Contact</th>
                    <th colspan=3>Marketing</th>
                    <th rowspan=2>Last Updated</th>
                </tr>
                <tr>
                    <th>Type</th>
                    <th>Given</th>
                    <th>First</th>
                    <th>Last</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Emails</th>
                    <th>Post</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>

                @if($requests->count()==0)
                    <tr class="">
                        <td colspan=11>Zero results.</td>
                    </tr>
                @endif
                @foreach($requests as $request)

                    <tr class="">
                        <td><a href="{{ route('gdpr-consent.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit request"><i class="far fa-edit"></i> {{ $request->id }}</a></td>
                        <td>{{ $request->consent_type }}</td>
                        <td>
                            @if($request->consent == 'N')
                                <a href="{{signedRoute('gdpr-consent.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-block btn-sm btn-danger" target="_blank" data-toggle="tooltip" data-placement="right" title="Open client response page">No (copy link <i class="far fa-external-link"></i>)</a>
                            @else
                                <button type="button" data-href="{{route('gdpr-consent.resend-adviser', $request->id)}}" class="btn btn-block btn-sm btn-success action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to yourself">Yes <i class="far fa-sync"></i> Adviser <i class="far fa-envelope"></i></button>
                            @endif
                        </td>
                        <td>{{ $request->client->first_name }}</td>
                        <td><a href="{{ route('clients.show',$request->client->id)}}" class="btn btn-block btn-sm btn-primary" data-toggle="tooltip" data-placement="right" title="{{ __('View clients details and requests') }}"><i class="far fa-search"></i> {{ $request->client->last_name }}</a></td>
                        <td>
                            @if($request->consent == 'N')
                                <button type="button" data-href="{{route('gdpr-consent.resend-client', $request->id)}}" class="btn btn-block btn-sm btn-warning action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to client"><i class="far fa-sync fa-spin"></i> {{ $request->client->email }}</button>
                            @else
                                <a href="mailto:{{ $request->client->email }}">{{ $request->client->email }}</a>
                            @endif
                        
                        </td>
                        <td>{{ $request->client->tel }}</td>
                        <td>{{ $request->client->mkt_email_consent }}</td>
                        <td>{{ $request->client->mkt_post_consent }}</td>
                        <td>{{ $request->client->mkt_phone_consent }}</td>
                        <td>
                            {{\Carbon\Carbon::parse($request->updated_at)->format('d/m/Y H:i')}}
                            <span class="badge badge-primary">{{\Carbon\Carbon::parse($request->updated_at)->diffForHumans()}}</span>
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $requests->links() }}
    </div>
</div>

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
    });
</script>
@endpush