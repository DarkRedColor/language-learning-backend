<div class="manual-way">
    <div class="ui field">
        <h5>Answer(s)</h5>
        <div class="localized-answer">
            @if(isset($game))
                @if(isset($game->link_words))
                    @foreach(json_decode($game->link_words) as $item)
                        <div class="ui field accordion localized">
                            <div class="title inputs">
                                <div class="field main-local">
                                    <label>Localized answer</label>
                                    <textarea class="textarea" name="link_words[]" rows="2">{{$item}}</textarea>
                                    <div class="ui label lang-iso">EN
                                        <i class="chevron down icon"></i>
                                    </div>
                                </div>
                            </div>
                            @php
                                $index = $loop->index;
                            @endphp
                            <div class="content field inputs">
                                @foreach($languages as $item)
                                    @if($item->lcid <> 'en')
                                        <div class="field">
                                            @php
                                                $iso = 'linked_words_'.$item->lcid;
                                            @endphp
                                            <textarea class="textarea" rows="2"
                                                      name="linked_words_{{$item->lcid}}[]">@if(isset($game->$iso)){{json_decode($game->$iso)[$index]}}@endif</textarea>
                                            <div class="ui label lang-iso">{{$item->lcid}}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="ui field accordion localized">
                        <div class="title inputs">
                            <div class="field main-local">
                                <label>Answer</label>
                                <textarea class="textarea" name="link_words[]" rows="2"></textarea>
                                <div class="ui label lang-iso">EN
                                    <i class="chevron down icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="content field inputs">
                            @foreach($languages as $item)
                                @if($item->lcid <> 'en')
                                    <div class="field">
                                        @php
                                            $iso = 'linked_words_'.$item->lcid;
                                        @endphp
                                        <textarea class="textarea" rows="2"
                                                  name="linked_words_{{$item->lcid}}[]"></textarea>
                                        <div class="ui label lang-iso">{{$item->lcid}}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="ui field accordion localized">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Answer</label>
                            <textarea class="textarea" name="link_words[]" rows="2"></textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                <div class="field">
                                    @php
                                        $iso = 'linked_words_'.$item->lcid;
                                    @endphp
                                    <textarea class="textarea" rows="2"
                                              name="linked_words_{{$item->lcid}}[]"></textarea>
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <a class="add ui basic button add-localization"><i class="plus icon"></i>Add one more correct answer</a>
    </div>
</div>
<div class="field selective-way">
    <div class="ui field">
        <label>Answer</label>
        <div class="field" tabindex="0">
            <div class="ui selection search dropdown task-audio-dropdown correct">
                <input type="hidden" name="link_words[]"
                       value="@if(isset($selectedWord)){{$selectedWord->where('game_id',$game->id)->where('type',2)->first()->word_id ?? null}}@endif">
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
        </div>
    </div>
</div>