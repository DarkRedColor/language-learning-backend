<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="option-area">
        <div class="arrows">
            <div class="info">
                <p>
                    New Page
                </p>
            </div>
        </div>

        <div class="ui selection dropdown" style="background: var(--line);">
            <input type="hidden" name="type" value="line" id="dialogue_type_select">
            <div class="default text">Type</div>
            <div class="menu">
                <div class="item" data-value="line">Line</div>
                <div class="item" data-value="action">Action</div>
                <div class="item" data-value="notification">Notification</div>
                <div class="item" data-value="choice">Choice</div>
            </div>
            <i class="chevron down icon"></i>
        </div>
    </div>

    <div class="edit-area">
        <form action="{{route('dialogue-page-store')}}" class="ui form" method="POST" id="dialogue_create_form"
              enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tree" value="" id="create_tree">
            <input type="hidden" name="content_type" value="line" id="content_type">
            <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
            <input type="hidden" name="parent_id" value="" id="dialogue_parent_id">
            <div class="two fields">
                <div class="field options">
                    <div class="field char_emotion">
                        <label>Character Emotions</label>
                        <div class="ui selection search dropdown char_emotion">
                            <input type="hidden" name="emotion_id" value="" id="emotion_id">
                            <i class="chevron down icon"></i>
                            <div class="default text">Character and Emotions</div>
                            <div class="menu">
                                @foreach($personages as $item)
                                    @foreach($item->emotions as $emotion)
                                        <div class="item" data-value="{{$emotion->id}}" id="personage">
                                            <span>{{$item->name_en}}</span> {{$emotion->name}}
                                            <span class="voice" hidden="">{{$item->voice}}</span>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
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
                    <div class="field char_bg">
                        <label>Background</label>

                        <div class="ui selection search dropdown">
                            <input type="hidden" name="background_id" value="" id="background_id">
                            <div class="default text">Background</div>
                            <div class="menu">
                                @foreach($bg as $item)
                                    <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                                @endforeach
                            </div>
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                    {{--        voice generation button                --}}
                    <div class="field">
                        <label>Generate audio for dialogue</label>
                        <div class="ui icon buttons spell games word task-audio">
                            <span class="ui icon button spelling word">
                                <p>Generate voice</p>
                            </span>
                            <button type="button" class="ui button large spell-word">
                                <i class="cloud download icon large"></i>
                            </button>
                            <button type="button" class="ui button negative large del-file">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <p class="filling-type">Hide Name</p>
                        <div class="ui buttons thumb">
                            <button type="button" class="ui button manual">Show</button>
                            <input type="hidden" id="tumbler" value="0" name="hide_name">
                            <div class="or"></div>
                            <button type="button" class="ui button selective">Hide</button>
                        </div>
                    </div>
                    {{--        voice generation button                --}}

                </div>
                <div class="ui field accordion">
                    <div class="title  inputs">
                        <div class="field main-local">
                            <label>Localization</label>
                            <textarea rows="2" name="text_en"></textarea>
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
                                    <textarea rows="2"
                                              name="text_{{$item['lcid']}}"></textarea>
                                    <div class="ui label lang-iso">{{$item['lcid']}}</div>
                                </div>
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" id="save" disabled="disabled">SAVE</button>
                <button class="negative ui button " id="cancel_dialogue_pages" type="button">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script type="application/javascript" src={{asset("js/voice-generation.js")}} ></script>
<script>

    $(document).ready(function () {
        // emotion
        var cashEmotion = localStorage.getItem('emotion');
        $('.char_emotion .text').removeClass('default').html(cashEmotion);
        var cashEmotionId = localStorage.getItem('emotion_id');
        $('#emotion_id').val(cashEmotionId)
        // background
        var cashBg = localStorage.getItem('bg');
        $('.char_bg .text').removeClass('default').html(cashBg);
        var cashBgId = localStorage.getItem('bg_id');
        $('#background_id').val(cashBgId)

        //============= check voice
        let charVoice;
        $('.char_emotion').dropdown({
            onChange: function () {
                charVoice = $(this).find('.text .voice').text()
                console.log(charVoice)
            }
        });
        //============= check voice

        $('#dialogue_sub_id').val($('#dialogue_sub_chapter_id').val())
        $('#dialogue_parent_id').val($('#dialogue_index_parent_id').val())
        if (localStorage.length > 0) {
            $('.button.save, .button.discard').attr('disabled', false)
        } else {
            $('.button.save, .button.discard').attr('disabled', true)
        }


        $('#cancel_dialogue_pages').on('click', function () {
            $('.discard-modal').modal({
                onApprove: function () {
                    $('#edit').empty();
                }
            }).modal('show');
        })

    })
    var selectiveBtn = $('.selective'),
        manualBtn = $('.manual'),
        pair = $('.loc-thumb');


    $(document).ready(function (){
        $('#tumbler').val(0)
        $('.button.manual').addClass('blue')
        pair.find('button:first-child').addClass('blue').after(function () {
            pair.hide();
        })
    })

    manualBtn.click(function () {
        switchButton(manualBtn);
        $('#tumbler').val(0)
    });
    selectiveBtn.click(function () {
        switchButton(selectiveBtn);
        $('#tumbler').val(1)
    });
    function switchButton(newButton) {

            newButton.siblings().removeClass('blue');
            newButton.addClass('blue');
            currentButton = newButton;
            pair.toggle();
            // selective.toggle();
            // manual.toggle();
            // rulesThumb.toggle();

    }

    // add class && show button
    $('.form input, textarea').not('.hidden').on('keyup change', function () {
        // catch emotion
        localStorage.setItem('emotion', $('.char_emotion .text').html());
        localStorage.setItem('emotion_id', $('#emotion_id').val());
        // catch background
        localStorage.setItem('bg', $('.char_bg .text').html());
        localStorage.setItem('bg_id', $('#background_id').val());

        $('.form').addClass('contentChanged')
        $('.button.save, #cancel_dialogue_pages').attr('disabled', false)
    })
</script>