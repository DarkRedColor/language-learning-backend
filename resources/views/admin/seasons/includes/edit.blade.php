<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div class="big ui left attached icon button arr-down">
            <i class="arrow down icon"></i>
        </div>
        <div class="big ui right attached icon button arr-up">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p class="position-list">Position list {{$season->order + 1}}</p>
        </div>
    </div>
    <div class="ui selection dropdown" style="background: var(--season);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Season">
        <div class="text">Season</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="{{route('season-update',$season->id)}}" class="ui form" id="season_edit_form" method="post" enctype="multipart/form-data" data-value="{{$season->id}}">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <input class="array-list" type="hidden" name="tree" value="" id="season_list">
        <div class="two fields">
            <div class="ui field">
                <div class="inline field file">
                    <label id="dropped">
                        <input type="file" name="image" accept="image/png"
                               onchange="previewFile(this);$('#delete_flag_of_language').remove()" placeholder="IMG">
                        <i class="upload icon">
                            @if($season->image_unlocked <> null)
                                <img class="previewImg"
                                     data-value="{{Storage::disk('gcs')->url("season/$season->id".'_S.webp')}}"
                                     src="{{Storage::disk('gcs')->url("season/$season->id".'_S.webp')}}"/>
                            @else
                                <img class="previewImg" src=""/>
                            @endif
                        </i>
                        <a href="#" class="dis-img" onclick="delLanguageFlag()"
                           id="season_delete_image_edit_form_button"><i
                                    class="close icon"></i></a>
                    </label>

                </div>
            </div>
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localization</label>
                        <textarea class="textarea" rows="2" data-value="{{$season->name_en}}" name="name_en"
                                  id="name_en_field_of_season_edit_form">{{$season->name_en}}</textarea>
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

                                <textarea class="textarea" rows="2" data-value="{{$season->$localisationName}}"
                                          name="name_{{$item->lcid}}">{{$season->$localisationName}}</textarea>
                                <div class="ui label lang-iso">{{$item->lcid}}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">

            <button type="button" class="positive ui button save" id="season_update_button" disabled> SAVE</button>

            <button class="grey ui button discard" type="button" id="season_discard" disabled> DISCARD</button>
            @if(!Auth::user()->hasRole('content'))
                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id=""> DELETE PAGE
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
        $('#season_discard').removeAttr('disabled');
    }

    // $('#season_cancel_button').on('click', function () {
    //     if ($('#season_create_form').hasClass('contentChanged')) {
    //         $("#discard_modal").modal({
    //             onApprove: function () {
    //                 $('#season_edit_create_zone').empty();
    //             }
    //         }).modal('show');
    //     } else {
    //         $('#season_edit_create_zone').empty();
    //     }
    // })
    $(document).ready(function () {
        let values = $('.form').serializeArray();
        $(".button.discard").on('click', function () {
            $('.discard-modal').modal({
                closable: false,
                onApprove: function () {
                    for (let i = 0; i < values.length; i++) {
                        $("input[name='" + values[i].name + "'], select[name='" + values[i].name + "'], textarea[name='" + values[i].name + "']").val(values[i].value);
                    }
                    // change img onDiscard
                    $('.previewImg').attr('src', $('.previewImg').attr('data-value'));
                    // let input = $('.textarea')
                    // input.each(function () {
                    //     $(this).val($(this).attr('data-value'))
                    // })
                    let form = $('.form')

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
                    $('.button.save, .button.discard').attr('disabled', true);
                }
            }).modal('show');
        })
    })
    $('#name_en_field_of_season_edit_form').on('keyup', function () {
        if ($('#name_en_field_of_season_edit_form').val() !== '') {
            $('#season_update_button').removeAttr('disabled');
        } else {
            $('#season_update_button').attr('disabled', true);
        }
    })

    $("#season_delete_image_edit_form_button").on('click', function () {
        $('#season_update_button').removeAttr('disabled');
        $('#season_edit_form').addClass('contentChanged');

    })


    $('.negative.button:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            closable: false,
            onApprove: function () {
                let id = $('#season_edit_form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/season/delete/" + id,
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


    $(".arr-down").on('click', function () {
        let id = $('.form').attr("data-value");
        let elem = $("." + id);

        let next = elem.not('.vis').next();

        if (next.attr("id") !== "add_new_season_button") {
            let form = $(".form")
            form.addClass("contentChanged contentMoved");
            $(".save.button, .discard.button").removeAttr('disabled');
            elem.insertAfter(next);
        }
    });
    $(".arr-up").on('click', function () {
        let id = $('.form').attr("data-value");
        let elem = $("." + id);

        let prev = elem.not('.vis').prev();

        if (prev.attr("data-value") !== undefined) {
            let form = $(".form")
            form.addClass("contentChanged contentMoved");
            $(".save.button, .discard.button").removeAttr('disabled');
            elem.insertBefore(prev);
        }
    });


</script>