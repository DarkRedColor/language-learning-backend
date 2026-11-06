<h1> Intro Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
{{--    <div class="arrows">--}}
{{--        <div id="chapter_arrow_down" class="big ui left attached icon button">--}}
{{--            <i class="arrow down icon"></i>--}}
{{--        </div>--}}
{{--        <div id="chapter_arrow_up" class="big ui right attached icon button">--}}
{{--            <i class="arrow up icon"></i>--}}
{{--        </div>--}}
{{--        <div class="info">--}}
{{--            <p class="position-list">Position list {{$chapter->order + 1}}</p>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="ui selection dropdown" style="background: var(--chapter);">--}}
{{--        <label>TYPE:</label>--}}
{{--        <input type="hidden" name="type" value="Chapter">--}}
{{--        <div class="text">Chapter</div>--}}
{{--    </div>--}}
</div>
<div class="edit-area adventure">
    <form action="{{route('intro-c-update')}}" class="ui form discard" id="chapter_edit_form" data-value="{{$intro->id}}" method="post">
        @csrf
        <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
        <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
{{--        <input class="array-list" type="hidden" name="tree" value="" id="chapter_list">--}}
        <div class="two fields">
            <div class="ui field">
                <div class="inline field file">
                    <label id="dropped">
                        <input type="file" name="image" accept="image/png" onchange="previewFile(this);" placeholder="IMG">
                        <i class="upload icon">
                            @if($intro->image_unlocked <> null)
                                <img class="previewImg" src="{{$intro->image_unlocked}}_S"/>
                            @else
                                <img class="previewImg" src=""/>
                            @endif
                        </i>
                        <a href="#" class="dis-img" onclick="delLanguageFlag()" id="chapter_delete_image_edit_form_button"><i
                                    class="close icon"></i></a>
                    </label>
                    {{--                    <a href="#" class="dis-img" onclick="delLanguageFlag()" ><i class="close icon"></i></a>--}}
                </div>
            </div>
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localization</label>
                        <textarea class="textarea" rows="2" name="name_en" data-value="{{$intro->name_en}}" id="name_en_field_of_chapter_edit_form">{{$intro->name_en}}</textarea>
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
                                <textarea class="textarea"  data-value="{{$intro->$localisationName}}" rows="2" name="name_{{$item->lcid}}">{{$intro->$localisationName}}</textarea>
                                <div class="ui label lang-iso">{{$item->lcid}}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="buttons action-buttons">

            <button type="button" class="positive ui button save" id="chapter_update_button" disabled> SAVE</button>

            <button class="grey ui button discard" type="button" id="chapter_discard" disabled> DISCARD</button>


        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script type="application/javascript" src={{asset("js/adventures_order_list.js")}}></script>

<script>

    $('#chapter_cancel_button').on('click',function (){
        if ($('#chapter_create_form').hasClass('contentChanged')){
            $(".discard-modal").modal({
                onApprove:function (){
                    $('#season_edit_create_zone').empty();
                }
            }).modal('show');
        } else {
            $('#season_edit_create_zone').empty();
        }
    })

    $("#chapter_discard").on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                let input = $('.textarea')
                input.each(function () {
                    $(this).val($(this).attr('data-value'))
                })
                let form = $('#chapter_edit_form')
                if (form.hasClass("contentMoved")) {
                    let id = form.attr('data-value');
                    let elem = $("." + id);
                    let position = elem.attr('id');
                    let nextOrPrevElement = $("#" + (parseInt(position) + 1))
                    if (nextOrPrevElement.length === 0){
                        nextOrPrevElement =  $("#" + (parseInt(position) -1))
                        elem.insertAfter(nextOrPrevElement)
                    } else {
                        elem.insertBefore(nextOrPrevElement)
                    }
                    form.removeClass("contentMoved");
                }
                form.removeClass('contentChanged');
                $('#chapter_edit_form').removeClass('contentChanged');
                $('#chapter_update_button').attr('disabled', true);
                $('#chapter_discard').attr('disabled', true);
            }
        }).modal('show');
    })

    $('#name_en_field_of_chapter_edit_form').on('keyup', function () {
        if ($('#name_en_field_of_chapter_edit_form').val() !== '') {
            $('#chapter_update_button').removeAttr('disabled');
        } else {
            $('#chapter_update_button').attr('disabled', true);
        }
    })


    $("#chapter_delete_image_edit_form_button").on('click', function () {
        $('#chapter_update_button').removeAttr('disabled');
        $('#chapter_edit_form').addClass('contentChanged');

    })
    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }
</script>