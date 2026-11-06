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
    <div class="ui selection dropdown" style="background: var(--intro);">
        <label>TYPE:</label>
        <input type="hidden" name="type" id="chapter_select_field" value="intro">
        <div class="text">Intro</div>
        <div class="menu">
            <div class="item" data-value="chapter">Chapter</div>
            <div class="item" data-value="intro">Intro</div>
            <div class="item" data-value="quiz">Quiz</div>
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>
<div class="edit-area adventure">
    <form action="{{route('chapter-store',[$season_id,$episode_id])}}" class="ui form" id="chapter_create_form"
          method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <input type="text" hidden name="type" id="chapter_select_type_value" value="2">
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
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localization</label>
                        <textarea class="textarea" rows="2" name="name_en"
                                  id="name_en_field_of_chapter_create_form"></textarea>
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
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="chapter_save_button">SAVE</button>
            <a href="#" class="negative ui button discard" id="chapter_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $("body").on('DOMSubtreeModified', "#tags", function () {
        $('.form').addClass('contentChanged')
    });

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

    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }

</script>