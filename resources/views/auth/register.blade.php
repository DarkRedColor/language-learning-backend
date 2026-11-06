@extends("layouts.guest")
@section("content")
    <div class="login-box">
        <h2>Register</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="color: #e53e3e">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="user-box">
                <input type="text" name="name" required autocomplete="off">
                <label>Nick Name</label>
            </div>
            <div class="user-box">
                <input type="email" name="email" required autocomplete="off">
                <label>Email</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <div class="user-box">
                <input type="password" name="repeat-password" required>
                <label>Repeat Password</label>
            </div>
            <div style="display: flex;justify-content: space-between;">
                <button style="background: linear-gradient(#141e30, #243b55)" type="submit">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    Submit
                </button>
                <a style="margin-top:40px; padding:10px 20px; color: #03e9f4" href="{{route("login")}}">Login</a>
            </div>
        </form>

    </div>
@endsection

