<div class="field manual-way">
    <div class="field task-audio">
        <input type="text" hidden name="erudite" value="false">
        <input type="text" name="correct-answer_link[]" placeholder="Correct"
               @if(isset($game->answer))value="@if(isset(json_decode($game->answer)[$i])){{json_decode($game->answer)[$i]}} @endif" @endif>
    </div>
</div>
<div class="field selective-way">
    <div class="field" tabindex="0">
        <div class="ui selection search dropdown task-audio-dropdown correct">
            <input type="hidden" name="correct-answer_link[]" value="@if(isset($selectedWord)){{$selectedWord[$i]->word_id ?? null}}@endif">
            <div class="default text" data-value="">select correct answer
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