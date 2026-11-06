<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>{{$dialogue->name_en}}</h3>
    </div>
    <div class="edit-area">
        <form action="{{route('dialogue-update',$dialogue->id)}}" class="ui form" method="post" id="dialogue_update_form">
            @csrf
            <input type="hidden" value="3" name="type">
            <div class="two fields">
                <div class="field">
                    <label>Dialogue name</label>
                    <input type="text" name="name_en" class="input" placeholder="Story name" autocomplete="off" data-value="{{$dialogue->name_en}}" value="{{$dialogue->name_en}}"
                           id="name_en_field_of_dialogue_update_form">
                </div>
                <div class="ui field accordion">
                    <div class="ui title inputs">
                        <div class="field main-local">
                            <label>Summary</label>
                            <textarea class="textarea" id="summary_en_field_of_dialogue_create_form"
                                      name="description_en"
                                      rows="2" data-value="{{$dialogue->description_en}}">{{$dialogue->description_en}}</textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                @php
                                    $localisationName = 'description_' . $item->lcid;
                                @endphp
                                <div class="field">
                                    <textarea class="textarea" rows="2" name="description_{{$item->lcid}}" data-value="{{$dialogue->$localisationName}}">{{$dialogue->$localisationName}}</textarea>
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" id="dialogue_save_button" disabled>SAVE</button>
                <button href="#" type="button" class="grey ui button discard" id="dialogue_discard_button" disabled>DISCARD</button>
                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="dialogue_delete_button">DELETE</button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>

<script>
    $('.discard.button').on('click', function () {
        if ($('form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                closable: false,
                onApprove: function () {
                    $.ajax({
                        type: "get",
                        url: "/admin/dialogue/edit/{{$dialogue->id}}",
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let form = $("#edit");
                            form.empty();
                            form.append(res)
                        },
                    });
                    $('.button.discard, .button.save').attr('disabled',true);
                }
            }).modal('show');
        }
    })
    $('.negative.button:not(.lena)').on('click',function () {
        $('.delete-modal').modal({
            closable: false,
            onApprove:function () {
                $.ajax({
                    type: "post",
                    url: "/admin/dialogue/delete/{{$dialogue->id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    }
                });
            }
        }).modal('show');
    })
</script>

