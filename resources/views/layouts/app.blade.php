<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <!-- Use the .htaccess and remove these lines to avoid edge case issues.-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Mobile viewport optimized: j.mp/bplateviewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    
    <!-- standard meta information -->
    <meta name="designer" content="Perpetual https://perpetual.pro" />
    <meta name="author" content="Perpetual https://perpetual.pro" />
    <meta name="publisher" content="Perpetual https://perpetual.pro" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="country" content="UK" />
    <meta name="language" content="{{ str_replace('_', '-', app()->getLocale()) }}" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- 2h refresh 
    <meta http-equiv="refresh" content="7200">-->

    <title>{{ config('app.name', 'TMBL Portal') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ mix('/css/'.Session::get('css','default.css')) }}" rel="stylesheet">
    <style type="text/css">
        body {
            font-size: .875rem;
        }

        /*
        * Sidebar
        */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100; /* Behind the navbar */
            padding: 48px 0 0; /* Height of navbar */
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
        }

        @supports ((position: -webkit-sticky) or (position: sticky)) {
            .sidebar-sticky {
                position: -webkit-sticky;
                position: sticky;
            }
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #f8fafc;
        }

        .sidebar .nav-link .fa {
            margin-right: 4px;
            color: #fff;
        }

        .sidebar .nav-link.active {
            color: #6c757d;
        }

        .sidebar .nav-link:hover .feather,
        .sidebar .nav-link.active .feather {
            color: inherit;
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
        }

        /*
        * Content
        */
        [role="main"] {
            padding-top: 48px; /* Space for fixed navbar */
        }

        @media (min-width: 768px) {
            [role="main"] {
                padding-top: 0px; /* Space for fixed navbar */
            }
        }

        /*
        * Navbar
        */
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
            width: 16.666667%;
            text-align: center;
        }

        .navbar-brand img {
            height: 25px;
            width: auto;
            margin: 0 auto;
        }

        .navbar .form-control {
            padding: .75rem 1rem;
            border-width: 0;
            border-radius: 0;
        }

        .form-control-dark {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .1);
        }

        .form-control-dark:focus {
            border-color: transparent;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
        }
    </style>
    @stack('css')
