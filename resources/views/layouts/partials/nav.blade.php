<nav class="navbar navbar-dark p-0 fixed-topX bg-primary shadow d-sm-flex d-md-flex d-lg-none">
    <a class="navbar-brand" href="{{ route('admin.dashboard') }}" style=" background: none; box-shadow: none;">
        <img class="" src="{{asset('img/'.Session::get('viewset','default').'/'.Session::get('logo','tmbl_logo.png'))}}" alt="{{ config('app.name', Session::get('viewset','default').' Portal') }}" width="237" height="84" />
    </a>
    <button class="ml-auto mr-2 navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

        @if (Auth::check())
            <form id="search" method="GET" action="{{ route('clients.search') }}">
                {{--@csrf--}}
                <div class="input-group w-100">
                    <input name="client_search" class="form-control form-control-dark" type="text" placeholder="Client's Name/Email" aria-label="Client Search" aria-describedby="button-search">
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
                @can('lead_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/leads/dashboard') }}" href="{{ route('leads.index') }}">
                            <i class="fas fa-tachometer-alt-slow"></i>
                            Leads Dashboard
                        </a>
                    </li>
                @endcan
                @can('lead_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/leads/manager') }}" href="{{ route('leads.manager') }}">
                            <i class="fa fa-folders"></i>
                            Leads Manager
                        </a>
                    </li>
                @endcan
                @can('leads')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/leads/table') }}" href="{{ route('leads.table') }}">
                            <i class="fa fa-inbox"></i>
                            Leads
                        </a>
                    </li>
                @endcan
                @can('leads')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/leads/flow') }}" href="{{ route('leads.flow') }}">
                            <i class="fab fa-trello"></i>
                            Lead Flow
                        </a>
                    </li>
                @endcan
                @can('calculators')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/calculators') }}" href="{{ route('calculators.index') }}">
                            <i class="fa fa-calculator"></i>
                            Calculators
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
                    @can('gdprconsents')
                        <li class="nav-item">
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
                @can('users')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/users') }}" href="{{ route('users.index') }}">
                            <i class="fa fa-user-tie"></i>
                            Users
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
