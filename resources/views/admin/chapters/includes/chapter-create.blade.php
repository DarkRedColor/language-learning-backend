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
    <div class="ui selection dropdown" style="background: var(--chapter);">
        <label>TYPE:</label>
        <input type="hidden" name="type" id="chapter_select_field" value="chapter">
        <div class="text">Chapter</div>
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
        <input type="text" hidden name="type" id="chapter_select_type_value" value="0">
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
                <div class="field char_emotion">
                    <label>Character Emotions</label>
                    <div class="ui selection search dropdown char_emotion">
                        <input type="hidden" name="emotion_id" value="" id="emotion_id">
                        <i class="chevron down icon"></i>
                        <div class="default text">Character and Emotions</div>
                        <div class="menu">
                            @foreach($personages as $item)
                                @foreach($item->emotions as $emotion)
                                    <div class="item" data-value="{{$emotion->id}}" id="personage">
                                        <span>{{$item->name_en}}</span> {{$emotion->name}}</div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="field char_emotion">
                    <label>Introductions</label>
                    <div class="ui selection search dropdown char_emotion">
                        <input type="hidden" name="introduction_id" value="" id="introduction_id">
                        <i class="chevron down icon"></i>
                        <div class="default text">Introductions</div>
                        <div class="menu">
                            @foreach($introductions as $item)
                                <div class="item" data-value="{{$item->id}}" id="introduction">
                                    {{$item->key}}</div>
                            @endforeach
                        </div>
                    </div>
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


    $(document).ready(function () {
        var hasName = $('#admin_seasons_list > div');
        if (hasName.hasClass('intro')) {
            $('.option-area .menu .item').each(function () {
                if ($(this).text() === 'Intro') {
                    $(this).hide();
                }
            })
        }
        if (hasName.hasClass('quiz')) {
            $('.option-area .menu .item').each(function () {
                if ($(this).text() === 'Quiz') {
                    $(this).hide();
                }
            })
        }
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

    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }

</script>