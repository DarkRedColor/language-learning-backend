<h1>Edit word</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area">
    <h3>{{$word->word}}</h3>
</div>
<div class="edit-area adventure">
    <form action="{{route('arcade-theme-word-update',[$theme,$word->id])}}" class="ui form" id="chapter_create_form"
          method="post">
        @csrf
        <div class="field eight wide">
            <label for="text">Add new word for theme</label>
            <input type="text" name="word" placeholder="Type new word" value="{{$word->word}}">
        </div>
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="arcade_word_save_button">SAVE</button>
            <a href="#" class="negative ui button" id="arcade_word_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    // ====== change form state
    $('input[name="word"]').on('keyup', function(){
        $('.form').addClass('contentChanged')
        if($(this).val() == ""){
            $('.save.button').attr('disabled', true)
        }else{
            $('.save.button').attr('disabled', false)
        }
    })
    $('#arcade_word_cancel_button').on('click', function () {
        const clearForm = $('#arcade_word_edit_create_zone');
        if ($('#chapter_create_form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    $('form').removeClass('contentChanged')
                    clearForm.empty();
                }
            }).modal('show');
        } else {
            $('form').removeClass('contentChanged')
            clearForm.empty();
        }
    })
</script>