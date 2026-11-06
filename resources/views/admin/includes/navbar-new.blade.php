<nav class="ui top fixed inverted menu">
    <div class="item">
        <span>Langly</span>
        <!--        <img src="/images/logo.png">-->
    </div>
    <div class="right menu">
        <span style="color: white">user role: {{Auth::user()->getRoleNames()->first()}}</span>
{{--        <div class="xmas">--}}
{{--            <div>Happy New Year! | <span id="count"></span> 🎉</div>--}}
{{--            <div>Happy money day: <span id="pay-day"></span> 💰</div>--}}
{{--        </div>--}}
{{--        <div class="kek"></div>--}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="ui button item" type="submit">Logout</button>
        </form>
    </div>
</nav>
