<h1>Edit Choice</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div class="ui left attached icon button arr-down vis">
            <i class="arrow down icon"></i>
        </div>
        <div class="ui right attached icon button arr-up vis">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p>
                Position 1
            </p>
        </div>
    </div>

    <div class="ui selection dropdown" style="background: var(--choice);">
        <input type="hidden" name="type" value="choice" id="dialogue_type_select">
        <div class="text">Choice</div>
    </div>
</div>
<div class="edit-area">
    <form action="{{route('dialogue-page-store')}}" class="ui form" method="POST" id="dialogue_create_form">
        @csrf
        <input type="hidden" name="tree" value="" id="create_tree">
        <input type="hidden" name="content_type" value="choice" id="content_type">
        <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
        <input type="hidden" name="parent_id" value="" id="dialogue_parent_id">

        <div class="field eight wide">
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localisations:</label>
                        <textarea class="textarea" rows="2" name="name_en"
                                  id="name_en_field_of_season_create_form"></textarea>
                        <div class="ui label lang-iso">EN
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                </div>
                <div class="content field inputs">
                    <div class="field">
                        <textarea class="textarea" rows="2" name="name_"></textarea>
                        <div class="ui label lang-iso">es-MX</div>
                    </div>
                    <div class="field">
                        <textarea class="textarea" rows="2" name="name_"></textarea>
                        <div class="ui label lang-iso">es-PT</div>
                    </div>
                    <div class="field">
                        <textarea class="textarea" rows="2" name="name_"></textarea>
                        <div class="ui label lang-iso">de-DE</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" id="save">SAVE</button>
            <button type="button" class="discard grey ui button" id="discard">discard</button>
            <button class="negative ui button delete" type="button">delete</button>
        </div>
    </form>
</div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}}></script>
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

        $('#dialogue_sub_id').val($('#dialogue_sub_chapter_id').val())
        $('#dialogue_parent_id').val($('#dialogue_index_parent_id').val())
        $('.button.save, .button.discard').attr('disabled', true)
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
