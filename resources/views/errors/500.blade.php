@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
    <H2>Boos</H2>
    <p>All's going to the hell !!!</p>
@endsection
