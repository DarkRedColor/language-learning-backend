<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h5>{{$gameDescription->key}}</h5>
    </div>
    <div class="edit-area">
        <form action="/admin/game-description/update/{{$gameDescription->id}}" class="ui form" method="post"
              data-value="{{$gameDescription->id}}">
            @csrf
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localization</label>
                        <textarea class="textarea" rows="2" name="description_en"
                                  id="name_en_field_of_text_update_form"
                                  data-value="{{$gameDescription->description_en}}">{{$gameDescription->description_en}}</textarea>
                        <div class="ui label lang-iso">EN
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                </div>
                <div class="content field inputs">
                    @foreach($languages as $item)
                        @if($item->lcid <> 'en')
                            @php
                                $nameIso = 'description_' . $item->lcid
                            @endphp
                            <div class="field">
                            <textarea class="textarea" rows="2"
                                      name="description_{{$item->lcid}}"
                                      data-value="{{$gameDescription->$nameIso}}">{{$gameDescription->$nameIso}}</textarea>
                                <div class="ui label lang-iso">{{$item->lcid}}</div>
                            </div>

                        @endif
                    @endforeach
                </div>
            </div>
            <div class="buttons action-buttons">

                <button type="submit" class="positive ui button save" id="text_update_button" disabled> SAVE</button>

                <button class="grey ui button discard" type="button" id="text_discard" disabled> DISCARD</button>

                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" id="text_delete_button"> DELETE PAGE
                </button>

            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {
        // add class && show button
        $('.form textarea').on('keyup', function () {
            if ($(this).val() !== '') {
                $('.form').addClass('contentChanged')
                $('.button.save, .button.discard').attr('disabled', false)
            } else {
                $('.button.save').attr('disabled', true)
            }
        })
    })
    $(".button.discard").on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                $.ajax({
                    type: "get",
                    url: "/admin/game-description/edit/{{$gameDescription->id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#edit");
                        text.empty();
                        text.append(res);
                    }
                })
            }
        }).modal('show');
    })

    $('#name_en_field_of_text_update_form').on('keyup', function () {
        if ($('#name_en_field_of_text_update_form').val() !== '') {
            $('#text_update_button').removeAttr('disabled');
            $('#text_discard').removeAttr('disabled');
        } else {
            $('#text_update_button').attr('disabled', true);
        }
    })

    $('#text_delete_button:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                let id = $('.form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/game-description/delete/" + id,
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
