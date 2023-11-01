@extends('layouts.app')

@section('title') {{ __('Add New User') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Account') }}</label>

            <div class="col-md-6">
                <select name="account_id" id="account_id"  class="form-control{{ $errors->has('account_id') ? ' is-invalid' : '' }}" required>
                    @foreach($accounts as $account)
                        <option value="{{$account->id}}" {{selected($account->id, old('account_id', ''))}}>{{$account->acronym}} | {{$account->name}}</option>
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
            <label for="role_id" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>

            <div class="col-md-6">
                <select name="role_id" id="role_id"  class="form-control">
                    @foreach($roles as $role)
                        <option value="{{$role->id}}" {{selected($role->id, old('role_id', ''))}}>{{$role->name}}</option>
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
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email', '') }}" required>

                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

            <div class="col-md-6">
                <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name', '') }}" required autofocus>

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
                <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name', '') }}" required>

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
                <input id="tel" type="tel" class="form-control{{ $errors->has('tel') ? ' is-invalid' : '' }}" name="tel" value="{{ old('tel', '') }}" placeholder="(optional)">

                @if ($errors->has('tel'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('tel') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password', '') }}" required>

                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

            <div class="col-md-6">
                <input id="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" value="{{ old('password_confirmation', '') }}" required>

                @if ($errors->has('password_confirmation'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Add User') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('js')

<script>
    $(document).ready(function() {

        $('#email').on('change',function(){
            $('input[type="submit"],button[type="submit"]').attr('disabled',true);
            //check for duplicate users
            var data = {};
            data._token = "{{ csrf_token() }}";
            data.email = $(this).val();
            $.ajax({
                url: "{{route('users.duplicate')}}",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(data),
                cache: false,
                success: function(response) {
                    if(response.count > 0) {
                        app.alerts.response('Duplicate Client','This user alreay exists.','error');
                    }else{
                        $('input[type="submit"],button[type="submit"]').removeAttr('disabled');
                    }
                },
                error: function (jqXHR, response, errorThrown) {
                    console.log(jqXHR);
                    $('input[type="submit"],button[type="submit"]').removeAttr('disabled');
                }
            });
        });

    });
</script>

@endpush