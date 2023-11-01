@extends('layouts.login')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="form-signin">

        <div class="text-center mb-4">
            <img class="img-fluid" src="{{asset('img/default/tmbl_logo.png')}}" alt="{{ config('app.name', 'TMBL Portal') }}" width="237" height="84" />
            <h1 class="h3 mb-3 font-weight-normal">{{ __('Reset Password') }}</h1>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
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

        <button class="btn btn-lg btn-primary btn-block" type="submit">{{ __('Send Password Reset Link') }}</button>

        <div class="mt-3">
            @if (Route::has('login'))
                <a class="btn btn-link float-left" href="{{ route('login') }}">{{ __('Login') }}</a>
            @endif
            @if(Route::has('register'))
                <a class="btn btn-link float-right" href="{{ route('register') }}">{{ __('Register') }}</a>
            @endif
            <div class="cf"></div>
        </div>
    </form>
@endsection
