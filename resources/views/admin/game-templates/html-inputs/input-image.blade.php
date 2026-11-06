<div class="inline field file">
    <input type="text" hidden name="is_deleted" id="is_deleted_language_file" class="hid"/>
    <input type="text" hidden name="dd_file" id="dd_file_language_file" class="hid"/>
    <label>Task image</label>
    <label id="dropped">
        <input type="file" name="image" accept="image/png"
               onchange="previewFile(this);$('#delete_flag_of_language').remove()" placeholder="IMG">
        <i class="upload icon">
            @if($game <> null)
                @if($game->input_type == 1)
                    @php
                        $wordsFromGame = \App\Models\GameWord::where('game_id', $game->id)->where('type', 2)->first();
                                                $wordFromTask = \App\Models\Word::find($wordsFromGame->word_id);
                                                $hostCheck = new \App\Http\Controllers\Services\HostChecker();
                                                $imageLink = config('values.api_url')."/word"."/$wordFromTask->id"."_S.webp"
                    @endphp
                    <img class="previewImg" data-value=""
                         src="{{$imageLink}}">
                @else
                    @if(json_decode($game->mission_en) <> null)
                        @if(json_decode($game->mission_en)[0] <> null)
                            <img class="previewImg" data-value=""
                                 src="@if(isset(json_decode($game->mission_en)[0])){{json_decode($game->mission_en)[0]}}_S @endif">
                        @else
                            <img class="previewImg" data-value=""
                                 src="">
                        @endif
                    @else
                        <img class="previewImg" data-value=""
                             src="">
                    @endif
                @endif
            @else
                <img class="previewImg" data-value=""
                     src="">
            @endif
        </i>
        <a href="#" class="dis-img" onclick="delLanguageFlag()"><i class="close icon"></i></a>
    </label>
</div>
<div class="selective-hint">
    <div class="ui field">
        <label>Hint</label>
        <div class="field" tabindex="0">
            <div class="ui selection search dropdown task-audio-dropdown correct">
                <input type="hidden" name="hint" value="@if(isset($selectedWord)){{$selectedWord->where('game_id',$game->id)->where('type',4)->first()->word_id ?? null}}@endif">
                <div class="default text" data-value="">select Hint
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
<div class="manual-hint">
    <div class="ui field accordion">
        <div class="title inputs">
            <div class="field main-local">
                <label>Hint</label>
                <textarea class="textarea" rows="2" name="hint_en"
                          id="name_en_field_of_text_create_form">{{$game['hint_en']??''}}</textarea>
                <div class="ui label lang-iso">EN
                    <i class="chevron down icon"></i>
                </div>
            </div>
        </div>
        <div class="content field inputs">

            @foreach($languages as $item)
                @if($item->lcid <> 'en')
                    <div class="field">
                        <textarea class="textarea" rows="2"
                                  name="hint_{{$item->lcid}}">{{$game['hint_'.$item->lcid]??''}}</textarea>
                        <div class="ui label lang-iso">{{$item->lcid}}</div>
                    </div>

                @endif
            @endforeach

        </div>
    </div>
</div>

<script>
    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }
</script>
