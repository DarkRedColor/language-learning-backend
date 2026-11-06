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
        <input type="hidden" name="type" value="Final Quiz">
        <div class="text">Final Quiz</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="/admin/episode/update/{{$episode->id}}/{{$season_id}}" class="ui form" id="episode_edit_form"
          data-value="{{$episode->id}}" method="post" enctype="multipart/form-data">
        @csrf
        <input hidden name="name_en" value="{{$episode->name_en}}">
        <input hidden name="type" value="1">
        <input class="array-list" type="hidden" name="tree" value="" id="episode_list">
        {{--images--}}
        <div class="two fields">
        </div>
        <div class="two fields">
            <div class="ui field accordion">

{{--                <div class="field char_bg">--}}
{{--                    <label>Background episode</label>--}}

{{--                    <div class="ui selection search dropdown">--}}
{{--                        <input type="hidden" name="background_episode_id" value="{{$episode->background_episode_id}}"--}}
{{--                               id="background_episode_id">--}}
{{--                        <div class="default text">Background episode</div>--}}
{{--                        <div class="menu">--}}
{{--                            @foreach($bg as $item)--}}
{{--                                <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                        <i class="chevron down icon"></i>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
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

            <button type="button" class="positive ui button save" id="episode_update_button" > SAVE</button>

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