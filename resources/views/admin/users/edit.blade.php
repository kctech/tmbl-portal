@extends('layouts.app')

@section('title') {{ __('Edit User') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users') }}
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="edit" method="POST" action="{{ route('users.update',$user->id) }}">
        @csrf
        @method('PATCH')
        <input name="id" type="hidden" value="{{ $user->user_id }}" />

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Account') }}</label>

            <div class="col-md-6">
                <select name="account_id" id="account_id"  class="form-control{{ $errors->has('account_id') ? ' is-invalid' : '' }}" required>
                    <option value="" {{selected('', $user->account_id)}}>{{ __('Account') }}</option>
                    @foreach($accounts as $account)
                        <option value="{{$account->id}}" {{selected($account->id, $user->account_id)}}>{{$account->acronym}} | {{$account->name}}</option>
                    @endforeach
                </select>

                @if ($errors->has('account_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('account_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>

            <div class="col-md-6">
                <select name="role_id" id="role_id"  class="form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" required>
                    <option value="" {{selected('', $user->role_id)}}>{{ __('Role') }}</option>
                    @foreach($roles as $role)
                        <option value="{{$role->id}}" {{selected($role->id, $user->role_id)}}>{{$role->name}}</option>
                    @endforeach
                </select>

                @if ($errors->has('role_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('role_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

            <div class="col-md-6">
                <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name', $user->first_name) }}" required autofocus>

                @if ($errors->has('first_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

            <div class="col-md-6">
                <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>

                @if ($errors->has('last_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

            <div class="col-md-6">
                <input id="tel" type="tel" class="form-control{{ $errors->has('tel') ? ' is-invalid' : '' }}" name="tel" value="{{ old('tel', $user->tel) }}" placeholder="(optional)">

                @if ($errors->has('tel'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('tel') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email', $user->email) }}" required>

                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}">

                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('New Password Confirmation') }}</label>

            <div class="col-md-6">
                <input id="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" value="{{ old('password_confirmation') }}">

                @if ($errors->has('password_confirmation'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4 d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ __('Edit User') }}
                </button>
                <button type="button" class="btn btn-danger ml-auto" onclick="app.alerts.confirmDelete('delete','User ID {{$user->id}}')">
                    {{ __('Delete User') }}
                </button>
            </div>
        </div>
    </form>
    <form id="delete" method="POST" action="{{ route('users.destroy',$user->id) }}">
        @csrf
        @method('DELETE')
    </form>
@endsection
