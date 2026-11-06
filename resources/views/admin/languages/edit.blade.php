<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="info">
        <p class="name">Name: </p>
        <p class="lang-name">{{$language->display_name}}</p>
    </div>
    <div class="lang-state">
        @if($language->is_displayed_to_user)
            <p class="lang-check unlock">Language is unlocked</p>  <!--  change color by class (lock || unlock)  -->
{{--            <button class="un-lock ui icon button unlockbutton" id="{{$language->id}}">--}}
{{--                <i class="unlock alternate icon"></i>--}}
{{--            </button>--}}
        @else
            <p class="lang-check lock">Language is locked</p>  <!--  change color by class (lock || unlock)  -->
{{--            <button class="lock ui icon button lockbutton" id="{{$language->id}}">--}}
{{--                <i class="lock alternate icon"></i>--}}
{{--            </button>--}}
        @endif
    </div>
</div>
<div class="edit-area flag">
    <form action="/admin/main/menu/language/update/{{$language->id}}" class="ui form" method="post"
          id="main_menu_language_edit" enctype="multipart/form-data">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
        <div class="two fields">
            <div class="field options">
                <div class="field">
                    <label>Name</label>
                    <input placeholder="Language name" name="name" type="text" value="{{$language->name}}">
                </div>
                <div class="field">
                    <label>Play</label>
                    <input placeholder="Type play language" name="learn" type="text" value="{{$language->learn}}">
                </div>
                <div class="field">
                    <label>My language</label>
                    <input placeholder="My language text" name="my_language" type="text"
                           value="{{$language->my_language}}">
                </div>
            </div>

            <div class="ui field">
                <!-- work from here -->
                <div class="inline field file" id="language_file">
                    <label for="file">Flag IMG: </label>
                    <label id="dropped">
                        <input type="file" name="flag" accept="image/png" onchange="previewFile(this);" placeholder="IMG"
                               id="language_flag_input"/>
                        <i class="upload icon">
                            @if($language->flag <> null)
                                <img class="previewImg" src="{{Storage::disk('gcs')->url("language/icon/$language->lcid".".png")}}"/>
                            @else
                                <img class="previewImg" src=""/>
                            @endif
                        </i>
                        @if($language->flag <> null)
                            <a href="#" class="dis-img" id="delete_flag_of_language" onclick="delLanguageFlag()"><i class="close icon"></i></a>
                        @endif
                    </label>

                </div>
            </div>
        </div>
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" id="save_language_button" disabled>SAVE</button>
            <button type="button" class="negative ui button" id="cancel_editing_button" disabled>CANCEL</button>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>

<script>
    $(document).ready(function (){
        if($('.option-area .lang-name').text().indexOf('Arabic') >= 0){
            $('form input').each(function(){
                $(this).attr('dir', 'rtl')
            })
        }
    })
    $("#cancel_editing_button").on('click', function () {
        if ($("#main_menu_language_edit").hasClass("contentChanged")) {
            $('.discard-modal').modal({
                onApprove: function () {
                    location.reload()
                }
            }).modal('show')
        } else {
            location.reload();
        }
    })

    $('#delete_flag_of_language').on('click', function () {
        $('#main_menu_language_edit').addClass('contentChanged');
        $('#save_language_button').removeAttr('disabled');
        $('#cancel_editing_button').removeAttr('disabled');
    })

    function delLanguageFlag() {
        $('.previewImg').attr('src', '/img_placeholder.svg')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }


</script>

