<form action="/admin/intro/store/modal/{{$next_id}}" class="ui form" method="POST" id="myForm">
    @csrf
    <input type="hidden" name="content_type" value="1" id="content_type">
    <input type="hidden" name="choice_id" value="" id="choice_id_form">
    <input type="hidden" name="init" value="" id="init_in_form">
    <input type="hidden" name="tree" value="" id="create_tree">
    <input type="hidden" name="sub_chapter_id" value="{{$subId}}" id="sub_id">


    <div class="two fields">
        <div class="field options">
            <div class="field">
                <label>Character</label>
                <div class="ui selection search dropdown">
                    @if(isset($data->personage_id))
                        <input type="hidden" name="personage_id" value="{{$data->personage_id ?? null}}"
                               id="personage_id">
                    @else
                        <input type="hidden" name="personage_id" value="" id="personage_id">
                    @endif
                    <div class="default text">Character</div>
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
                    @if(isset($data->emotion_id))
                        <input type="hidden" name="emotion_id" id="emotion_value" value="{{$data->emotion_id ?? null}}">
                    @else
                        <input type="hidden" name="emotion_id" id="emotion_value" value="">
                    @endif
                    <div class="default text">Emotion</div>
                    <div class="menu" id="emotion">

                    </div>

                    <i class="chevron down icon"></i>
                </div>
            </div>
            <div class="field">
                <label>Position</label>

                <div class="ui selection dropdown">
                    <input type="hidden" name="position">
                    <div class=" text">Left</div>
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
                    @if(isset($data->background_id))
                        <input type="hidden" name="background_id" value="{{$data->background_id ?? null}}">
                    @else
                        <input type="hidden" name="background_id" value="">
                    @endif
                    <div class="default text">Background</div>
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
                    @if(isset($data->text_en))
                        <textarea rows="2" name="text_en">{{$data->text_en ?? null}}</textarea>
                    @else
                        <textarea rows="2" name="text_en"></textarea>
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
                            @php
                                $textIso ="text_" . $item['lcid']
                            @endphp
                            @if(isset($data->$textIso))
                                <textarea rows="2"
                                          name="text_{{$item['lcid']}}">{{$data->$textIso ?? null}}</textarea>
                            @else
                                <textarea rows="2"
                                          name="text_{{$item['lcid']}}"></textarea>
                            @endif

                            <div class="ui label lang-iso">{{$item['lcid']}}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="buttons action-buttons">
        <button type="button" class="positive ui button save" id="save">SAVE</button>
        <button class="grey ui button discard" type="button" id="create_cancel" disabled>Cancel</button>

        {{--        <a href="#" class="negative ui button delete">DELETE PAGE</a>--}}
    </div>
</form>
<script type="application/javascript" src={{asset("js/semantic.min.js")}}></script>
<script type="application/javascript" src={{asset("js/main.js")}}></script>
<script type="application/javascript" src={{asset("js/emotion.js")}}></script>
