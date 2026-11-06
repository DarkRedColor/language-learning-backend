<div class="field">
    <label>Task Audio</label>
    <div class="editor game-editor">
        <div id="toolbar-container"></div>
        <div id="editor">
            @if(isset($game))
                @forelse($game->mission_en as $mission)
                    <p>{!! $mission !!}</p>
                @empty
                @endforelse
            @endif
        </div>
    </div>
    <p class="hint">*You need to have a dedicated word</p>
</div>
<div class="ui icon buttons spell games word task-audio">
    @if(isset($game))
        <input type="hidden" name="read_type" value="{{$game->read_type}}">
        @if($game->read_type == 0)
            <span class="ui icon button spelling word">
                <p>by word</p>
            </span>
        @else
            <span class="ui icon button spelling letter">
                <p>by letter</p>
            </span>
        @endif

    @else
        <input type="hidden" name="read_type" value="0">
        <span class="ui icon button spelling word">
            <p>by word</p>
        </span>
    @endif
    <button type="button" class="ui button large spell-word">
        <i class="cloud download icon large"></i>
    </button>
    <button type="button" class="ui button negative large del-file">
        <i class="trash alternate icon"></i>
    </button>
</div>
<script src="{{asset("js/games_editor.js")}}"></script>
<script src="{{asset("js/main.js")}}"></script>
