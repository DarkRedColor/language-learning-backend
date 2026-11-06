<form action="{{route('intro-update', $intro->id)}}" class="ui form contentChanged" id="updateForm" method="post">
    @csrf
    <input type="hidden" name="type" value="choice" id="type">
    <input type="hidden" name="tree" value="" id="tree">
    <div class="two fields">
        <div class="field options">
            <div class="field">
                <label>Character</label>
                <div class="ui selection search dropdown">
                    <input type="hidden" data-value="{{$intro->personage_id}}" name="personage_id"
                           value="{{$intro->personage_id}}" id="personage_id">
                    @php
                        $po = "Character";
                        foreach($personages as $item){
                            if ($item['id'] == $intro->personage_id){
                                $po = $item['name_en'];
                            }
                        }
                    @endphp
                    <div class="default text" data-value="{{$po}}">Character</div>
                    <div class="menu">
                        @foreach($personages as $item)
                            <div class="item" data-value="{{$item['id']}}" id="personage">{{$item['name_en']}}</div>
                        @endforeach
                    </div>
                    <i class="chevron down icon"></i>
                </div>
            </div>
            <div class="field">
                <label>Emotion</label>
                <div class="ui selection search dropdown">
                    <input type="hidden" data-value="{{$intro->emotion_id}}" name="emotion_id" id="emotion_value"
                           value="{{$intro->emotion_id}}">

                    @if($intro->emotion_id <> '' || $intro->emotion_id <> null || $intro->emotion_id <> 0)
                        @php
                            $emotionName = "default" ;

                                if (isset($intro->emotion_id)) {
                                    $emotion = \App\Models\Emotion::find($intro->emotion_id);
                                    if (isset($emotion)){
                                        $emotionName = $emotion['name'];
                                    }
                                }
                        @endphp
                        <div class="text">{{$emotionName}}</div>
                    @else
                        <div class="text" data-value="default">default</div>

                    @endif
                    <div class="menu" id="emotion">

                    </div>
                    <i class="chevron down icon"></i>
                </div>
            </div>
            <div class="field">
                <label>Position</label>
                <div class="ui selection dropdown">

                    @php
                        if ($intro->position == 0 || $intro->position == null){
                            $po = "left";
                        }
                        else{
                            $po = "right";
                        }
                    @endphp
                    <input type="hidden" name="position" data-value="{{$po}}" value="{{$po}}">
                    <div class="text" data-value="{{Str::headline($po)}}">{{Str::headline($po)}}</div>
                    <div class="menu">
                        <div class="item default" data-value="left">Left</div>
                        <div class="item" data-value="right">Right</div>
                    </div>
                    <i class="chevron down icon"></i>
                </div>
            </div>
            <div class="field">
                <label>Background</label>
                <div class="ui selection search dropdown">
                    <input type="hidden" name="background_id" data-value="{{$intro->background_id}}"
                           value="{{$intro->background_id}}">
                    @php
                        $po = "Background";
                        foreach($bg as $item){
                            if ($item['id'] == $intro->background_id){
                                $po = $item['name'];
                            }
                        }
                    @endphp
                    <div class="text" data-value="{{$po}}">Background</div>
                    <div class="menu">
                        @foreach($bg as $item)
                            <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                        @endforeach
                    </div>
                    <i class="chevron down icon"></i>
                </div>
            </div>
        </div>
        <div class="ui field accordion">
            <div class="title inputs">
                <div class="field main-local">
                    <label>Localization</label>
                    @if(isset($intro->text_en))
                        <textarea rows="2" name="text_en"
                                  data-value="{{$intro->text_en}}">{{$intro->text_en}}</textarea>
                    @else
                        <textarea rows="2" name="text_en"
                                  data-value=""></textarea>
                    @endif

                    <div class="ui label lang-iso">EN
                        <i class="chevron down icon"></i>
                    </div>
                </div>
            </div>
            <div class="content field inputs">
                @foreach($langs as $item)
                    @if($item['lcid'] <> "en")
                        <div class="field">
                            @if(isset($intro[$item["text"]]))
                                <textarea rows="2" data-value="{{$intro[$item["text"]]}}" name="text_{{$item['lcid']}}">{{$intro[$item["text"]]}}</textarea>
                            @else
                                <textarea rows="2" name="text_{{$item['lcid']}}"></textarea>
                            @endif
                            <div class="ui label lang-iso">{{$item['lcid']}}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="buttons action-buttons">
        <button class="positive ui button save" type="button" id="update">SAVE</button>
        <button class="grey ui button discard" type="button" id="discardsss" disabled>DISCARD</button>

        <a href="#" class="negative ui button delete intro">DELETE PAGE</a>
    </div>
</form>
<script type="application/javascript" src={{asset("js/semantic.min.js")}}></script>
<script type="application/javascript" src={{asset("js/main.js")}}></script>