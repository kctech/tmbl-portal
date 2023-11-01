@extends('layouts.app')

@section('title') Calculators @endsection

@section('content')
<div class="container py-2">

    <div class="card mb-3">
        <div class="card-header">
            <h2>Mortgage Calculator</h2>
        </div>
        <div class="card-body">
            @include('admin.calcs.partials.mortgage')
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h2>Borrowing Calculator</h2>
        </div>
        <div class="card-body">
            @include('admin.calcs.partials.borrowing')
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h2>SDLT Calculator</h2>
        </div>
        <div class="card-body">
            @include('admin.calcs.partials.sdlt')
        </div>
    </div>

</div>
@endsection
