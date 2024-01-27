@extends('layouts.frontend')

@section('title','TMB Portal')

@section('pagetitle','Choose your role...')

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="far fa-user fa-5x"></i>
                    <h2>Client</h2>
                    <a href="{{ route('clients.dashboard') }}" class="btn btn-lg btn-dark">Click Here</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="far fa-user-tie fa-5x"></i>
                    <h2>Adviser</h2>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-lg btn-primary">Click Here</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
