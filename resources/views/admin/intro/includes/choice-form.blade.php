<form action="{{route('intro-update', $intro->id)}}" class="ui form contentChanged" id="updateForm" method="post">
    @csrf
    <input type="hidden" name="type" value="choice" id="type">
    <input type="hidden" name="tree" value="" id="tree">
    <input type="hidden" name="sub_chapter_id" value="{{$subId}}" id="sub_id">

    <div class="two fields">
        <div class="field options">
            <div class="field">
                <label>Variants</label>
                <div class="ui selection dropdown">
                    <input type="hidden" name="v">
                    <div class="text">2</div>
                    <div class="menu">
                    </div>
                    <i class="chevron down icon"></i>
                </div>
            </div>
            <div class="field">
                <label>Character</label>

                <div class="ui selection search dropdown">
                    <input type="hidden" name="personage_id" value="{{$intro->personage_id}}" id="personage_id">
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
                        <div class="text">default</div>

                    @endif
                    <div class="menu" id="emotion">

                    </div>
                    <i class="chevron down icon"></i>
                </div>
            </div>
            <div class="field">
                <label>Position</label>
                <div class="ui selection dropdown" style="background: var(--choice);">
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
                    <input type="hidden" name="background_id" value="{{$intro->background_id}}">
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
    </div>
    <div class="buttons action-buttons">
        <button class="positive ui button save" type="button" id="update">SAVE</button>
        <button class="grey ui button discard" type="button" id="discardsss" disabled>DISCARD</button>

        <a href="#" class="negative ui button delete intro">DELETE PAGE</a>
    </div>
</form>
<script type="application/javascript" src={{asset("js/semantic.min.js")}}></script>
<script type="application/javascript" src={{asset("js/main.js")}}></script>
<script type="application/javascript" src={{asset("js/emotion.js")}}></script>