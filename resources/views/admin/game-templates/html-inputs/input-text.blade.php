<div class="selective-way">
    <div class="field task-audio">
        <label>Task</label>
        <div class="ui left labeled button field sixteen wide" tabindex="0">
            <div class="ui selection search dropdown task-audio-dropdown">
                <input type="hidden" name="task-text[]" value="@if(isset($selectedWord)){{$selectedWord->where('type',1)->first()->word_id ?? null}}@endif">
                <div class="default text" data-value="">select task audio</div>
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
</div>
<div class="field manual-way">
    <div class="inline field file">
        <label>Task</label>
        <div class="field">
            <input type="text" name="task-text[]" placeholder="Text for game"
                   @if(isset($game->mission_en))value="{{json_decode($game->mission_en)[0]}}" @endif>
        </div>
    </div>
</div>