<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area">
    <h5>{{$text->key}}</h5>
</div>
<div class="edit-area">
    <form action="/admin/subtitle/update/{{$text->id}}" class="ui form" method="post" id="text_update_form"
          data-value="{{$text->id}}">
        @csrf
        <input type="text" name="name_en" class="hid text">
        <div class="two fields">
            <div class="ui field">
                <div class="field">
                    <label>Name text</label>
                    <input type="text" placeholder="Name" name="key" value="{{$text->key}}">
                </div>
                <div class="editor files-text">
                    <div id="toolbar-container"></div>
                    <div id="editor">
                        @foreach($text->name_en as $description)
{{--                        @dd($text->name_en)--}}
                            {!! $description !!}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="buttons action-buttons">

            <button type="submit" class="positive ui button save" id="text_update_button" disabled> SAVE</button>

            <button class="grey ui button discard" type="button" id="text_discard" disabled> DISCARD</button>

            <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="text_delete_button"> DELETE PAGE
            </button>

        </div>
    </form>
</div>
<script type="application/javascript" src="{{asset('js/texts_editor.js')}}"></script>
<script>

    setTimeout(function(){
        $('.form, .form input').on('DOMSubtreeModified keyup', function() {
            $(this).addClass('contentChanged')
            $('.button.discard, .button.save').attr('disabled', false)
        })
    }, 500);

    $('.button.save').one('click', function () {

        var text = []
        $('.ck.ck-content').children().not('.ck-fake-selection-container').each(function () {
            $('.ck.ck-content img').replaceWith('@@').after(function () {
                $('[class^="ck"]').remove()
            })
            text.push($(this).html().replace('/<p>(.*?)<\/p>/g', '(.*?)'))
        })
        $('.hid.text').val(JSON.stringify(text))

        $('.form').submit();
    })

    $(".button.discard").on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                let input = $('.textarea')
                input.each(function () {
                    $(this).val($(this).attr('data-value'))
                })
                $('#text_update_form').removeClass('contentChanged');
                $('#text_update_button').attr('disabled', true);
                $('#text_discard').attr('disabled', true);
            }
        }).modal('show');
    })

    $('#name_en_field_of_text_update_form').on('keyup', function () {
        if ($('#name_en_field_of_text_update_form').val() !== '') {
            $('#text_update_button').removeAttr('disabled');
        } else {
            $('#text_update_button').attr('disabled', true);
        }
    })

    $('#text_delete_button:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                let id = $('#text_update_form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/subtitle/delete/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    }
                })
            }
        }).modal('show');
    });


</script>


<script type="application/javascript" src={{asset("js/main.js")}} ></script>
