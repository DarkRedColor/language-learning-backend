<div class="field">
    <div id="incorrect" class="word">
        @if(isset($game) and !empty($game))
            @if(isset($game->wrong) and !empty($game->wrong))
                @if(json_decode($game->wrong) !== null)
                    @forelse(json_decode($game->wrong) as $item)
                        <span>{{$item}}</span>
                    @empty
                    @endforelse
                @endif
            @endif
        @endif
        <input type="text" placeholder="Type a word"/>
        <input class="hid"/>
    </div>
</div>