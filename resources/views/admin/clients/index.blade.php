@extends('layouts.app')

@section('title') Your Clients @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('clients') }}
    <a href="{{ route('clients.create') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus-circle"></i> Add New</a>
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>
        <form class="d-flex align-items-center justify-content-start" method="get" action="{{ route('clients.search') }}">
            {{--@csrf--}}
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
                        <input class="form-control" placeholder="Client Search" name="client_search" value="{{$client_search}}" />
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
                <a href="{{ route('clients.index') }}" class="btn btn-outline-dark btn-lg">Clear</a>
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
                    <th rowspan=2>Consents</th>
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

                @if($clients->count()==0)
                    <tr class="">
                        <td colspan=11>{{ __('Zero results.') }}</td>
                    </tr>
                @endif
                @foreach($clients as $client)

                    <tr class="">
                        <td><a href="{{ route('clients.edit',$client->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="{{ __('Edit client') }}"><i class="far fa-edit"></i> {{ $client->id }}</a></td>
                        <td><a href="{{ route('clients.show',$client->id)}}" class="btn btn-block btn-sm btn-primary" data-toggle="tooltip" data-placement="right" title="{{ __('View clients details and requests') }}"><i class="far fa-search"></i> {{ __('View Details') }}</a></td>
                        <td>{{ $client->first_name }}</td>
                        <td>{{ $client->last_name }}</td>
                        <td>
                            @if($client->consent == 'N')
                                {{--<button type="button" data-href="{{route('clients.resend-login', $client->id)}}" class="btn btn-block btn-sm btn-warning action-btn" data-toggle="tooltip" data-placement="right" title="{{ __('Resend email to client') }}"><i class="far fa-sync fa-spin"></i> {{ $client->email }}</button>--}}
                                <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                            @else
                                <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                            @endif

                        </td>
                        <td>{{ $client->tel }}</td>
                        <td>{{ $client->mkt_email_consent }}</td>
                        <td>{{ $client->mkt_post_consent }}</td>
                        <td>{{ $client->mkt_phone_consent }}</td>
                        <td>
                            {{\Carbon\Carbon::parse($client->updated_at)->format('d/m/Y H:i')}}
                            <span class="badge badge-primary">{{\Carbon\Carbon::parse($client->updated_at)->diffForHumans()}}</span>
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $clients->appends(['consent_status'=>$consent_status ?? '', 'client_search'=>$client_search ?? '', 'sort'=>$sort ?? ''])->links() }}
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
