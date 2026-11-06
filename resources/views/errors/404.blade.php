@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message')
    <H2>Huston! <br>We have a problem</H2>
    <p>Click <a href="#" onclick="history.back()"><span style="color:#f17000">here</span></a> to go Back</p>
    <p>or <a href="#" onclick="window.location.replace('/login')"><span style="color:#f17000">here</span></a> for a login screen</p>
@endsection
