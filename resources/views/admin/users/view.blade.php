@extends('layouts.app')

@section('title') User Information: {{ $user->first_name }} {{ $user->last_name }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users') }}
@endsection

@section('content')

<div class="row">

    <div class="col-md-9">

        <div class="card mb-3">
            <div class="card-header text-white bg-dark">Clients</div>
            <ul class="list-group list-group-flush">
                @if($user->clients->count()==0)
                    <li class="list-group-item">{{ __('Zero results.') }}</li>
                @endif
                @foreach($user->clients as $request)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('clients.edit',$request->id)}}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="Edit client"><i class="far fa-edit"></i> {{ $request->id }}</a>
                            </div>
                            <div class="col-sm-3">
                                <a href="{{ route('clients.show',$request->id)}}" class="btn btn-block btn-sm btn-primary" data-toggle="tooltip" data-placement="right" title="View client"><i class="far fa-search"></i> 
                                    {{ $request->first_name }} {{ $request->last_name }}
                                </a>
                            </div>
                            <div class="col-sm-4">
                                <a href="mailto:{{ $request->email }}">
                                    {{ $request->email }}
                                </a>
                            </div>
                            <div class="col-sm-3">
                                {{\Carbon\Carbon::parse($request->updated_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($request->updated_at)->diffForHumans()}}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>

    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header">General Information</div>
            <div class="card-body">
                {{ $user->first_name }} {{ $user->last_name }}
                <br />
                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                <br />
                <br />
                <i class="fa fa-building"></i> {{ $user->account->acronym }}
                <br />
                <i class="fa fa-shield-alt"></i> {{ $user->role->name }}
                <br />
                <br />
                <strong>Created:</strong> {{\Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i')}} <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->created_at)->diffForHumans()}}</span>
                <br />
                <strong>Verified:</strong>
                @if(!is_null($user->email_verified_at))
                    {{\Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y H:i')}}
                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->email_verified_at)->diffForHumans()}}</span>
                @else
                    Not Verified
                @endif
                <br />
                <strong>Last Login:</strong>
                @if(!is_null($user->last_login_at))
                    {{\Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i')}}
                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->last_login_at)->diffForHumans()}}</span>
                @else
                    Never Logged In
                @endif
                <br />
                <br />
                <a href="{{ route('users.edit',$user->id)}}" class="btn btn-block btn-sm btn-danger"><i class="far fa-edit"></i> {{ __('Edit') }}</a>
                <br />
                <a href="{{ route('users.impersonate',$user->id)}}" class="btn btn-block btn-sm btn-warning"><i class="far fa-user-ninja"></i> {{ __('Impersonate') }}</a>
            </div>
        </div>
    </div>

</div>

<form target="_blank" id="download" method="POST" action="/">
    @csrf
    <input id="record_id" name="record_id" type="hidden" value="" />
    <input name="uid" type="hidden" value="{{$user->uid}}" />
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