<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('app.name', 'Langly') }}</title>

    <script src="{{asset('js/bootstrap.boundle.min.js')}}"></script>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/jquery-1.8.2.js')}}"></script>
    <script src="{{asset('vendor/sweetalert/sweetalert.all.js')}}"></script>
    <script src="{{asset('js/jquery-ui.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/jquery-ui.css')}}">

    {{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">--}}


</head>

<body>
@include('admin.includes.navbar')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 d-none d-md-block">
            @include('admin.includes.sidebar-old')
        </div>
        <div class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            @include('sweetalert::alert')
            @yield('content')
        </div>
    </div>
</div>
{{--@include('include.admin.footer')--}}
</body>

</html>
