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
                position list {{$intro->order + 1}}
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
                <div class="field disabled">
                    <label>Variants</label>
                    <div class="ui selection dropdown">
                        <input type="hidden" name="v">
                        <div class="text">{{count($intro->next_id)}}</div>
                        <div class="menu">
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
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
                            <div class="text">default</div>

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
        </div>
        <div class="buttons action-buttons">
            <button class="positive ui button save" type="button" id="update" disabled>SAVE</button>
            <button class="grey ui button discard" type="button" id="discardsss" disabled>DISCARD</button>

            <a href="#" class="negative ui button delete intro">DELETE PAGE</a>
        </div>
    </form>
</div>
<script type="application/javascript" src={{asset("js/semantic.min.js")}} ></script>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script type="application/javascript" src={{asset("js/emotion.js")}}></script>