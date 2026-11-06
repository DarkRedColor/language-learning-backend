<div class="field manual-way">
    <div class="field">
        @if($game <> null)
            <input type="text" name="wrong-answer[]" placeholder="Incorrect"
                   @if(json_decode($game->wrong) <> null)value="{{json_decode($game->wrong)[$i] ?? null}}" @endif>
        @else
            <input type="text" name="wrong-answer[]" placeholder="Incorrect">
        @endif
    </div>
</div>
<div class="field selective-way">
    <div class="field" tabindex="0">
        <div class="ui selection search dropdown task-audio-dropdown wrong">
            @if(isset($selectedWord) and count($selectedWord) <> 0)
                <input type="hidden" name="wrong-answer[]" value="{{$selectedWord[$i+1]->word_id ?? null}}">
            @else
                <input type="hidden" name="wrong-answer[]" value="">
            @endif
            <div class="default text" data-value="">select wrong answer
            </div>
            <div class="menu">
                @if(isset($allWordsOfTheory))
                    @foreach($allWordsOfTheory as $arrayitems)
                        @if(isset($arrayitems))
                            @foreach($arrayitems as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->word}}
                                    <span>{{$item->definition_en}}</span></div>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            </div>
            <i class="chevron down icon"></i>
        </div>
    </div>
</div>