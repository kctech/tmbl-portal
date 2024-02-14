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
    <meta http-equiv="refresh" content="1800">

    <title>@yield('title', config('app.name', 'TMBL Portal'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ mix('/css/'.Session::get('css','default.css')) }}" rel="stylesheet">
    @stack('css')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img class="" src="{{asset('img/'.Session::get('viewset','default').'/'.Session::get('logo_frontend','tmbl_logo.png'))}}" alt="{{ config('app.name', Session::get('viewset','default').' Portal') }}"width="229" height="84" />
                </a>

                @hasSection('heading')
                    @yield('heading')
                @else
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <h2>@yield('title', 'Client Portal')</h2>
                            </li>
                        </ul>
                    </div>
                @endif

            </div>
        </nav>

        <!-- HERO -->
        <div class="section hero short lazy-force mb-3" data-src="{{asset('img/'.Session::get('viewset','default').'/background.jpg')}}">
            <div class="container">
                <div class="hero__cta-container">
                    <div class="hero__cta">
                        <h1 class="hero__title">
                            <span class="no-highlight text-white">
                                <span>@yield('pagetitle', 'The Mortage Broker Client Portal')</span>
                            </span>
                        </h1>
                        <!--<div class="hero__content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque risus lorem,
                                hendrerit non sapien varius, ornare consequat purus.</p>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>

        <!--<div id="breadcrumbs" class="bg-white d-none d-md-block">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Library</li>
                    </ol>
                </nav>
            </div>
        </div>-->

        <main>
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->

    <script src="{{ mix('/js/portal.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" ></script>
    @stack('js')
</body>
</html>