</head>
<body>
    <div id="app">
    
        <nav class="navbar navbar-dark p-0 fixed-top bg-dark shadow d-sm-block d-md-block d-lg-none">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <img class="" src="{{asset('img/'.Session::get('viewset','default').'/'.Session::get('logo','tmbl_logo.png'))}}" alt="{{ config('app.name', Session::get('viewset','default').' Portal') }}" width="237" height="84" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                @if (Auth::check())
                    <form id="search" method="POST" action="{{ route('clients.search') }}">
                        @csrf
                        <div class="input-group w-100">
                            <input name="client_surname" class="form-control form-control-dark" type="text" placeholder="Client's Surname" aria-label="Client Surname Search" aria-describedby="button-search">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="button-search"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ isActive('admin/dashboard') }}" href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        @can('calculators')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/calculators') }}" href="{{ route('calculators.index') }}">
                                    <i class="fa fa-calculator"></i>
                                    Calculators
                                </a>
                            </li>
                        @endcan
                        @can('users')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/users') }}" href="{{ route('users.index') }}">
                                    <i class="fa fa-user-tie"></i>
                                    Users
                                </a>
                            </li>
                        @endcan
                        @can('clients')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/clients') }}" href="{{ route('clients.index') }}">
                                    <i class="fa fa-users"></i>
                                    Clients
                                </a>
                            </li>
                        @endcan
                        <!--
                            @can('gdprconsents')<li class="nav-item">
                            <a class="nav-link {{ isActive('admin/gdpr-consent') }}" href="{{ route('gdpr-consent.index') }}">
                                <i class="fa fa-user"></i>
                                GDPR Consents
                            </a>
                        </li>
                        @endcan
                        -->
                        @can('transferrequests')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/transfer-request') }}" href="{{ route('transfer-request.index') }}">
                                    <i class="fa fa-passport"></i>
                                    Client Transfer (from Openwork)
                                </a>
                            </li>
                        @endcan
                        @can('btlconsents')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/btl-consent') }}" href="{{ route('btl-consent.index') }}">
                                    <i class="fa fa-user-friends"></i>
                                    BTL Consents
                                </a>
                            </li>
                        @endcan
                        @can('sdltdisclaimers')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/btl-consent') }}" href="{{ route('sdlt-consent.index') }}">
                                    <i class="fa fa-pound-sign"></i>
                                    SDLT Disclaimers
                                </a>
                            </li>
                        @endcan
                        @can('businessterms')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/terms-consent') }}" href="{{ route('terms-consent.index') }}">
                                    <i class="fa fa-tasks"></i>
                                    Business Terms
                                </a>
                            </li>
                        @endcan
                        @can('businesstermsprotection')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/terms-consent') }}" href="{{ route('terms-consent.search', array('service' => 'P')) }}">
                                    <i class="fa fa-umbrella"></i>
                                    Protection Business Terms
                                </a>
                            </li>
                        @endcan
                        @can('quotes')
                            <li class="nav-item">
                                <a class="nav-link {{ isActive('admin/quote') }}" href="{{ route('quote.index') }}">
                                    <i class="fa fa-key"></i>
                                    Mortgage Quote
                                </a>
                            </li>
                        @endcan
                    </ul>

                @endif

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 d-none d-lg-block @if(Session::has('impersonate')) bg-dark @else bg-dark @endif text-light sidebar p-2">
                    <div class="sidebar-sticky">
                        <div class="w-100 text-center mb-2">
                            <a href="{{ route('admin.dashboard') }}">
                                <img class="img-fluid" src="{{asset('img/'.Session::get('viewset','default').'/'.Session::get('logo','tmbl_logo.png'))}}" alt="{{ config('app.name', Session::get('viewset','default').' Portal') }}" width="237" height="84" />
                            </a>
                            <h2>Adviser Portal</h2>
                        </div>
                        
                        @guest

                            <ul class="nav flex-column mb-2">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fa fa-sign-in"></i>
                                        {{ __('Login') }}
                                    </a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">
                                            <i class="fa fa-user-plus"></i>
                                            {{ __('Register') }}
                                        </a>
                                    </li>
                                @endif
                                <!--<li class="nav-item">
                                    <a class="nav-link" href="{{ route('password.update') }}">
                                        <i class="fa fa-key"></i>
                                        {{ __('Change Password') }}
                                    </a>
                                </li>-->
                            </ul>

                        @else
                            <form id="search2" method="POST" action="{{ route('clients.search') }}">
                                @csrf
                                <div class="input-group mb-3 w-100">
                                    <input name="client_surname" class="form-control form-control-dark" type="text" placeholder="Client's Surname" aria-label="Client Surname Search" aria-describedby="button-search2">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="button-search2"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>

                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ isActive('admin/dashboard') }}" href="{{ route('admin.dashboard') }}">
                                        <i class="fa fa-home"></i>
                                        Dashboard
                                    </a>
                                </li>
                                @can('calculators')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/calculators') }}" href="{{ route('calculators.index') }}">
                                            <i class="fa fa-calculator"></i>
                                            Calculators
                                        </a>
                                    </li>
                                @endcan
                                @can('users')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/users') }}" href="{{ route('users.index') }}">
                                            <i class="fa fa-user-tie"></i>
                                            Users
                                        </a>
                                    </li>
                                @endcan
                                @can('clients')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/clients') }}" href="{{ route('clients.index') }}">
                                            <i class="fa fa-users"></i>
                                            Clients
                                        </a>
                                    </li>
                                @endcan
                                <!--
                                    @can('gdprconsents')<li class="nav-item">
                                    <a class="nav-link {{ isActive('admin/gdpr-consent') }}" href="{{ route('gdpr-consent.index') }}">
                                        <i class="fa fa-user"></i>
                                        GDPR Consents
                                    </a>
                                </li>
                                @endcan
                                -->
                                @can('transferrequests')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/transfer-request') }}" href="{{ route('transfer-request.index') }}">
                                            <i class="fa fa-passport"></i>
                                            Client Transfer (from Openwork)
                                        </a>
                                    </li>
                                @endcan
                                @can('btlconsents')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/btl-consent') }}" href="{{ route('btl-consent.index') }}">
                                            <i class="fa fa-user-friends"></i>
                                            BTL Consents
                                        </a>
                                    </li>
                                @endcan
                                @can('sdltdisclaimers')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/sdlt-consent') }}" href="{{ route('sdlt-consent.index') }}">
                                            <i class="fa fa-pound-sign"></i>
                                            SDLT Disclaimers
                                        </a>
                                    </li>
                                @endcan
                                @can('businessterms')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/terms-consent') }}" href="{{ route('terms-consent.index') }}">
                                            <i class="fa fa-tasks"></i>
                                            Business Terms
                                        </a>
                                    </li>
                                @endcan
                                @can('businesstermsprotection')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/terms-consent') }}" href="{{ route('terms-consent.search', array('service' => 'P')) }}">
                                            <i class="fa fa-umbrella"></i>
                                            Protection Business Terms
                                        </a>
                                    </li>
                                @endcan
                                @can('quotes')
                                    <li class="nav-item">
                                        <a class="nav-link {{ isActive('admin/quote') }}" href="{{ route('quote.index') }}">
                                            <i class="fa fa-key"></i>
                                            Mortgage Quote
                                        </a>
                                    </li>
                                @endcan
                            </ul>

                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                <span>User Settings</span>
                                <!--<a class="d-flex align-items-center text-muted" href="#">
                                    <span data-feather="plus-circle"></span>
                                </a>-->
                            </h6>
                            <ul class="nav flex-column mb-2">
                                <li class="nav-item pl-3">
                                    <i class="fa fa-user-tie"></i>
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                </li>
                                <li class="nav-item pl-3">
                                    <i class="fa fa-shield-alt"></i>
                                    {{ Auth::user()->role->name }}
                                </li>
                                <li class="nav-item pl-3">
                                    <i class="fa fa-building"></i>
                                    {{ Auth::user()->account->name }}
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out"></i>
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                                <!--<li class="nav-item">
                                    <a class="nav-link" href="{{ route('password.update') }}">
                                        <i class="fa fa-key"></i>
                                        {{ __('Change Password') }}
                                    </a>
                                </li>-->
                            </ul>
                        @endguest
                    </div>
                </nav>

                <main role="main" class="col-lg-10 ml-sm-auto px-4 pb-4">

                    @if(Session::has('impersonate'))
                        <div class="alert alert-danger p-4">
                            <a href="{{ route('users.stop-impersonating') }}" title="{{ __('Stop Impersonating') }}" class="btn btn-dark btn-sm float-right">{{ __('Stop Impersonating') }}</a>
                            {!! Session::get('impersonate') !!}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">@yield('title','Dashboard')</h1>
                        
                        <div class="btn-toolbar mb-2 mb-md-0">

                            @yield('breadcrumbs')
                            
                        </div>
                    </div>

                    <div class="flash-message py-2">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
                            @endif
                        @endforeach
                    </div> <!-- end .flash-message -->

                    <!--
                        <router-view></router-view>
                        <ul class="list-group">
                            <li><router-link class="list-item" to="/">Home</router-link></li>
                            <li><router-link class="list-item" to="/about">About</router-link></li>
                        </ul>
                    -->

                    @yield('content')

                </main>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <!-- Scripts -->
    <script src="{{ mix('/js/default.js') }}"></script>
    <script>
        app.ticktock(45,1);
    </script>
    @stack('js')

</body>
</html>
