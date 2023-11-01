@extends('layouts.app')

@section('title') {{ __('Add New SDLT Disclaimer') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('sdlt-consent') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('sdlt-consent.store') }}">
        @csrf
        <input name="consent_type" type="hidden" value="SDLT" />
        <input name="user_id" type="hidden" value="{{ Session::get('user_id', Auth::user()->id) }}" />
        <input name="link" type="hidden" value="{{$link}}" />

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif

        @include('admin.partials.client_select',['clients' => $clients])

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary" tabindex="10">
                    {{ __('Send Request') }}
                </button>
            </div>
        </div>
    </form>
@endsection
