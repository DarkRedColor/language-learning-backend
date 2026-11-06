<h1>Create</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area" style="background: var(--choice);">
    <h3>
        Choices
    </h3>
</div>
<div class="edit-area">
    <form action="" class="ui form" id="arcade_game_create_form"
          method="post">
        @csrf
        <input type="text" hidden name="is_deleted" id=""/>
        <input type="text" hidden name="dd_file" id=""/>
        <input type="text" hidden name="type" id="" value="2">
        <div class="field eight wide">
            <label>Variants</label>
            <div class="ui selection dropdown">
                <input type="hidden" name="variants" value="2">
                <div class="text">2</div>
                <div class="menu">
                    <div class="item default" data-value="2">2</div>
                    <div class="item" data-value="3">3</div>
                    <div class="item" data-value="4">4</div>
                </div>
                <i class="chevron down icon"></i>
            </div>
        </div>
        <div class="field eight wide">
            <div class="field">
                <label>Character Emotion:</label>
                <div class="ui selection search dropdown char_emotion">
                    <input type="hidden" name="emotion_id" value="" id="emotion_id">
                    <div class="text default">Select character and emotion</div>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <div class="item" data-value="1" id="personage">
                            <span>Julia</span>Happy
                        </div>
                        <div class="item" data-value="2" id="personage">
                            <span>Langly</span>Angry
                        </div>
                    </div>
                </div>
            </div>
            <div class="field">
                <label>Position:</label>
                <div class="ui selection search dropdown">
                    <input type="hidden" name="arcade_bg_id" value="" id="position_id">
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

        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="arcade_game_save_button">SAVE</button>
            <a href="#" class="negative ui button discard" id="arcade_game_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    // ====== change links from dropdown
    $('.arcade-create-select')
    // ====== change form state
    $('.dropdown').on('change', () => {
        $('.form').addClass('contentChanged')
        $('.save.button').attr('disabled', false)
    })
    $(document).ready(() => {
        $('.ui.dropdown').dropdown({
            clearable: true
        });
    })

    $('#arcade_game_cancel_button').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $(".discard_modal").modal({
                onApprove: function () {

                }
            }).modal('show');
        } else {

        }
    })
</script>