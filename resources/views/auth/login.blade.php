@extends('layouts.login')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="form-signin">

        <div class="text-center mb-4">
            <img class="img-fluid" src="{{asset('img/default/tmbl_logo.png')}}" alt="{{ config('app.name', 'TMBL Portal') }}" width="237" height="84" />
            <h1 class="h3 mb-3 font-weight-normal">{{ __('Adviser Portal Login') }}</h1>
        </div>
        @csrf

        <div class="form-label-group">
            <input id="email" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required autofocus>
            <label for="email">{{ __('E-Mail Address') }}</label>
            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
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

        {{--<div>
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" name="remember" id="remember" class="custom-control-input" value="1" {{ old('remember') ? 'checked' : '' }} />
                <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
            </div>
        </div>--}}

        <button class="btn btn-lg btn-primary btn-block" type="submit">{{ __('Login') }}</button>

        <div class="mt-3">
            @if (Route::has('password.request'))
                <a class="btn btn-link float-left" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
            @endif
            @if(Route::has('register'))
                <a class="btn btn-link float-right" href="{{ route('register') }}">{{ __('Register') }}</a>
            @endif
            <div class="cf"></div>
        </div>
    </form>
@endsection
