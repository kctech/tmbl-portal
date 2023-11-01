@extends('layouts.app')

@section('title') Quotes @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('quote') }}
    <a href="{{ route('quote.create') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus-circle"></i> Add New</a>
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>
        <form class="d-flex align-items-center justify-content-start" method="post" action="{{ route('quote.search') }}">
            @csrf
            <div class="d-flex align-self-center">
                <!--<div class="card">
                    <div class="card-body bg-light d-flex align-items-center">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="accepted_all" name="accepted" class="custom-control-input" value="" {{checked('default', $accepted)}}>
                            <label class="custom-control-label" for="accepted_all">All Quotes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="accepted_yes" name="accepted" class="custom-control-input" value="Y" {{checked('Y', $accepted)}}>
                            <label class="custom-control-label" for="accepted_yes">Accepted Only</label>
                        </div>
                    </div>
                </div>-->
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
                <a href="{{ route('quote.index') }}" class="btn btn-outline-dark btn-lg">Clear</a>
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
                    <th colspan=2>Actions</th>
                    <th colspan=3>Details</th>
                    <th colspan=2>Client</th>
                    <th colspan=2>Contact</th>
                    <th rowspan=2>Last Updated</th>
                </tr>
                <tr>
                    <th>Edit</th>
                    <th>Copy</th> 
                    <th>Fee</th>
                    <th>Amount</th>
                    <th>View</th>
                    <th>First</th>
                    <th>Last</th>
                    <th>Email</th>
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
                        <td><a href="{{ route('quote.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit quote"><i class="far fa-edit"></i> {{ $request->id }}</a></td>
                        <td><a href="{{ route('quote.copy',$request->id)}}" class="btn btn-block btn-sm btn-info text-white" data-toggle="tooltip" data-placement="right" title="Copy quote for the same client"><i class="far fa-copy"></i> Copy</a></td>
                        <td>&pound;{{ $request->fee }}</td>
                        <td>&pound;{{ $request->loan_amnt }}</td>
                        <td>
                            <a href="{{signedRoute('quote.respond', ['code'=>$request->client->uid, 'id'=>$request->id])}}" class="btn btn-block btn-sm btn-success" target="_blank" data-toggle="tooltip" data-placement="right" title="Open quote view page">View <i class="far fa-external-link"></i></a>
                        </td>
                        <td>{{ $request->client->first_name }}</td>
                        <td><a href="{{ route('clients.show',$request->client->id)}}" class="btn btn-block btn-sm btn-primary" data-toggle="tooltip" data-placement="right" title="{{ __('View clients details and requests') }}"><i class="far fa-search"></i> {{ $request->client->last_name }}</a></td>
                        <td>
                            <button type="button" data-href="{{route('quote.resend-client', $request->id)}}" class="btn btn-block btn-sm btn-warning action-btn" data-toggle="tooltip" data-placement="right" title="Resend email to client"><i class="far fa-sync fa-spin"></i> {{ $request->client->email }}</button>
                        </td>
                        <td>{{ $request->client->tel }}</td>
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