<h1>Edit Intro</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area" style="background: var(--intro);">
    <h3>
        Intro
    </h3>
</div>
<div class="edit-area">
    <form action="{{route('arcade-page-store',$page->id)}}" class="ui form" id="arcade_game_create_form"
          method="post" data-value="{{$page->id}}">
        @csrf
        <input type="hidden" name="content_type" value="intro" id="content_type">
        <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
        <div class="two fields">
            <div class="field eight wide">
                <div class="field">
                    <label>Character Emotion:</label>
                    <div class="ui selection search dropdown char_emotion">
                        <input type="hidden" name="emotion_id" value="{{$page->emotion_id}}" id="emotion_id">
                        @if(isset($page->pesonage_id))
                            <div class="text">
                                <span>{{$personages->where('id',$page->pesonage_id)->first()->name_en}}</span>{{$personages->where('id',$page->pesonage_id)->emotions->where('id',$page->emotion_id)->first()->name}}
                            </div>
                        @else
                            <div class="default text">Character and Emotions</div>
                        @endif
                        {{--                        <i class="dropdown icon"></i>--}}
                        <div class="menu">
                            @foreach($personages as $item)
                                @foreach($item->emotions as $emotion)
                                    <div class="item" data-value="{{$emotion->id}}" id="personage">
                                        <span>{{$item->name_en}}</span>{{$emotion->name}}</div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label>Position:</label>
                    <div class="ui selection search dropdown">
                        @if($page->position == 1)
                            <input type="hidden" name="position" value="right">
                            <div class=" text">Right</div>
                        @else
                            <input type="hidden" name="position" value="left">
                            <div class=" text">Left</div>
                        @endif
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item" data-value="left" id="pos_left">
                                left
                            </div>
                            <div class="item" data-value="right" id="pos_right">
                                right
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field eight wide">
                <div class="ui field accordion">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Localisations:</label>
                            <textarea class="textarea" rows="2" name="text_en">{{$page->text_en}}</textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($langs as $item)
                            @if($item['lcid'] <> "en")
                                <div class="field">
                                    @php
                                        $textIso ="text_" . $item['lcid']
                                    @endphp
                                    <textarea rows="2"
                                              name="text_{{$item['lcid']}}">{{$page->$textIso}}</textarea>
                                    <div class="ui label lang-iso">{{$item['lcid']}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" id="arcade_game_save_button">SAVE</button>
            <button type="button" class="grey ui button" id="discard">discard</button>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready(function () {
        $('.ui.dropdown').dropdown({
            clearable: true
        });
        $('#dialogue_sub_id').val($('#dialogue_sub_chapter_id').val())
        $('#dialogue_parent_id').val($('#dialogue_index_parent_id').val())
        $('.button.save, #discard').attr('disabled', true)

        $('#discard').on('click', function () {
            let editAria = $('#arcade_storage_edit_create_zone')
            $('.discard-modal').modal({
                onApprove: function () {
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
                    $('.button.save, #discard').attr('disabled', true);
                    let type = $('#content_type').val()
                    $.ajax({
                        type: "get",
                        url: "/admin/arcade-games/page/create-and-edit/" + type + "/{{$page->id}}",
                        contentType: 'application/json',
                        async: true,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            editAria.empty();
                            editAria.append(res);
                        }
                    });
                }
            }).modal('show');
        })
    })
    $('.form input, textarea').not('.hidden').on('keyup change', function () {
        console.log(this)
        $('.form').addClass('contentChanged')
        $('.button.save, #discard').attr('disabled', false)
    })
    // ====== change form state
    $('.dropdown').on('change', () => {
        $('.form').addClass('contentChanged')
        $('.save.button, #discard').attr('disabled', false)
    })
</script>