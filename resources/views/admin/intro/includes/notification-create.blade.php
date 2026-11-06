<form action="/admin/intro/store/modal/{{$next_id}}" class="ui form" method="POST" id="myForm">
    @csrf
    <input type="hidden" name="content_type" value="2" id="content_type">
    <input type="hidden" name="choice_id" value="" id="choice_id_form">
    <input type="hidden" name="init" value="" id="init_in_form">
    <input type="hidden" name="tree" value="" id="create_tree">
    <input type="hidden" name="sub_chapter_id" value="{{$subId}}" id="sub_id">


    <div class="two fields">
        <div class="field options">
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
                        @php
                            $textIso ="text_" . $item['lcid']
                        @endphp
                        <div class="field">
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

<script type="application/javascript" src={{asset("js/semantic.min.js")}} ></script>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>