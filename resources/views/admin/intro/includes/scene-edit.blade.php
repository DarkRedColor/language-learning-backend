<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    {{--    <div class="arrows">--}}
    {{--        <div class="big ui left attached icon button disabled">--}}
    {{--            <i class="arrow down icon"></i>--}}
    {{--        </div>--}}
    {{--        <div class="big ui right attached icon button disabled">--}}
    {{--            <i class="arrow up icon"></i>--}}
    {{--        </div>--}}
    {{--        <div class="info">--}}
    {{--            <p class="position-list">Position list {{$scene->order}}</p>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="ui selection dropdown" style="background-color: var(--scene)">--}}
    {{--        <label>TYPE: </label>--}}
    {{--        <input type="hidden" name="scene">--}}
    {{--        <div class="text">Scene</div>--}}
    {{--    </div>--}}
</div>
<div class="edit-area adventure">
    <form action="{{route('scene-update',$scene->id)}}" class="ui form " id="intro_scene_edit_form"
          method="post">
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
                            @if($scene->image_unlocked <> null)
                                <img class="previewImg" src="{{$scene->image_unlocked}}_S"/>
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
        <div class="buttons action-buttons">

            <button type="button" class="positive ui button save" id="scene_update_button"> SAVE</button>

            {{--            <button class="grey ui button discard" type="button" id="scene_discard" disabled> DISCARD</button>--}}

            <a href="#" class="negative ui button del" id="scene_delete_button" name="{{$scene->id}}"> DELETE PAGE </a>

        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/semantic.min.js")}}></script>
<script type="application/javascript" src={{asset("js/main.js")}}></script>

<script>

    $("body").on('DOMSubtreeModified', "#tags", function () {
        $('.form').addClass('contentChanged')
    });
    // tags
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

    $("#scene_update_button").on('click', function () {
        let arr = [];
        $('#tags span').each(function () {
            arr.push($(this).text());
        });
        $('#intro_scene_tags').val(JSON.stringify(arr));
        $('#intro_scene_edit_form').submit();
    })

    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }


</script>