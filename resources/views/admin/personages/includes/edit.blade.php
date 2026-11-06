<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit" id="character_exit_button"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>{{$personage->name_en}}</h3>
    </div>
    <div class="edit-area">
        <form action="/admin/personage/update/{{$personage->id}}" class="ui form" id="character_edit_form"
              method="post">
            @csrf
            <div class="eight wide field">
                <label>Personage voice</label>
                <input type="text" placeholder="voice name" name="voice" value="{{$personage->voice}}">
            </div>
            <div class="field">
                <div class="ui field accordion char-name">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Name</label>
                            <input type="text" placeholder="Character Name - English" name="name_en"
                                   id="name_en_field_of_character_edit_form" data-value="{{$personage->name_en}}"
                                   autocomplete="false" value="{{$personage->name_en}}">
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                @php
                                    $nameIso = 'name_' . $item->lcid;
                                @endphp
                                <div class="field">
                                    <input type="text" name="name_{{$item->lcid}}" data-value="{{$personage->$nameIso}}"
                                           value="{{$personage->$nameIso}}"
                                           placeholder="Character Name - {{$item->display_name}}">
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button" id="character_update_button" disabled>SAVE</button>
                <button type="button" class="grey ui button " id="character_discard_button" disabled>DISCARD</button>
                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="character_delete_button">DELETE
                </button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>

<script>
    $('#character_discard_button').on('click', function () {
        let disButton = $(this);
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    let uForm = $(".form").find('input:not([name$="_token"])');
                    uForm.each(function () {
                        $(this).val($(this).attr("data-value"))
                    })
                    disButton.attr('disabled', true)
                    $('#character_update_button').attr('disabled', true)
                }
            }).modal('show');
        }
    })

    $("#character_delete_button:not(.lena)").on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "{{route('personage-destroy',$personage->id)}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload()
                    }
                })
            }
        }).modal('show');
    })


</script>


