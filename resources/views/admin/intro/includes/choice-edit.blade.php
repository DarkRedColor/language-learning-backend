<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<input type="hidden" id="intro-id" value="{{$intro->id}}">
<div class="option-area">
    <div class="arrows">
        <div class="ui left attached icon button" id="arrow_down" name="{{$intro->id}}">
            <i class="arrow down icon"></i>
        </div>
        <div class="ui right attached icon button" id="arrow_up" name="{{$intro->id}}">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p>
                position list {{$intro->order +1}}
            </p>
        </div>
    </div>

    <div class="ui selection dropdown" style="background: var(--choice);">
        <input type="hidden" name="type" value="choice" id="update_select">
        <div class="text">Choice</div>
    </div>
</div>

<div class="edit-area">
    <form action="{{route('intro-update', $intro->id)}}" class="ui form" id="updateForm" method="post">
        @csrf
        <input type="hidden" name="type" value="choice" id="type">
        <input type="hidden" name="tree" value="" id="tree">
        <input type="hidden" name="sub_chapter_id" value="{{$subId}}" id="sub_id">

        <div class="two fields">
            <div class="field options">
                <div class="field">
                </div>
                <!-- empty space ( just for theme symmetry ) -->
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
            <button class="positive ui button save" type="button" id="update" disabled>SAVE</button>
            <button class="grey ui button discard" type="button" id="discardsss" disabled>DISCARD</button>

{{--                        <a href="#" class="negative ui button delete">DELETE PAGE</a>--}}
        </div>
    </form>


    <script type="application/javascript"  src={{asset("js/semantic.min.js")}}></script>
    <script type="application/javascript"  src={{asset("js/main.js")}}></script>
</div>
