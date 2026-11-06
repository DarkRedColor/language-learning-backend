<h1>Create</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="info">
        <p>Key word</p>
    </div>
    <div class="ui selection dropdown" style="background: var(--chapter);">
        <label>TYPE:</label>
        <input type="hidden" name="type" id="chapter_select_field" value="0">
        <div class="text">Chapter</div>
        <div class="menu">
            <div class="item" data-value="chapter">Chapter</div>
            <div class="item" data-value="quiz">Quiz</div>
            <div class="item" data-value="intro_chapter">Introduction to a chapter</div>
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>
<div class="edit-area">
    <form action="/admin/introduction/store" class="ui form" method="post" id="text_create_form">
        @csrf
        <input id="type" type="hidden" name="type" value="0">

        <div class="ui field accordion">
            <div class="title inputs">
                <div class="field main-local">
                    <label>Localization</label>
                    <textarea class="textarea" rows="2" name="name_en"
                              id="name_en_field_of_text_create_form"></textarea>
                    <div class="ui label lang-iso">EN
                        <i class="chevron down icon"></i>
                    </div>
                </div>
            </div>
            <div class="content field inputs">

                @foreach($languages as $item)
                    @if($item->lcid <> 'en')
                        <div class="field">
                            <textarea class="textarea" rows="2" name="name_{{$item->lcid}}"></textarea>
                            <div class="ui label lang-iso">{{$item->lcid}}</div>
                        </div>

                    @endif
                @endforeach

            </div>
        </div>
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="text_save_button">SAVE</button>
            <a href="#" class="negative ui button" id="text_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script>
    // ------- change type of dropdown
    var selector = $('#chapter_select_field'),
        input = $('#type');

    selector.on('change', function () {
        switch (selector.val()) {
            case 'chapter':
                input.val(0)
                break;
            case 'quiz':
                input.val(1)
                break;
            case 'intro_chapter':
                input.val(2)
                break;
                case 'final_quiz':
                input.val(3)
                break;
        }
    })
    // -----------

    $('#text_cancel_button').on('click', function () {
        let form = $('#text_create_form');
        if (form.hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    location.reload();
                }
            }).modal('show');
        } else {
            location.reload();
        }
    })
    $('#name_en_field_of_text_create_form').on('keyup', function () {
        if ($('#name_en_field_of_text_create_form').val() !== '') {
            $('#text_save_button').removeAttr('disabled');
        } else {
            $('#text_save_button').attr('disabled', true);
        }
    })

</script>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>