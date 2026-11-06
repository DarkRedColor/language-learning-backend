<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0" style="display: flex">

        <a class="navbar-brand mr-0" style="margin-left: 25px" href="">{{ config('app.name') }}</a>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap ml-5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-link nav-link" type="submit">Logout</button>
                </form>
            </li>
        </ul>
</nav>
