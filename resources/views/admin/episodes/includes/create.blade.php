<h1>Create</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div class="big ui left attached icon button">
            <i class="arrow down icon"></i>
        </div>
        <div class="big ui right attached icon button">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p class="position-list">Position list "the last one"</p>
        </div>
    </div>
    <div class="ui selection dropdown" style="background: var(--episode);">
        <label>TYPE:</label>
        <input type="hidden" name="type" id="episode_type" value="Episode">
        <div class="text">Episode</div>
        <div class="menu">
            <div class="item" data-value="episode">Episode</div>
            <div class="item" data-value="final_quiz">Final Quiz</div>
        </div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="/admin/episode/store/{{$season_id}}" class="ui form" id="episode_create_form" method="post" enctype="multipart/form-data">
        @csrf
        <input hidden value="0" name="type">
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
                            <img class="previewImg" data-value="" src="">
                        </i>
                        <a href="#" class="dis-img" onclick="delLanguageFlag()"><i class="close icon"></i></a>
                    </label>

                </div>
                <div class="field additional-img">

                </div>
            </div>
            <div class="two fields">
                <div class="ui field accordion">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Summary</label>
                            <textarea class="textarea" rows="2" name="description_en"
                                      id="description_en_field_of_episode_create_form"></textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                <div class="field">
                                    <textarea class="textarea" rows="2" name="description_{{$item->lcid}}"></textarea>
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="field char_bg">
                        <label>Background episode</label>

                        <div class="ui selection search dropdown">
                            <input type="hidden" name="background_episode_id" value="" id="background_episode_id">
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
                            <textarea class="textarea" rows="2" name="name_en"
                                      id="name_en_field_of_episode_create_form"></textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                <div class="field">
                                    <textarea class="textarea" rows="2" name="name_{{$item->lcid}}"></textarea>
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="field char_bg">
                        <label>Background</label>

                        <div class="ui selection search dropdown">
                            <input type="hidden" name="background_id" value="" id="background_id">
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
            <button type="button" class="positive ui button save" disabled id="episode_save_button">SAVE</button>
            <a href="#" class="negative ui button discard" id="episode_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
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

    $("#episode_save_button").one('click', function () {
        $('.form').submit();
    })

    $('.button.discard').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $(".discard_modal").modal({
                onApprove: function () {
                    $('.edit').empty();
                }
            }).modal('show');
        } else {
            $('.edit').empty();
        }
    })

    $('#episode_type').on('change', function () {
        let editAria = $('#edit')
        editAria.empty();
        $.ajax({
            type: "get",
            url: "/admin/episode/final-quiz/create/" + "{{$season_id}}" ,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                editAria.append(res);
            }
        });
    })
</script>