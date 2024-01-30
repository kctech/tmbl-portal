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
            <form id="search" method="GET" action="{{ route('clients.search') }}">
                {{--@csrf--}}
                <div class="input-group w-100">
                    <input name="client_search" class="form-control form-control-dark" type="text" placeholder="Client's Name/Email" aria-label="Client Search" aria-describedby="button-search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="button-search"><i class="fa fa-search"></i></button>
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
                @can('leads')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/leads/dashboard') }}" href="{{ route('leads.index') }}">
                            <i class="fas fa-tachometer-alt-slow"></i>
                            Leads Dashboard
                        </a>
                    </li>
                @endcan
                @can('leads')
                    <li class="nav-item">
                        <a class="nav-link {{ isActive('admin/leads/manage') }}" href="{{ route('leads.manage') }}">
                            <i class="fa fa-inbox"></i>
                            Leads
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
