@extends('layouts.app')

@section('title') {{ __('Edit BTL Confirmation') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('btl-consent') }}
@endsection

@section('content')
    <form id="edit" method="POST" action="{{ route('btl-consent.update',$consent->id) }}">
        @csrf
        @method('PATCH')
        <input name="user_id" type="hidden" value="{{ $consent->user_id }}" />
        <input name="client_id" type="hidden" value="{{ $consent->client_id }}" />
        <input name="consent_type" type="hidden" value="BTL" />

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif

        @include('admin.partials.client_edit',['client' => $consent->client])

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4 d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ __('Edit Request') }}
                </button>
                <button type="button" class="btn btn-danger ml-auto" onclick="app.alerts.confirmDelete('delete','BTL Request ID {{$consent->id}}')">
                    {{ __('Delete Request') }}
                </button>
            </div>
        </div>
    </form>
    <form id="delete" method="POST" action="{{ route('btl-consent.destroy',$consent->id) }}">
        @csrf
        @method('DELETE')
    </form>
@endsection
