@extends('layouts.app')

@section('title') Welcome {{ Auth::user()->first_name }} @endsection

@section('content')
<div class="container py-2">

    <div class="row">

        @can('users')
            <div class="col-md-4">
                <div class="card border-danger mb-3">
                    <div class="card-header text-white bg-danger"><i class="fa fa-user-tie"></i> Users</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('users.create') }}"><i class="fa fa-user-plus"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('users.index') }}"><i class="fa fa-user-cog"></i> View System Users</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('clients')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-users"></i> Clients</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('clients.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('clients.index') }}"><i class="fa fa-file-search"></i> View Your Clients</a>
                    </div>
                </div>
            </div>
        @endcan
        
        <!--
        @can('gdprconsents')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-user"></i> GDPR Consents</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('gdpr-consent.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('gdpr-consent.index') }}"><i class="fa fa-file-search"></i> View Requests</a>
                    </div>
                </div>
            </div>
        @endcan
        -->

        @can('transferrequests')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-passport"></i> Client Transfers (from Openwork)</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('transfer-request.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('transfer-request.index') }}"><i class="fa fa-file-search"></i> View Requests</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('btlconsents')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-user-friends"></i> BTL Consent</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('btl-consent.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('btl-consent.index') }}"><i class="fa fa-file-search"></i> View Requests</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('sdltdisclaimers')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-pound-sign"></i> SDLT Disclaimers</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('sdlt-consent.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('sdlt-consent.index') }}"><i class="fa fa-file-search"></i> View Requests</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('businessterms')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-tasks"></i> Business Terms</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('terms-consent.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('terms-consent.index') }}"><i class="fa fa-file-search"></i> View Packs Sent</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('businesstermsprotection')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-umbrella"></i> Protection Business Terms</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('terms-consent.create-protection') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('terms-consent.index') }}"><i class="fa fa-file-search"></i> View Packs Sent</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('quotes')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-key"></i> Mortgage Quote</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('quote.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('quote.index') }}"><i class="fa fa-file-search"></i> View Quotes</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('eligibilitystatements')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-check-circle"></i> Eligibility Statements</div>

                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('eligibility-statements.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <a class="list-group-item list-group-item-light" href="{{ route('eligibility-statements.index') }}"><i class="fa fa-file-search"></i> View Statements</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('calculators')
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header text-white bg-dark"><i class="fa fa-calculator"></i> Calculators</div>

                    <div class="list-group">
                        <a class="list-group-item list-group-item-light" href="{{ route('calculators.index') }}"><i class="fa fa-file-search"></i> View All</a>
                    </div>
                </div>
            </div>
        @endcan
        
    </div>

</div>
@endsection
