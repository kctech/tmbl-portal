@extends('layouts.app')

@section('title') System Users @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users') }}
    <a href="{{ route('users.create') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus-circle"></i> Add New</a>
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>
        <form class="d-flex align-items-center justify-content-start flex-wrap" method="post" action="{{ route('users.search') }}">
            @csrf
            <div class="d-flex align-self-center">
                <div class="card">
                    <div class="card-body bg-light">
                        <select name="role_id" id="role_id"  class="form-control">
                            <option value="" {{selected('', $user_role)}}>{{ __('Role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}" {{selected($role->id, $user_role)}}>{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <select name="account_id" id="account_id"  class="form-control">
                            <option value="" {{selected('', $user_account)}}>{{ __('Account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{$account->id}}" {{selected($account->id, $user_account)}}>{{$account->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <input class="form-control" placeholder="User Email" name="user_email" value="{{$user_email}}" />
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <input class="form-control" placeholder="User Surname" name="user_surname" value="{{$user_surname}}" />
                    </div>
                </div>
            </div>
            <div style="flex-basis:100%; height: 0"></div>
            <div class="d-flex align-self-center mt-1">
                <div class="card">
                    <div class="card-body bg-light">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="far fa-sort-alpha-down"></i></div>
                            </div>
                            <select name="sort" id="sort"  class="form-control">
                                <option value="recent" {{selected('recent', $sort)}}>Recently Updated</option>
                                <option value="newest_first" {{selected('newest_first', $sort)}} {{selected('default', $sort)}}>Latest Login</option>    
                                <option value="oldest_first" {{selected('oldest_first', $sort)}}>Oldest Login</option>
                                <option value="surname_az" {{selected('surname_az', $sort)}}>Surname A-Z</option>
                                <option value="surname_za" {{selected('surname_za', $sort)}}>Surname Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light d-flex align-items-center">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="deleted_status_active"" name="deleted_status" class="custom-control-input" value="" {{checked('default', $deleted_status)}}>
                            <label class="custom-control-label" for="deleted_status_active">Active Users Only</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="deleted_status_included" name="deleted_status" class="custom-control-input" value="Y" {{checked('Y', $deleted_status)}}>
                            <label class="custom-control-label" for="deleted_status_included">Include Deleted</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-auto">
                <a href="{{ route('users.index') }}" class="btn btn-outline-dark btn-lg">Clear</a>
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
                    <th rowspan=2>View</th>
                    <th rowspan=2>Impersonate</th>
                    <th rowspan=2>Email</th>
                    <th rowspan=2>Account / Role</th>
                    <th colspan=3>Dates</th>
                </tr>
                <tr>
                    <th>Created</th>
                    <th>Verified</th>
                    <th>Last Login</th>
                </tr>
            </thead>
            <tbody>

                @if($users->count()==0)
                    <tr class="">
                        <td colspan=7>{{ __('Zero results.') }}</td>
                    </tr>
                @endif
                @foreach($users as $user)

                    <tr class="@if(auth()->id() == $user->id) table-success @endif @if(!is_null($user->deleted_at)) table-danger @endif">
                        <td>
                            @if(!is_null($user->deleted_at))
                                <a href="{{ route('users.reinstate',$user->id) }}" class="btn btn-success">{{ __('Restore User') }}</a>
                            @else
                                <a href="{{ route('users.edit',$user->id) }}" class="btn btn-block btn-sm btn-dark" data-toggle="tooltip" data-placement="right" title="{{ __('Edit User') }}"><i class="far fa-edit"></i> {{ $user->id }}</a>
                            @endif
                        </td>
                        <td><a href="{{ route('users.show',$user->id) }}" class="btn btn-block btn-sm btn-primary" data-toggle="tooltip" data-placement="right" title="{{ __('View users details and requests') }}"><i class="far fa-search"></i> {{ $user->first_name }} {{ $user->last_name }}</a></td>
                        <td>
                            @if(auth()->id() != $user->id)
                                <a href="{{ route('users.impersonate',$user->id) }}" class="btn btn-block btn-sm btn-danger" data-toggle="tooltip" data-placement="right" title="{{ __('Impersonate user') }}"><i class="far fa-user-secret"></i></a>
                            @else
                                You
                            @endif
                        </td>
                        <td>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            @if(!is_null($user->deleted_at)) 
                                <br />Deleted at {{\Carbon\Carbon::parse($user->deleted_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->deleted_at)->diffForHumans()}}</span>
                            @endif
                        </td>
                        <td>{{ $user->account->acronym }} | {{ $user->role->name }}</td>
                        <td>
                            {{\Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i')}}
                            <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->created_at)->diffForHumans()}}</span>
                        </td>
                        <td>
                            @if(!is_null($user->email_verified_at))
                                {{\Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->email_verified_at)->diffForHumans()}}</span>
                            @else
                                Not Verified
                            @endif
                        </td>
                        <td>
                            @if(!is_null($user->last_login_at))
                                {{\Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($user->last_login_at)->diffForHumans()}}</span>
                            @else
                                Never Logged In
                            @endif
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $users->links() }}
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