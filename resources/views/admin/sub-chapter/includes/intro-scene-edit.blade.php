<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div id="scene_arrow_down" class="big ui left attached icon button">
            <i class="arrow down icon"></i>
        </div>
        <div id="scene_arrow_up" class="big ui right attached icon button">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p class="position-list">Position list {{$scene->order + 1}}</p>
        </div>
    </div>
    <div class="ui selection dropdown" style="background: var(--scene);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Scene">
        <div class="text">Scene</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="/admin/sub-chapter/update/{{$scene->id}}/{{$season->id}}/{{$episode->id}}/{{$chapter->id}}"
          class="ui form" id="scene_edit_form" data-value="{{$scene->id}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <input class="array-list" type="hidden" name="tree" value="" id="scene_list">
        <input type="hidden" name="tags" value="" id="intro_scene_tags">
        <div class="two fields">
            <div class="ui field">
                <div class="inline field file">
                    <label id="dropped">
                        <input type="file" name="image" accept="image/png"
                               onchange="previewFile(this);$('#delete_flag_of_language').remove()" placeholder="IMG">
                        <i class="upload icon">
                            @if($scene->image_unlocked <> null)
                            <img class="previewImg"
                                data-value="{{Storage::disk('gcs')->url("sub-chapter/$chapter->id" . "_S.webp")}}"
                                src="{{Storage::disk('gcs')->url("sub-chapter/$chapter->id" . "_S.webp")}}"/>
                            @else
                                <img class="previewImg" src=""/>
                            @endif
                        </i>
                        <a href="#" class="dis-img" onclick="delLanguageFlag()"
                           id="scene_delete_image_edit_form_button"><i
                                    class="close icon"></i></a>
                    </label>
                    {{--                    <a href="#" class="dis-img" onclick="delLanguageFlag()" ><i class="close icon"></i></a>--}}
                </div>
            </div>
            <div class="ui field">
                <div class="field">
                    <label>Tags</label>
                    <div id="tags">
                        @if(isset($scene->tags))
                            @foreach($scene->tags as $item)
                                <span>{{$item}}</span>
                            @endforeach
                        @endif
                        <input type="text" placeholder="Add a tag"/>
                        <input class="hidden"/>
                    </div>
                </div>
                <div class="field">
                    <label>Dialogues</label>
                    <div class="ui selection search dropdown">

                        <input type="hidden" name="dialogue_id"
                               @if(isset($selectedDialogue)) value="{{$selectedDialogue->id}}" @endif>
                        @if(isset($selectedDialogue))
                            <div class="text">
                                {{$selectedDialogue->name_en}}
                            </div>
                        @else
                            <div class="default text">Dialogues</div>
                        @endif
                        <div class="menu">
                            @foreach($dialogues->where('sub_chapter_id', null) as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Short Dialogues</label>
                    <div class="ui selection search dropdown">

                        <input type="hidden" name="short_dialogue_id"
                               @if(isset($selectedShortDialogue)) value="{{$selectedShortDialogue->id}}" @endif>
                        @if(isset($selectedShortDialogue))
                            <div class="text">
                                {{$selectedShortDialogue->name_en}}
                            </div>
                        @else
                            <div class="default text">Dialogues</div>
                        @endif
                        <div class="menu">
                            @foreach($dialogues->where('sub_chapter_id', null) as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="ui accordion">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Localization</label>
                            <textarea class="textarea" rows="2" name="name_en"
                                      data-value="{{$scene->name_en}}"
                                      id="name_en_field_of_scene_create_form">{{$scene->name_en}}</textarea>
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
                                <textarea class="textarea" data-value="{{$scene->$localisationName}}" rows="2"
                                          name="name_{{$item->lcid}}">{{$scene->$localisationName}}</textarea>
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">

            <button type="button" class="positive ui button save" id="scene_update_button" disabled> SAVE</button>

            <button class="grey ui button" type="button" id="scene_discard" disabled> DISCARD</button>
            @if(!Auth::user()->hasRole('content'))
                <button type="button" class="negative ui button delete  @if(!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="scene_delete_button"> DELETE PAGE
                </button>
            @endif
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}}></script>
<script type="application/javascript" src={{asset("js/adventures_order_list.js")}}></script>
<script>
    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null);
        $('#scene_discard').removeAttr('disabled');
    }

    $("#scene_discard").on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                $.ajax({
                    type: "get",
                    url: "/admin/sub-chapter/edit/{{$scene->id}}/{{$season->id}}/{{$episode->id}}/{{$chapter->id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
                // change img onDiscard
                $('.previewImg').attr('src', $('.previewImg').attr('data-value'));
                let input = $('.textarea')
                input.each(function () {
                    $(this).val($(this).attr('data-value'))
                })
                let form = $('#scene_edit_form')

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
                $('#scene_edit_form').removeClass('contentChanged');
                $('#scene_update_button').attr('disabled', true);
                $('#scene_discard').attr('disabled', true);

            }
        }).modal('show');
    })

    $('#name_en_field_of_scene_edit_form').on('keyup', function () {
        if ($('#name_en_field_of_scene_edit_form').val() !== '') {
            $('#scene_update_button').removeAttr('disabled');
        } else {
            $('#scene_update_button').attr('disabled', true);
        }
    })

    $('.button.delete:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                let id = $('#scene_edit_form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/sub-chapter/delete/" + id,
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
    $("#scene_delete_image_edit_form_button").on('click', function () {
        $('#scene_update_button').removeAttr('disabled');
        $('#scene_edit_form').addClass('contentChanged');

    })


    $("#scene_arrow_down").on('click', function () {
        let id = "{{$scene->id}}";
        let elem = $("." + id);
        let next = elem.next();

        if (next.attr("id") !== "add_new_scene_button") {
            let form = $("#scene_edit_form")
            form.addClass("contentChanged");
            form.addClass("contentMoved");
            $("#scene_update_button").removeAttr('disabled')
            $("#scene_discard").removeAttr('disabled')
            elem.insertAfter(next);
        }

    })
    $("#scene_arrow_up").on('click', function () {
        let id = "{{$scene->id}}";
        let elem = $("." + id);
        let prev = elem.prev();

        if (prev.attr("data-value") !== undefined) {
            let form = $("#scene_edit_form")
            form.addClass("contentChanged");
            form.addClass("contentMoved");
            $("#scene_update_button").removeAttr('disabled')
            $("#scene_discard").removeAttr('disabled')
            elem.insertBefore(prev);
        }

    });

    $("body").on('DOMSubtreeModified', "#tags", function () {
        $('.form').addClass('contentChanged')
        $('.save.button').removeAttr('disabled');
        $('#scene_discard').removeAttr('disabled');
    });

    $(function () {
        $("#tags input").on({
            focusout: function () {
                var txt = this.value.replace(/[^a-z0-9\+\-\.\,\ ]/ig, ''); // allowed characters
                if (txt) $("<span/>", {text: txt.toLowerCase(), insertBefore: this});
                this.value = "";
            },
            keyup: function (ev) {
                // if: comma|enter (delimit more keyCodes with | pipe) /(188|13)/
                if (/(13)/.test(ev.which)) $(this).focusout();
            }
        });
        $('#tags').on('click', 'span', function () {
            // if(confirm("Remove "+ $(this).text() +"?"))
            $(this).remove();
        });
    })
    $(".save.button").on('click', function () {
        let arr = [];
        $('#tags span').each(function () {
            arr.push($(this).text());
        });
        $('#intro_scene_tags').val(JSON.stringify(arr));
        $('#scene_edit_form').submit();
    })
</script>
