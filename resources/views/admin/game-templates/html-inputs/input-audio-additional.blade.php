<div class="inline field file eight wide column">
    <label>Task Audio</label>
    <div class="field">
        @if(isset($game) and $game->gamesTemplate[0]->answer == "type")
            <input type="text" name="additional_task[]" placeholder="Text for game"
                   @if(isset($game->description_en))value="{{$game->description_en??null}}" @endif>
        @else
            <input type="text" name="additional_task[]" placeholder="Text for game"
                   @if(isset($game->link_words))value="{{json_decode($game->link_words)[0]??null}}" @endif>
        @endif
    </div>
</div>