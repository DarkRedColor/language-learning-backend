<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div id="episode_arrow_down" class="big ui left attached icon button">
            <i class="arrow down icon"></i>
        </div>
        <div id="episode_arrow_up" class="big ui right attached icon button">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p class="position-list">Position list {{$episode->order + 1}}</p>
        </div>
    </div>
    <div class="ui selection dropdown" style="background: var(--episode);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Episode">
        <div class="text">Episode</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="/admin/episode/update/{{$episode->id}}/{{$season_id}}" class="ui form" id="episode_edit_form"
          data-value="{{$episode->id}}" method="post" enctype="multipart/form-data">
        @csrf
        <input class="array-list" type="hidden" name="tree" value="" id="episode_list">
        <input hidden name="type" value="0">
        {{--images--}}
        <div class="two fields">
            <div class="field">
                <input type="text" hidden name="is_deleted" id="is_deleted_language_file"
                       class="hid"/>
                <input type="text" hidden name="dd_file" id="dd_file_language_file" class="hid"/>
                <label style="text-align: center;">Sticker</label>
                <label id="dropped1" class="add-bg ">
                    <input type="file" name="image" accept="image/png"
                           onchange="previewFile(this,'dd_file_language_file','previewImg');$('#delete_flag_of_language').remove()"
                           placeholder="IMG">
                    <i class="upload icon">
                        <img class="previewImg" data-value=""
                             @if(isset($episode->image_unlocked))src="{{Storage::disk('gcs')->url("episode/$episode->id".'_S.webp')}}" @endif>
                    </i>
                    <a href="#" class="dis-img" onclick="delLanguageFlag()"><i class="close icon"></i></a>
                </label>

            </div>
            <div class="field additional-img">
                @if(isset($episode->image_unlocked))
                    <input type="text" hidden name="is_deleted_additional" id="is_deleted_language_file_additional"
                           class="hid"/>
                    <input type="text" hidden name="dd_file_additional" id="dd_file_language_file_additional"
                           class="hid"/>
                    <label style="text-align: center;">Additional image</label>
                    <label id="dropped2" class="add-bg ">
                        <input type="file" name="image-additional" accept="image/png"
                               onchange="previewFile(this,'dd_file_language_file_additional','previewImgAdditional');$('#delete_flag_of_language').remove()"
                               placeholder="IMG">
                        <i class="upload icon">
                            <img class="previewImgAdditional" data-value=""
                                 @if(isset($episode->additional_image_unlocked))src="{{Storage::disk('gcs')->url("episode/additional/$episode->id"."_S.webp")}}" @endif>
                        </i>
                        <a href="#" class="dis-img" onclick="delLanguageFlagAdditional()"><i class="close icon"></i></a>
                    </label>
                @endif

            </div>
        </div>
        <div class="two fields">
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Summary</label>
                        <textarea class="textarea" rows="2" name="description_en"
                                  data-value="{{$episode->description_en}}"
                                  id="name_en_field_of_episode_edit_form">{{$episode->description_en}}</textarea>
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
                                <textarea class="textarea" data-value="{{$episode->$localisationName}}" rows="2"
                                          name="description_{{$item->lcid}}">{{$episode->$localisationName}}</textarea>
                                <div class="ui label lang-iso">{{$item->lcid}}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="field char_bg">
                    <label>Background episode</label>

                    <div class="ui selection search dropdown">
                        <input type="hidden" name="background_episode_id" value="{{$episode->background_episode_id}}"
                               id="background_episode_id">
                        <div class="default text">Background episode</div>
                        <div class="menu">
                            @foreach($bg as $item)
                                <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
            </div>
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Episode Name</label>
                        <textarea class="textarea" rows="2" name="name_en" data-value="{{$episode->name_en}}"
                                  id="name_en_field_of_episode_edit_form">{{$episode->name_en}}</textarea>
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
                                <textarea class="textarea" data-value="{{$episode->$localisationName}}" rows="2"
                                          name="name_{{$item->lcid}}">{{$episode->$localisationName}}</textarea>
                                <div class="ui label lang-iso">{{$item->lcid}}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="field char_bg">
                    <label>Background</label>

                    <div class="ui selection search dropdown">
                        <input type="hidden" name="background_id" value="{{$episode->background_id}}"
                               id="background_id">
                        <div class="default text">Background</div>
                        <div class="menu">
                            @foreach($bg as $item)
                                <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">

            <button type="button" class="positive ui button save" id="episode_update_button" disabled> SAVE</button>

            <button class="grey ui button" type="button" id="episode_discard" disabled> DISCARD</button>

            @if(!Auth::user()->hasRole('content'))
                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="episode_delete_button"> DELETE PAGE
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
        $('#dd_file_language_file').val(null)
    }

    function delLanguageFlagAdditional() {
        $('.previewImgAdditional').attr('src', '')
        $('#is_deleted_language_file_additional').val(1);
        $('#dd_file_language_file_additional').val(null)
    }

    @if(!isset($episode->image_unlocked))
    $('.previewImg').one('load', function () {
        $('.additional-img').append(
            `<input type="text" hidden name="is_deleted_additional" id="is_deleted_language_file_additional"
                       class="hid"/>
                <input type="text" hidden name="dd_file_additional" id="dd_file_language_file_additional" class="hid"/>
                <label style="text-align: center;">Additional image</label>
                <label id="dropped2" class="add-bg ">
                    <input type="file" name="image-additional" accept="image/png"
                           onchange="previewFile(this,'dd_file_language_file_additional','previewImgAdditional');$('#delete_flag_of_language').remove()"
                           placeholder="IMG">
                    <i class="upload icon">
                        <img class="previewImgAdditional" data-value="" src="">
                    </i>
                    <a href="#" class="dis-img" onclick="delLanguageFlagAdditional()"><i class="close icon"></i></a>
                </label>`
        )
    })
    @endif

    $('#episode_cancel_button').on('click', function () {
        if ($('#episode_create_form').hasClass('contentChanged')) {
            $("#discard_modal").modal({
                onApprove: function () {
                    $('#season_edit_create_zone').empty();
                }
            }).modal('show');
        } else {
            $('#season_edit_create_zone').empty();
        }
    })

    $("#episode_discard").on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                let input = $('.textarea')
                input.each(function () {
                    $(this).val($(this).attr('data-value'))
                })
                let form = $('#episode_edit_form')
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
                $('#episode_update_button').attr('disabled', true);
                $('#episode_discard').attr('disabled', true);
            }
        }).modal('show');
    })

    $('#name_en_field_of_episode_edit_form').on('keyup', function () {
        if ($('#name_en_field_of_episode_edit_form').val() !== '') {
            $('#episode_update_button').removeAttr('disabled');
        } else {
            $('#episode_update_button').attr('disabled', true);
        }
    })

    $('#episode_delete_button:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                let id = $('#episode_edit_form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/episode/delete/" + id,
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
    $("#episode_arrow_down").on('click', function () {
        let id = "{{$episode->id}}";
        let elem = $("." + id);
        let next = elem.next();

        if (next.attr("id") !== "add_new_episode_button") {
            let form = $("#episode_edit_form")
            form.addClass("contentChanged");
            form.addClass("contentMoved");
            $("#episode_update_button").removeAttr('disabled')
            $("#episode_discard").removeAttr('disabled')
            elem.insertAfter(next);
        }

    })
    $("#episode_arrow_up").on('click', function () {
        let id = "{{$episode->id}}";
        let elem = $("." + id);
        let prev = elem.prev();

        if (prev.attr("data-value") !== undefined) {
            let form = $("#episode_edit_form")
            form.addClass("contentChanged");
            form.addClass("contentMoved");
            $("#episode_update_button").removeAttr('disabled')
            $("#episode_discard").removeAttr('disabled')
            elem.insertBefore(prev);
        }

    })
</script>