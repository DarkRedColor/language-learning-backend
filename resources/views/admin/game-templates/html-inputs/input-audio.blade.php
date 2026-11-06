<div class="field selective-way">
    <div class="field task-audio sixteen wide columns">
        <div class="field sixteen wide columns">
            <label>Task Audio</label>
            <div class="ui left labeled button field sixteen wide" tabindex="0">
                <div class="ui selection search dropdown task-audio-dropdown">
                    <input type="hidden" name="task-audio[]"
                           value="@if(isset($selectedWord)){{$selectedWord->where('type',0)->wherenotnull('type')->first()->word_id ?? null}}@endif">
                    <div class="default text audio-spell" data-value="">select task audio</div>
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
                {{--            input-ul de mai jos e pentru schimbarea modului de citire (0 -> by word; 1 -> by letter)--}}
                {{--            pentru forma de edit: valoarea salvata in bd o sa se gaseasca in $game->read_type (ea o sa fie egala sau cu 0 sau cu 1)--}}
                {{--            cu alte cuvinte o sa fie ceva de genu:--}}
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
                {{--            trebuie de adaugat js pentru click pe button si gata--}}
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
                <input type="text" name="task-audio[]" placeholder="Text for game"
                       @if(isset($game->mission_en) and json_decode($game->mission_en) !== null)value="{{json_decode($game->mission_en)[0]}}" @endif>
                {{--            input-ul de mai jos e pentru schimbarea modului de citire (0 -> by word; 1 -> by letter)--}}
                {{--            pentru forma de edit: valoarea salvata in bd o sa se gaseasca in $game->read_type (ea o sa fie egala sau cu 0 sau cu 1)--}}
                {{--            cu alte cuvinte o sa fie ceva de genu:--}}
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
                {{--            trebuie de adaugat js pentru click pe button si gata--}}

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
</div>