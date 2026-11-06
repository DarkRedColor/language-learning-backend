<div class="selective-way">
    <div class="ui field">
        <label>Task</label>
        <div class="field" tabindex="0">
            <div class="ui selection search dropdown task-audio-dropdown correct">
                <input type="hidden" name="mission_en" value="@if(isset($selectedWord)){{$selectedWord->where('game_id',$game->id)->where('type',1)->first()->word_id ?? null}}@endif">
                <div class="default text" data-value="">select correct ansewr
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
</div>
<div class="manual-way">
    <div class="ui field accordion">
        <div class="ui title inputs">
            <div class="field main-local">
                <label>Task Text</label>
                <textarea class="textarea" name="mission_en" rows="2">@if(isset($game)){{str_replace('"','',$game->mission_en)}}@endif</textarea>
                <div class="ui label lang-iso">EN
                    <i class="chevron down icon"></i>
                </div>
            </div>
        </div>
        <div class="content field inputs">
            @foreach($languages as $item)
                @if($item->lcid <> 'en')
                    @php
                        $iso = 'mission_'.$item->lcid;
                    @endphp
                    <div class="field">
                        <textarea class="textarea" rows="2" name="mission_{{$item->lcid}}">@if(isset($game)){{$game->$iso}}@endif</textarea>
                        <div class="ui label lang-iso">{{$item->lcid}}</div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>