@extends('layouts.app')

@section('title') Eligibility Statements @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('eligibility-statements') }}
    <a href="{{ route('eligibility-statements.create') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus-circle"></i> Add New</a>
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>
        <form class="d-flex align-items-center justify-content-start" method="post" action="{{ route('eligibility-statements.search') }}">
            @csrf
            <div class="d-flex align-self-center">
                <div class="card">
                    <div class="card-body bg-light d-flex align-items-center">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="statement_status_all" name="statement_status" class="custom-control-input" value="" {{checked('default', $statement_status)}}>
                            <label class="custom-control-label" for="statement_status_all">All Responses</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="statement_status_no" name="statement_status" class="custom-control-input" value="N" {{checked('N', $statement_status)}}>
                            <label class="custom-control-label" for="statement_status_no">No Response Only</label>
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
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="far fa-sort-alpha-down"></i></div>
                            </div>
                            <select name="sort" id="sort"  class="form-control">
                                <option value="recent" {{selected('recent', $sort)}}>Recently Updated</option>
                                <option value="newest_first" {{selected('newest_first', $sort)}} {{selected('default', $sort)}}>Newest First</option>
                                <option value="oldest_first" {{selected('oldest_first', $sort)}}>Oldest First</option>
                                <option value="surname_az" {{selected('surname_az', $sort)}}>Surname A-Z</option>
                                <option value="surname_za" {{selected('surname_za', $sort)}}>Surname Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-auto">
                <a href="{{ route('eligibility-statements.index') }}" class="btn btn-outline-dark btn-lg">Clear</a>
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
                    <th rowspan=2>Consent Given</th>
                    <th colspan=2>Client</th>
                    <th colspan=2>Contact</th>
                    <th colspan=3>Marketing</th>
                    <th rowspan=2>Last Updated</th>
                </tr>
                <tr>
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
                        <td><a href="{{ route('eligibility-statements.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit request"><i class="far fa-edit"></i> {{ $request->id }}</a></td>
                        <td>
                            @if($request->consent == 'N')
                                <a href="{{signedRoute('eligibility-statements.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-block btn-sm btn-danger" target="_blank" data-toggle="tooltip" data-placement="right" title="Open client response page">No (copy link <i class="far fa-external-link"></i>)</a>
                            @else
                                <button type="button" data-href="{{route('eligibility-statements.resend-adviser', $request->id)}}" class="btn btn-block btn-sm btn-success action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to yourself">Yes <i class="far fa-sync"></i> Adviser <i class="far fa-envelope"></i></button>
                            @endif
                        </td>
                        <td>{{ $request->client->first_name }}</td>
                        <td><a href="{{ route('clients.show',$request->client->id)}}" class="btn btn-block btn-sm btn-primary" data-toggle="tooltip" data-placement="right" title="{{ __('View clients details and requests') }}"><i class="far fa-search"></i> {{ $request->client->last_name }}</a></td>
                        <td>
                            @if($request->consent == 'N')
                                <button type="button" data-href="{{route('eligibility-statements.resend-client', $request->id)}}" class="btn btn-block btn-sm btn-warning action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to client"><i class="far fa-sync fa-spin"></i> {{ $request->client->email }}</button>
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
