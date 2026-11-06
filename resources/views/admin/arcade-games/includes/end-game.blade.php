<h1>Create arcade game</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div class="info">
            <p>
                Type of the game
            </p>
        </div>
    </div>

    <div class="ui selection dropdown arcade-create-select" style="background: var(--end-game);">
        <input type="hidden" name="type" value="type-fast" id="arcade_type_select">
        <div class="text">End Game</div>
        <div class="menu">
            <a class="item" data-value="intro">Intro</a>
            <a class="item" data-value="notification">Notification</a>
            <a class="item selected active" data-value="end-game">End Game</a>
            <a class="item" data-value="win-alert">Win Alert</a>
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>
<div class="edit-area">
    <form action="{{route('arcade-page-store')}}" class="ui form" id="arcade_game_create_form"
          method="post">
        @csrf
        <input type="hidden" name="content_type" value="end-game" id="content_type">
        <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
        <div class="two fields">
            <div class="field eight wide">
                <div class="field">
                    <label>Character Emotion:</label>
                    <div class="ui selection search dropdown char_emotion">
                        <input type="hidden" name="emotion_id" value="" id="emotion_id">
                        <div class="text default">Select character and emotion</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            @foreach($personages as $item)
                                @foreach($item->emotions as $emotion)
                                    <div class="item" data-value="{{$emotion->id}}" id="personage">
                                        <span>{{$item->name_en}}</span> {{$emotion->name}}</div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label>Position:</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="position" value="" id="position_id">
                        <div class="text default">Select personage position</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item" data-value="left" id="pos_left">
                                left
                            </div>
                            <div class="item" data-value="right" id="pos_right">
                                right
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field eight wide">
                <div class="ui field accordion">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Localisations:</label>
                            <textarea class="textarea" rows="2" name="text_en"
                                      id="name_en_field_of_season_create_form"></textarea>
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
        </div>


        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="arcade_game_save_button">SAVE</button>
            <a href="#" class="negative ui button" id="arcade_game_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready(function () {
        $('#dialogue_sub_id').val($('#dialogue_sub_chapter_id').val())

    })
    $('.form input, textarea').not('.hidden').on('keyup change', function () {
        $('.form').addClass('contentChanged')
        $('.button.save, #arcade_game_cancel_button').attr('disabled', false)
    })
    // ====== change links from dropdown
    $('.arcade-create-select')
    // ====== change form state
    $('.dropdown').on('change', () => {
        $('.form').addClass('contentChanged')
        $('.save.button').attr('disabled', false)
    })
    $(document).ready( () => {
        $('.ui.dropdown').dropdown({
            clearable: true
        });
    })

    $('#arcade_game_cancel_button').on('click', function () {
        const editAria = $('#arcade_storage_edit_create_zone')
        if ($('.form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    editAria.empty();
                }
            }).modal('show');
        } else {
            editAria.empty();
        }
    })
</script>