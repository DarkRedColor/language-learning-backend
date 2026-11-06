<div class="field selective-way">
    <div class="field task-audio sixteen wide columns">
        <div class="field sixteen wide columns">
            <label>Task Audio</label>
            <label>Spelling</label>
            <div class="field" tabindex="0">
                <div class="ui left labeled button field sixteen wide" tabindex="0">
                    <div class="ui selection search dropdown task-audio-dropdown">
                        <input type="hidden" name="mission_en"
                               value="@if(isset($selectedWord)){{$selectedWord->where('game_id',$game->id)->where('type',0)->first()->word_id ?? null}}@endif">
                        <div class="default text audio-spell" data-value="">select correct answer
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
                    @if(isset($game))
                        <input type="hidden" name="read_type" value="{{$game->read_type}}">
                        @if($game->read_type == 0)
                            <div class="ui icon button spelling word">
                                <p>by word</p>
                            </div>
                        @else
                            <div class="ui icon button spelling letter">
                                <p>by letter</p>
                            </div>
                        @endif
                    @else
                        <input type="hidden" name="read_type" value="0">
                        <div class="ui icon button spelling word">
                            <p>by word</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="manual-way">
    <div class="field task-audio sixteen wide columns">
        <div class="field sixteen wide columns">
            <label>Task Audio</label>
            <label>Spelling</label>
            <div class="ui input audio-spell">
                <input type="text" name="mission_en" placeholder="Type here new task" value="@if(isset($game)){{str_replace('"','',$game->mission_en)}}@endif">
                @if(isset($game))
                    <input type="hidden" name="read_type" value="{{$game->read_type}}">
                    @if($game->read_type == 0)
                        <div class="ui icon button spelling word">
                            <p>by word</p>
                        </div>
                    @else
                        <div class="ui icon button spelling letter">
                            <p>by letter</p>
                        </div>
                    @endif
                @else
                    <input type="hidden" name="read_type" value="0">
                    <div class="ui icon button spelling word">
                        <p>by word</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="field upload-block">
            <div class="ui icon buttons spell games">
                <button type="button" class="ui button large spell-word">
                    <i class="cloud download icon large"></i>
                </button>
                <button type="button" class="ui button negative large del-file">
                    <i class="trash alternate icon"></i>
                </button>
            </div>
        </div>
    </div>



{{--    <div class="ui field eight wide column accordion">--}}
{{--        <div class="ui title inputs">--}}
{{--            <div class="field main-local">--}}
{{--                <label><h5>Task Audio</h5></label>--}}
{{--                <textarea class="textarea" name="mission_en" rows="2">@if(isset($game))--}}
{{--                        {{str_replace('"','',$game->mission_en)}}--}}
{{--                    @endif</textarea>--}}
{{--                <div class="ui label lang-iso">EN--}}
{{--                    <i class="chevron down icon"></i>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="content field inputs">--}}
{{--            @foreach($languages as $item)--}}
{{--                @if($item->lcid <> 'en')--}}
{{--                    @php--}}
{{--                        $iso = 'mission_'.$item->lcid;--}}
{{--                    @endphp--}}
{{--                    <div class="field">--}}
{{--                        <textarea class="textarea" rows="2" name="mission_{{$item->lcid}}">@if(isset($game))--}}
{{--                                {{$game->$iso}}--}}
{{--                            @endif</textarea>--}}
{{--                        <div class="ui label lang-iso">{{$item->lcid}}</div>--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}
</div>