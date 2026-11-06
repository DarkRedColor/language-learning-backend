<h1>Create arcade game</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="ui selection dropdown notification-type">
        <input type="hidden" name="type" value="0" id="notification_type_select">
        <div class="text">Standard</div>
        <div class="menu">
            <a class="item selected active" data-value="0">Standard</a>
            <a class="item" data-value="1">Tutorial</a>
        </div>
        <i class="chevron down icon"></i>
    </div>
    <div class="ui selection dropdown arcade-create-select" style="background: var(--notification);">
        <input type="hidden" name="type" value="type-fast" id="arcade_type_select">
        <div class="text">Notification</div>
        <div class="menu">
            <a class="item" data-value="intro">Intro</a>
            <a class="item selected active" data-value="notification">Notification</a>
            <a class="item" data-value="end-game">End Game</a>
            <a class="item" data-value="win-alert">Win Alert</a>
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>
<div class="edit-area">
    <form action="{{route('arcade-page-store')}}" class="ui form" id="arcade_game_create_form"
          method="post">
        @csrf
        <input type="hidden" name="content_type" value="notification" id="content_type">
        <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
        <input type="hidden" name="add_type" value="0" id="add_type">
        <div class="two fields">
            <div class="field eight wide">
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
                                @php
                                    $textIso ="text_" . $item['lcid']
                                @endphp
                                <div class="field">
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
        $('.button.save, .button.discard').attr('disabled', true)

        $('.form input, textarea').not('.hidden').on('keyup change', function () {
            console.log(this)
            $('.form').addClass('contentChanged')
            $('.button.save, .button.discard').attr('disabled', false)
        })
    })
    // ====== get type of notification
    $('#notification_type_select').on('change', function(){
        $('#add_type').val($(this).val())
    })
    // ====== change form state
    $('.dropdown').on('change', () => {
        $('.form').addClass('contentChanged')
        $('.save.button').attr('disabled', false)
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