<h1>Add Theme</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area">
    <h3>Create theme</h3>
</div>
<div class="edit-area adventure">
    <form action="{{route('arcade-storage-store')}}" class="ui form" id="chapter_create_form"
          method="post">
        @csrf
        <div class="ui field accordion eight wide">
            <div class="title inputs">
                <div class="field main-local">
                    <label>Name Theme</label>
                    <textarea class="textarea" rows="2" name="name_en"
                              id="name_en_field_of_season_create_form"></textarea>
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
            <button type="submit" class="positive ui button save" disabled id="chapter_save_button">SAVE</button>
            <a href="#" class="negative ui button" id="chapter_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    // ====== change form state
    $('textarea').on('keyup', () => {
        $('.form').addClass('contentChanged')
        $('.save.button').attr('disabled', false)
    })
    $('#chapter_cancel_button').on('click', function () {
        var clearForm = $('#arcade_storage_edit_create_zone');
        if ($('.form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    clearForm.empty();
                }
            }).modal('show');
        } else {
            clearForm.empty();
        }
    })
</script>