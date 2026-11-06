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
    <div class="ui selection dropdown" style="background: var(--season);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Season">
        <div class="text">Season</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="{{route('season-store')}}" class="ui form" id="season_create_form" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <div class="two fields">
            <div class="ui field">
                <div class="inline field file">
                    <label id="dropped">
                        <input type="file" name="image" accept="image/png" onchange="previewFile(this);" placeholder="IMG">
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
                        <textarea class="textarea" rows="2" name="name_en" id="name_en_field_of_season_create_form"></textarea>
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
            <button type="submit" class="positive ui button save" disabled id="season_save_button">SAVE</button>
            <a href="#" class="negative ui button discard" id="season_cancel_button">CANCEL</a>
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

    // $('#season_edit_create_zone').not('click', function (){
    //     if ($('#season_create_form').hasClass('contentChanged')){
    //         $("#discard_modal").modal({
    //             onApprove:function (){
    //                 $('#season_edit_create_zone').empty();
    //             }
    //         }).modal('show');
    //     } else {
    //         $('#season_edit_create_zone').empty();
    //     }
    // })
    $('.button.discard').on('click',function (){
        if ($('.form').hasClass('contentChanged')){
            $(".discard_modal").modal({
                onApprove:function (){
                    $('.edit').empty();
                }
            }).modal('show');
        } else {
            $('.edit').empty();
        }
    })

</script>