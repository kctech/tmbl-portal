@extends('layouts.app')

@section('title') {{ __('Edit GDPR Consent') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('gdpr-consent') }}
@endsection

@section('content')
    <form id="edit" method="POST" action="{{ route('gdpr-consent.update',$consent->id) }}">
        @csrf
        @method('PATCH')
        <input name="user_id" type="hidden" value="{{ $consent->user_id }}" />
        <input name="client_id" type="hidden" value="{{ $consent->client_id }}" />

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif
        
        <div class="form-group row">
            <label for="consent_type" class="col-md-4 col-form-label text-md-right">{{ __('Consent Type') }}</label>

            <div class="col-md-6">
                <select name="consent_type" id="consent_type"  class="form-control{{ $errors->has('consent_type') ? ' is-invalid' : '' }}" required autofocus>
                    <option value="DA" {{selected('DA', old('consent_type', $consent->consent_type))}}>Directly Authorised</option>
                    <option value="OPW" {{selected('OPW', old('consent_type', $consent->consent_type))}}>Openwork</option>
                    <option value="PR" {{selected('PR', old('consent_type', $consent->consent_type))}}>Protection Referral</option>
                    <option value="EA" {{selected('EA', old('consent_type', $consent->consent_type))}}>Estate Agent</option>
                    {{--<option value="TMH" {{selected('TMH', old('consent_type', $consent->consent_type))}}>The Money Hub</option>
                    <option value="LW" {{selected('LW', old('consent_type', $consent->consent_type))}}>Loan Warehouse</option>
                    <option value="SM" {{selected('SM', old('consent_type', $consent->consent_type))}}>Smart Money</option>
                    <option value="VR" {{selected('VR', old('consent_type', $consent->consent_type))}}>Viva Retirement</option>
                    <option value="BNPF" {{selected('BNPF', old('consent_type', $consent->consent_type))}}>BNP Finance</option>--}}
                </select>

                @if ($errors->has('consent_type'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('consent_type') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        @include('admin.partials.client_edit',['client' => $consent->client])

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4 d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ __('Edit Request') }}
                </button>
                <button type="button" class="btn btn-danger ml-auto" onclick="app.alerts.confirmDelete('delete','GDPR request ID {{$consent->id}}')">
                    {{ __('Delete Request') }}
                </button>
            </div>
        </div>
    </form>
    <form id="delete" method="POST" action="{{ route('gdpr-consent.destroy',$consent->id) }}">
        @csrf
        @method('DELETE')
    </form>
@endsection
