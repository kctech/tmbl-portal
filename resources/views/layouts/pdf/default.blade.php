<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <base href="{{ url('/') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@yield('title', config('app.name', 'TMBL Portal'))</title>

    <!-- Styles -->
    <!--<link href="{{ asset('css/pdf.css') }}" rel="stylesheet">-->
    <style>
        {{ cssToInline('css/pdf.css') }}
        .page-break {
            page-break-after: always;
        }
        .no-break {
            page-break-inside: avoid;
        }
        @stack('css')
    </style>
</head>
<body>

    <table class="w-100">
        <tbody>
            <tr>
                <td valign="center" width="237" height="84">
                    <img class="mb-0" src="@yield('logo', imgBase64('img/default/tmbl_logo.jpg'))" alt="{{ config('app.name', 'TMBL Portal') }}" width="237" height="84" />
                </td>
                <td valign="center" class="text-right">
                    @hasSection('heading')
                        @yield('heading')
                    @else
                        <h2 class="mb-0">@yield('title', 'Client Portal')</h2>
                    @endif 
                </td>
            </tr>
        </tbody>
    </table>

    @yield('content')

</body>
</html>
