@extends('layouts.app')

@section('title') {{ __('Edit Client') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('clients') }}
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <h3>There's a probelm with your submission:</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="edit" method="POST" action="{{ route('clients.update',$client->id) }}">
        @csrf
        @method('PATCH')
        <input name="client_id" type="hidden" value="{{ $client->id }}" />

        @can('users')
            @include('admin.partials.adviser_select', ['users' => $users, 'user_id' => $client->user_id])
        @else
            @if(!Session::has('impersonate'))
                @include('admin.partials.adviser')
            @else
                @include('admin.partials.adviser_impersonating')
            @endif
        @endcan

        @include('admin.partials.client_edit', ['client' => $client, 'hide_notice' => 1])

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4 d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ __('Edit Client') }}
                </button>
                <button type="button" class="btn btn-danger ml-auto" onclick="app.alerts.confirmDelete('delete','Client ID {{$client->id}}')">
                    {{ __('Delete Client') }}
                </button>
            </div>
        </div>
    </form>
    <form id="delete" method="POST" action="{{ route('clients.destroy',$client->id) }}">
        @csrf
        @method('DELETE')
    </form>
@endsection
