@extends("layouts.racoon")
@section("content")
    <div class="login-wrap">
        <div class="login-panel">
            <object data="{{ asset('img/racoon.svg') }}" type="image/svg+xml"></object>
            <div class="permission">
                <h2>Hello, you've been registered successfully.</h2>
                <p>Ask <strong><u>Administrator</u></strong> to give you a permission</p>
            </div>
        </div>
    </div>
@endsection