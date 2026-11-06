<div class="field">
    <input type="text" hidden name="erudite" value="false">
    <input type="text" name="correct_answer[]" placeholder="Correct" @if(isset($game->answer))value="{{json_decode($game->answer)[0]}}" @endif>
</div>