@extends('layouts.login')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="form-signin">

        <input name="account_id" type="hidden" value="1" />
        <input name="role_id" type="hidden" value="1" />

        <div class="text-center mb-4">
            <img class="img-fluid" src="{{asset('img/default/tmbl_logo.png')}}" alt="{{ config('app.name', 'TMBL Portal') }}" width="237" height="84" />
            <h1 class="h3 mb-3 font-weight-normal">{{ __('Register') }}</h1>
        </div>

        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-label-group">
            <input id="first_name" type="text" class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('First Name') }}" required autofocus>
            <label for="first_name">{{ __('First Name') }}</label>
            @if ($errors->has('first_name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('first_name') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-label-group">
            <input id="last_name" type="text" class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('Last Name') }}" required>
            <label for="last_name">{{ __('Last Name') }}</label>
            @if ($errors->has('last_name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('last_name') }}</strong>
                </span>
            @endif
        </div>
        
        <div class="form-label-group">
            <input id="email" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required>
            <label for="email">{{ __('E-Mail Address') }}</label>
            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-label-group">
            <input id="tel" type="tel" class="form-control {{ $errors->has('tel') ? ' is-invalid' : '' }}" name="tel" value="{{ old('tel') }}" placeholder="{{ __('Phone Number') }}" required>
            <label for="tel">{{ __('Phone Number') }}</label>
            @if ($errors->has('tel'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('tel') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-label-group">
            <input id="password" type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" required>
            <label for="password">{{ __('Password') }}</label>
            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-label-group">
            <input id="password_confirmation" type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
            @if ($errors->has('password_confirmation'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>

        <button class="btn btn-lg btn-primary btn-block" type="submit">{{ __('Register') }}</button>

        <div class="mt-3">
            @if (Route::has('password.request'))
                <a class="btn btn-link float-left" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
            @endif
            @if(Route::has('register'))
                <a class="btn btn-link float-right" href="{{ route('login') }}">{{ __('Login') }}</a>
            @endif
            <div class="cf"></div>
        </div>
    </form>
@endsection