<h1>Create</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area">
    <h5>Key word</h5>
</div>
<div class="edit-area">
    <form action="/admin/motivation/store" class="ui form" method="post" id="text_create_form">
        @csrf
        <div class="ui field accordion">
            <div class="title inputs">
                <div class="field main-local">
                    <label>Localization</label>
                    <textarea class="textarea" rows="2" name="name_en" id="name_en_field_of_text_create_form"></textarea>
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
    $('#name_en_field_of_text_create_form').on('keyup',function (){
        if ($('#name_en_field_of_text_create_form').val() !== ''){
            $('#text_save_button').removeAttr('disabled');
        }else{
            $('#text_save_button').attr('disabled', true);
        }
    })

</script>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>