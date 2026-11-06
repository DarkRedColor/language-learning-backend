<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Langly - users</title>
    <script src="https://unpkg.com/vue@3.3.4"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/energy.css')}}">
</head>
<body>
<div id="app">
    <div class="toolbar">
        <h1></h1>
        <div class="menu menu-content" @click="toggleMenu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <p class="server-name"></p>
    </div>
    <nav :class="{ 'menu-open': isOpen }">
        <header>
            <span class="close" @click="toggleMenu"></span>
            <h1>MENU</h1>
        </header>
        <a href="{{route('user-energy')}}">Energy and Coins</a>
        <a href="{{route('user-data')}}">Users & transaction</a>
        <a href="{{route('exp')}}">EXP</a>
        <a href="{{route('status-settings')}}">Status settings</a>
        <a href="{{route('arcade-vip')}}">Arcade VIP</a>
        <footer>
            <a href="/">EXIT</a>
        </footer>
    </nav>
    @include('admin.users.status-setting')
</div>
</body>
<script>
    const email = "{{config('values.python_login')}}";
    const password = "{{config('values.python_password')}}";
</script>
<script src="{{asset("js/status-settings.js")}}"></script>
</html>
