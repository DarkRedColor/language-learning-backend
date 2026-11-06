<h1>Create</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<input type="hidden" value="{{$id}}" id="choice_id">
<input type="hidden" value="{{$initial}}" id="init">


<div class="option-area">
    <div class="arrows">
        <div class="ui left attached icon button">
            <i class="arrow down icon"></i>
        </div>
        <div class="ui right attached icon button">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p>
                position list 1
            </p>
        </div>
    </div>

    <div class="ui selection dropdown" style="background: var(--line);">
        <input type="hidden" name="type" value="line" id="select" next_id="{{$next_id}}">
        <div class="default text">Type</div>
        <div class="menu">
            <div class="item" data-value="line">Line</div>
            <div class="item" data-value="action">Action</div>
            <div class="item" data-value="notification">Notification</div>
            @if($noChoice)
                <div class="item" data-value="choice">Choice</div>
            @endif
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>

<div class="edit-area">
    <form action="/admin/intro/store/modal/{{$next_id}}" class="ui form" method="POST" id="myForm">
        @csrf
        <input type="hidden" name="choice_id" value="" id="choice_id_form">
        <input type="hidden" name="init" value="" id="init_in_form">
        <input type="hidden" name="tree" value="" id="create_tree">
        <input type="hidden" name="content_type" value="0" id="content_type">
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
                            <input type="hidden" name="emotion_id" id="emotion_value"
                                   value="{{$data->emotion_id ?? null}}">
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
                        <input type="hidden" name="background_id" value="{{$data->background_id ?? null}}">
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
                <div class="title  inputs">
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
            <button class="grey ui button" type="button" id="create_cancel">Cancel</button>

            {{--            <a href="#" class="negative ui button delete">DELETE PAGE</a>--}}
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/semantic.min.js")}}></script>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script type="application/javascript" src={{asset("js/emotion.js")}}></script>