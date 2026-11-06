<div class="two wide column main-menu">
    <h1>Main menu</h1>
    <div class="ui vertical accordion menu">
        <div class="scene">
            <a href="{{route('season-index')}}" class="item @if(Route::is("season-index"))active @endif">Adventure</a>
            <a href="{{route('learning-index')}}"
               class="item @if(Route::is("learning-index"))active @endif">Learning</a>
            <a href="{{route('dialogue-index')}}"
               class="item @if(Route::is("dialogue-index"))active @endif">Dialogues</a>
            <a href="{{route('arcade-games')}}"
               class="item @if(Route::is("arcade-games"))active @endif">Arcade Games</a>
        </div>

        <div class="storage">
            <a href="{{route('word-index')}}" class="item @if(Route::is("word-index"))active @endif">Words</a>
            <a href="{{route('theory-index')}}" class="item @if(Route::is("theory-index"))active @endif">Theory</a>
            <a href="{{route('practice-index')}}"
               class="item @if(Route::is("practice-index"))active @endif">Practice</a>
            <a href="{{route('game-description-index')}}"
               class="item @if(Route::is("game-description-index"))active @endif">Description Games</a>
            <a href="{{route('game-templates-index')}}"
               class="item @if(Route::is("game-templates-index"))active @endif">Game Templates</a>
            <a class="ui item title files-menu">
                <i class="dropdown icon"></i>
                Files
            </a>
            <div class="content field files-dropdown">
                <a href="{{route('file-text-index')}}" class="item @if(Route::is("file-text-index"))active @endif">Text
                    files</a>
                <a href="{{route('file-audio-index')}}"
                   class="item @if(Route::is("file-audio-index"))active @endif">Audio files</a>
                <a href="{{route('file-video-index')}}"
                   class="item @if(Route::is("file-video-index"))active @endif">Video files</a>
                <a href="{{route('quiz-index')}}"
                   class="item @if(Route::is("quiz-index"))active @endif">Quiz IMG</a>
                <a href="{{route('animation-index')}}" class="item @if(Route::is("animation-index"))active @endif">Animation</a>
                <a href="{{route('arcade-storage')}}" class="item @if(Route::is("arcade-storage"))active @endif">Arcade Theme</a>
            </div>
        </div>

        <div class="images">
            <a href="{{route('personage-index')}}" class="item @if(Route::is("personage-index"))active @endif">Characters</a>
            <a href="{{route('background-index')}}" class="item @if(Route::is("background-index"))active @endif">Backgrounds</a>
        </div>

        <div class="texts">
            <a href="{{route('main-language-index')}}" class="item @if(Route::is("main-language-index"))active @endif">Languages</a>
            <a href="{{route('text-index')}}" class="item @if(Route::is("text-index"))active @endif">Text interface</a>
            <a href="{{route('status')}}" class="item @if(Route::is("status"))active @endif">Status</a>
            <a class="ui item title files-menu">
                <i class="dropdown icon"></i>
                Other
            </a>
            <div class="content field other-dropdown">
                <a href="{{route('introduction-index')}}"
                   class="item @if(Route::is("introduction-index"))active @endif">Introduction</a>
                <a href="{{route('welcome-index')}}"
                   class="item @if(Route::is("welcome-index"))active @endif">Welcome</a>
                <a href="{{route('motivation-index')}}" class="item @if(Route::is("motivation-index"))active @endif">Motivation</a>
                <a href="{{route('subtitle-index')}}" class="item @if(Route::is("subtitle-index"))active @endif">Subtitles for audio</a>
            </div>
            @if (Auth::check() && Auth::user()->hasRole(['admin','user','qa','project']))
                <a href="{{route('language-index')}}"
                   class="item @if(Route::is("language-index"))active @endif">Create New Languages</a>
            @endif
        </div>
    </div>
    @if (Auth::check() && Auth::user()->hasRole(['admin','qa','project','developer']))

    <h1>Admin</h1>
    <div class="ui vertical menu">
        <a href="{{ route('user-data') }}" class="item @if(Route::is("user-data"))active @endif">User data</a>
    @if (Auth::check() && Auth::user()->hasRole('admin'))


            <a href="{{ route('user-index') }}" class="item @if(Route::is("user-index"))active @endif">Workspace
                Member</a>

            <a href="{{ route('cash-clear') }}" class="item">Clear all cash (All!!)</a>

            @php

            @endphp



            @if (Auth::user()->email == "dimaterticinii@gmail.com")
                <a href="{{ route('lang-seeder') }}" class="item">Seeds db with default languages</a>
                <a href="{{ route('role-seeder') }}" class="item">Seeds db with default roles</a>
                <a href="{{ route('infos-data') }}" class="item">Seeds db with infos</a>

            @endif

    @endif
</div>
    @endif
</div>