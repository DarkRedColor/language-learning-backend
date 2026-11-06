<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CTF.MD') }}</title>

    <script src="{{asset('js/bootstrap.boundle.min.js')}}"></script>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/sweetalert/sweetalert.all.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">

</head>

<body>
@include('include.navbar')

<div class="container my-4">
    <div class="row">
{{--        @yield('content')--}}
    </div>
</div>
{{--@include('include.footer')--}}
</body>

</html>
