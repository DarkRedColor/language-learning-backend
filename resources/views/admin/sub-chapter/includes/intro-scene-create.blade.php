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
    <div class="ui selection dropdown" style="background: var(--scene);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Scene">
        <div class="text">Scene</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="{{route('sub-chapter-store',[$season_id,$episode_id, $chapter_id])}}" class="ui form"
          id="scene_create_form" method="post" enctype="multipart/form-data" >
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <input type="hidden" name="tags" value="" id="intro_scene_tags">
        <div class="two fields">
            <div class="ui field">
                <div class="inline field file">
                    <label id="dropped">
                        <input type="file" name="image" accept="image/png" onchange="previewFile(this);"
                               placeholder="IMG">
                        <i class="upload icon">
                            <img class="previewImg" src=""/>
                        </i>
                    </label>
                    {{--                    <a href="#" class="dis-img" onclick="delLanguageFlag()" ><i class="close icon"></i></a>--}}
                </div>
            </div>
            <div class="ui field">
                <div class="field">
                    <label>Tags</label>
                    <div id="tags">
                        <input type="text" placeholder="Add a tag"/>
                        <input class="hidden"/>
                    </div>
                </div>
                <div class="field">
                    <label>Dialogues</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="dialogue_id">
                        <div class="default text">Dialogues</div>
                        <div class="menu">
                            @foreach($dialogues as $item)
                            <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="short_dialogue_id">
                        <div class="default text">Short Dialogues</div>
                        <div class="menu">
                            @foreach($dialogues as $item)
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
                                      id="name_en_field_of_scene_create_form"></textarea>
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
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="scene_save_button">SAVE</button>
            <button type="button" class="negative ui button" id="scene_cancel_button">CANCEL</button>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $("body").on('DOMSubtreeModified', "#tags", function () {
        $('.form').addClass('contentChanged')
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

    $('#scene_cancel_button').on('click', function () {
        if ($('#scene_create_form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    $('#season_edit_create_zone').empty();
                }
            }).modal('show');
        } else {
            $('#season_edit_create_zone').empty();
        }
    })
    $("#scene_save_button").on('click', function () {
        let arr = [];
        $('#tags span').each(function () {
            arr.push($(this).text());
        });
        $('#intro_scene_tags').val(JSON.stringify(arr));
        $('#scene_create_form').submit();
    })

    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }

</script>