<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- CSRF Token -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') </title>
    <meta name="description" content="@yield('description')">
    {{-- <link rel="preconnect" href="https://fonts.gstatic.com"> --}}
    {{-- <link href="https://fonts.googleapis.com/css2?family=Mukta+Malar&display=swap" rel="stylesheet"> --}}

{{--    <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css">--}}
    {{-- <link rel="stylesheet" href="{{public_path("css/fonts.css")}}" type="text/css"> --}}
    {{-- <style>
        * {
            /*font-size: 8px;*/
            font-family: 'Mukta Malar', sans-serif;
        }
    </style> --}}
    {{-- <script type="text/javascript" src="{{asset("js/pdf-reports.js")}}"></script> --}}
    @stack("css")
    <style rel="stylesheet">
        * {
            font-family: "Mukta Malar", san-serif
        }

    </style>
</head>

<body>
    <div>
        <table class="table table-clear">
            <tr>
                <td class="header-logo">
{{--                    <img src="{{ asset('assets/brand/banner-black.png') }}" width="200" alt="LOGO">--}}
                </td>
                <td class="text-right">
                    <div>
                        <h3 class="font-weight-bolder">{{ config('app.strathmore.address.line1') }}</h3>
                        <div class="font-weight-bold">{{ config('app.strathmore.address.line2') }}</div>
                        <div class="font-weight-bold">{{ config('app.strathmore.address.line3') }}</div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="table table-clear">
            <tr>
                <td class="w-50">@yield('title-1')</td>
                <td class="right header-4 report-title w-50">@yield("title")</td>
            </tr>
        </table>

        <div>
            {{-- <hr> --}}
            @yield("report-details")
            {{-- <br> --}}
            @yield("report-body")
            {{-- <br> --}}
            @yield("report-footer")
        </div>
    </div>
</body>

</html>
