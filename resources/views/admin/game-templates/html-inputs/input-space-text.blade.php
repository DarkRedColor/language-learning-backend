<div class="field ">
    <label>Task Text</label>
    <div class="editor game-editor">
        <div id="toolbar-container"></div>
        <div id="editor">
            @if(isset($game->mission_en))
                @forelse($game->mission_en as $mission)
                    <p>{!! $mission !!}</p>
                @empty
                @endforelse
            @endif
        </div>
    </div>
    <p class="hint">*You need to have a dedicated word</p>
</div>
<script src="{{asset("js/games_editor.js")}}"></script>
<script src="{{asset("js/main.js")}}"></script>
