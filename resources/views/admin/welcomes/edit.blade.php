<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area">
    <h5>{{$text->key}}</h5>
</div>
<div class="edit-area">
    <form action="/admin/welcome/update/{{$text->id}}" class="ui form" method="post" id="text_update_form"
          data-value="{{$text->id}}">
        @csrf
        <div class="ui field accordion">
            <div class="title inputs">
                <div class="field main-local">
                    <label>Localization</label>
                    <textarea class="textarea" rows="2" name="name_en"
                              id="name_en_field_of_text_update_form"
                              data-value="{{$text->name_en}}">{{$text->name_en}}</textarea>
                    <div class="ui label lang-iso">EN
                        <i class="chevron down icon"></i>
                    </div>
                </div>
            </div>
            <div class="content field inputs">
                @foreach($languages as $item)
                    @if($item->lcid <> 'en')
                        @php
                            $nameIso = 'name_' . $item->lcid
                        @endphp
                        <div class="field">
                            <textarea class="textarea" rows="2"
                                      name="name_{{$item->lcid}}"
                                      data-value="{{$text->$nameIso}}">{{$text->$nameIso}}</textarea>
                            <div class="ui label lang-iso">{{$item->lcid}}</div>
                        </div>

                    @endif
                @endforeach
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

<script>

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
                    url: "/admin/welcome/delete/" + id,
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
