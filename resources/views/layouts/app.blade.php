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
    @livewireStyles
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

        .cursor-pointer:hover {
            cursor: pointer;
        }
    </style>
    @stack('css')
</head>
<body>
    <div id="app">

        @include('layouts.partials.nav')

        <div class="container-fluid">
            <div class="row">

                @include('layouts.partials.sidebar')

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
    @livewireScripts
    <script src="{{ mix('/js/portal.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Extra Scripts -->
    <script>
        app.ticktock(45,1);
    </script>
    @stack('js')

</body>
</html>
