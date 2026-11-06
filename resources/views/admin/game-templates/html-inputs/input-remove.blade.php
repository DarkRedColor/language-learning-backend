<div class="field">
    <div id="correct" class="word remove-w">
        @if(isset($game))
            @if(isset($game->answer))
                @if(json_decode($game->answer) != null)
                    @foreach(json_decode($game->answer) as $key => $item)
                        <span @if(json_decode($game->link_words) !== null)@if(in_array($key,json_decode($game->link_words))) class="remove-span" @endif @endif>{{$item}}</span>
                    @endforeach
                @endif
            @endif
        @endif
        <input type="text" placeholder="Type a word"/>
        <input class="hid"/>
    </div>
</div>
