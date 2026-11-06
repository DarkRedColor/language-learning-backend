<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div id="chapter_arrow_down" class="big ui left attached icon button">
            <i class="arrow down icon"></i>
        </div>
        <div id="chapter_arrow_up" class="big ui right attached icon button">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p class="position-list">Position list {{$chapter->order + 1}}</p>
        </div>
    </div>
    <div class="ui selection dropdown" style="background: var(--chapter);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Chapter">
        <div class="text">Chapter</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="/admin/chapter/update/{{$chapter->id}}/{{$season_id}}/{{$episode_id}}" class="ui form"
          id="chapter_edit_form" data-value="{{$chapter->id}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <input class="array-list" type="hidden" name="tree" value="" id="chapter_list">
        <div class="two fields">
            <div class="ui field">
                <div class="inline field file">
                    <label id="dropped">
                        <input type="file" name="image" accept="image/png"
                               onchange="previewFile(this);$('#delete_flag_of_language').remove()" placeholder="IMG">
                        <i class="upload icon">
                            @if($chapter->image_unlocked <> null)
                                <img class="previewImg"
                                     data-value="{{Storage::disk('gcs')->url("chapter/image/$chapter->id"."_S.webp")}}"
                                     src="{{Storage::disk('gcs')->url("chapter/image/$chapter->id"."_S.webp")}}"/>
                            @else
                                <img class="previewImg" src=""/>
                            @endif
                        </i>
                        <a href="#" class="dis-img" onclick="delLanguageFlag()"
                           id="chapter_delete_image_edit_form_button"><i
                                    class="close icon"></i></a>
                    </label>
                    {{--                    <a href="#" class="dis-img" onclick="delLanguageFlag()" ><i class="close icon"></i></a>--}}
                </div>
                <div class="field">
                    <label>Character Emotions</label>
                    <div class="ui selection search dropdown char_emotion">
                        <input type="hidden" name="emotion_id" value="{{$chapter->emotion_id}}" id="emotion_id">
                        @if(isset($chapter->personage_id))
                            <div class="text">
                                <span>{{$personages->where('id',$chapter->personage_id)->first()->name_en}}</span>{{$personages->where('id',$chapter->personage_id)->first()->emotions[0]->where('id',$chapter->emotion_id)->first()->name}}
                            </div>
                        @else
                            <div class="default text">Character and Emotions</div>
                        @endif
                        <div class="menu">
                            @foreach($personages as $item)
                                @foreach($item->emotions as $emotion)
                                    <div class="item" data-value="{{$emotion->id}}" id="personage">
                                        <span>{{$item->name_en}}</span>{{$emotion->name}}</div>
                                @endforeach
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Introductions</label>
                    <div class="ui selection search dropdown char_emotion">
                        <input type="hidden" name="introduction_id" value="{{$chapter->introduction_id}}"
                               id="introduction_id">
                        @if(isset($chapter->introduction_id))
                            <div class="text">
                                @if($introductions->where('id',$chapter->introduction_id)->first() !== null)
                                    {{$introductions->where('id',$chapter->introduction_id)->first()->key}}
                                @else
                                    <div class="default text">Introductions</div>
                                @endif
                            </div>
                        @else
                            <div class="default text">Introductions</div>
                        @endif
                        <div class="menu">
                            @foreach($introductions as $item)
                                <div class="item" data-value="{{$item->id}}" id="personage">
                                    {{$item->key}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
            </div>
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localization</label>
                        <textarea class="textarea" rows="2" name="name_en" data-value="{{$chapter->name_en}}"
                                  id="name_en_field_of_chapter_edit_form">{{$chapter->name_en}}</textarea>
                        <div class="ui label lang-iso">EN
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                </div>
                <div class="content field inputs">
                    @foreach($languages as $item)
                        @if($item->lcid <> 'en')
                            @php
                                $localisationName = 'name_' . $item->lcid;
                            @endphp
                            <div class="field">
                                <textarea class="textarea" data-value="{{$chapter->$localisationName}}" rows="2"
                                          name="name_{{$item->lcid}}">{{$chapter->$localisationName}}</textarea>
                                <div class="ui label lang-iso">{{$item->lcid}}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">

            <button type="button" class="positive ui button save" id="chapter_update_button" disabled> SAVE</button>

            <button class="grey ui button" type="button" id="chapter_discard" disabled> DISCARD</button>
            @if(!Auth::user()->hasRole('content'))

                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="chapter_delete_button"> DELETE PAGE
                </button>
            @endif
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script type="application/javascript" src={{asset("js/adventures_order_list.js")}}></script>

<script>
    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null);
        $('#chapter_discard').removeAttr('disabled');
    }

    $("#chapter_discard").on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                // change img onDiscard
                $('.previewImg').attr('src', $('.previewImg').attr('data-value'));
                let input = $('.textarea')
                input.each(function () {
                    $(this).val($(this).attr('data-value'))
                })
                let form = $('#chapter_edit_form')
                if (form.hasClass("contentMoved")) {
                    let id = form.attr('data-value');
                    let elem = $("." + id);
                    let position = elem.attr('id');
                    let nextOrPrevElement = $("#" + (parseInt(position) + 1))
                    if (nextOrPrevElement.length === 0) {
                        nextOrPrevElement = $("#" + (parseInt(position) - 1))
                        elem.insertAfter(nextOrPrevElement)
                    } else {
                        elem.insertBefore(nextOrPrevElement)
                    }
                    form.removeClass("contentMoved");
                }
                form.removeClass('contentChanged');
                $('#chapter_edit_form').removeClass('contentChanged');
                $('#chapter_update_button').attr('disabled', true);
                $('#chapter_discard').attr('disabled', true);
            }
        }).modal('show');
    })

    $('#name_en_field_of_chapter_edit_form').on('keyup', function () {
        if ($('#name_en_field_of_chapter_edit_form').val() !== '') {
            $('#chapter_update_button').removeAttr('disabled');
        } else {
            $('#chapter_update_button').attr('disabled', true);
        }
    })

    $('#chapter_delete_button:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                let id = $('#chapter_edit_form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/chapter/delete/" + id,
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
    $("#chapter_delete_image_edit_form_button").on('click', function () {
        $('#chapter_update_button').removeAttr('disabled');
        $('#chapter_edit_form').addClass('contentChanged');

    })

    $("#chapter_arrow_down").on('click', function () {
        let id = "{{$chapter->id}}";
        let elem = $("." + id);
        let next = elem.next();

        if (next.attr("id") !== "add_new_chapter_button") {
            let form = $("#chapter_edit_form")
            form.addClass("contentChanged");
            form.addClass("contentMoved");
            $("#chapter_update_button").removeAttr('disabled')
            $("#chapter_discard").removeAttr('disabled')
            elem.insertAfter(next);
        }

    })
    $("#chapter_arrow_up").on('click', function () {
        let id = "{{$chapter->id}}";
        let elem = $("." + id);
        let prev = elem.prev();

        if (prev.attr("data-value") !== undefined) {
            let form = $("#chapter_edit_form")
            form.addClass("contentChanged");
            form.addClass("contentMoved");
            $("#chapter_update_button").removeAttr('disabled')
            $("#chapter_discard").removeAttr('disabled')
            elem.insertBefore(prev);
        }

    })
</script>