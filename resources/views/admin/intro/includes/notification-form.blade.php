<form action="{{route('intro-update', $intro->id)}}" class="ui form contentChanged" id="updateForm" method="post">
    @csrf
    <input type="hidden" name="type" value="" id="type">
    <input type="hidden" name="tree" value="" id="tree">
    <div class="two fields">
        <div class="field options">
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
        <button class="positive ui button save" type="button" id="update" >SAVE</button>
        <button class="grey ui button discard" type="button" id="discardsss" disabled>DISCARD</button>
        <a href="#" class="negative ui button delete intro">DELETE PAGE</a>
    </div>
</form>

<script type="application/javascript"  src={{asset("js/semantic.min.js")}} ></script>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>