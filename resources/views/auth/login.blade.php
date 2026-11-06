@extends("layouts.guest")
@section("content")
<div class="login-box">
    <h2>Login</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color: #e53e3e">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('post-login') }}">
        @csrf
        <div class="user-box">
            <input type="text" name="email" required autocomplete="off">
            <label>Email</label>
        </div>
        <div class="user-box">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>
<div style="display: flex;justify-content: space-between;">
    <button style="background: linear-gradient(#141e30, #243b55)" type="submit" >
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        Submit
    </button>
{{--    <a style="margin-top:40px; padding:10px 20px; color: #03e9f4" href="{{route("register")}}">Register</a>--}}
</div>


{{--        <div class="block mt-4">--}}
{{--            <label for="remember_me" class="inline-flex items-center">--}}
{{--                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">--}}
{{--                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>--}}
{{--            </label>--}}
{{--        </div>--}}

{{--        <div class="flex items-center justify-end mt-4">--}}
{{--            @if (Route::has('password.request'))--}}
{{--                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">--}}
{{--                    {{ __('Forgot your password?') }}--}}
{{--                </a>--}}
{{--            @endif--}}

{{--            <button class="ml-3">--}}
{{--                {{ __('Login') }}--}}
{{--            </button>--}}
{{--        </div>--}}
    </form>

</div>
@endsection

