<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="option-area">
        <div class="arrows">
            <div class="ui left attached icon button arr-down vis">
                <i class="arrow down icon"></i>
            </div>
            <div class="ui right attached icon button arr-up vis">
                <i class="arrow up icon"></i>
            </div>
            <div class="info">
                <p>
                    Position {{$page->order + 1}}

                </p>
            </div>
        </div>

        <div class="ui selection dropdown" style="background: var(--choice);">
            <input type="hidden" name="type" value="choice" id="dialogue_type_select">
            <div class="text">Choice</div>
            {{--            <div class="menu">--}}
            {{--                <div class="item" data-value="line">Line</div>--}}
            {{--                <div class="item" data-value="action">Action</div>--}}
            {{--                <div class="item" data-value="notification">Notification</div>--}}
            {{--                <div class="item" data-value="choice">Choice</div>--}}
            {{--            </div>--}}
            {{--            <i class="chevron down icon"></i>--}}
        </div>
    </div>

    <div class="edit-area">
        <form action="{{route('dialogue-page-store',$page->id)}}" class="ui form" method="POST" id="dialogue_edit_form"
              data-value="{{$page->id}}">
            @csrf
            <input type="hidden" name="tree" value="" id="create_tree">
            <input type="hidden" name="content_type" value="choice" id="content_type">
            <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
            <input type="hidden" name="parent_id" value="" id="dialogue_parent_id">
            <input type="hidden" name="variants">
            <input type="hidden" name="emotion_id">
            <input type="hidden" name="position">
            <input type="hidden" name="background_id">
            <div class="two fields">
                <div class="field options">
                    <div class="field">

                    </div>
                </div>
                <div class="ui field accordion">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Localization</label>
                            <textarea rows="2" name="text_en">{{$page->text_en}}</textarea>

                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">

                        @foreach($langs as $item)
                            @if($item['lcid'] <> "en")
                                @php
                                    $textIso ="text_" . $item['lcid']
                                @endphp
                                <div class="field">
                                    <textarea rows="2"
                                              name="text_{{$item['lcid']}}">{{$page->$textIso}}</textarea>
                                    <div class="ui label lang-iso">{{$item['lcid']}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" id="save">SAVE</button>
                <button type="button" class="discard grey ui button" id="discard">DISCARD</button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready(function () {
        $('#dialogue_sub_id').val($('#dialogue_sub_chapter_id').val())
        $('#dialogue_parent_id').val($('#dialogue_index_parent_id').val())
        $('.button.save, .button.discard').attr('disabled', true)

        $('.button.discard').on('click', function () {
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
                    $('.button.save, .button.discard').attr('disabled', true);
                    let type = $('#dialogue_type_select').val()
                    $.ajax({
                        type: "get",
                        url: "/admin/dialogue/page/create-and-edit/" + type + "/{{$page->id}}",
                        contentType: 'application/json',
                        async: true,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let editAria = $('#edit')
                            editAria.empty();
                            editAria.append(res);
                        }
                    });
                }
            }).modal('show');
        })
    })
    // add class && show button
    $('.form input, textarea').not('.hidden').on('keyup change', function () {
        console.log(this)
        $('.form').addClass('contentChanged')
        $('.button.save, .button.discard').attr('disabled', false)
    })
    // discard dropdown
    // $('input[name="position"]').on('change', function () {
    //     $(this).next().text($(this).attr('value'));
    // })

    // element id & position
    function getId() {
        let positionId = [];
        $('.buttons.stories .adv').each(function () {
            let id = $(this).attr('data-value')
            positionId.push(id)
        })
        $('#create_tree').val(JSON.stringify(positionId))
    }


    // move position by arrows
    $(".arr-down").on('click', function () {

        let id = $('.form').attr("data-value");
        let elem = $("." + id);

        let next = elem.next();

        if (next.attr("id").length > 0) {
            let form = $(".form")
            form.addClass("contentMoved");
            $(".save.button, .discard.button").attr('disabled', false);
            elem.insertAfter(next);
        }
        getId();
    });
    $(".arr-up").on('click', function () {

        let id = $('.form').attr("data-value");
        let elem = $("." + id);

        let prev = elem.prev();

        if (prev.attr("id").length > 0) {
            let form = $(".form")
            form.addClass("contentMoved");
            $(".save.button, .discard.button").attr('disabled', false);
            elem.insertBefore(prev);
        }
        getId();
    });

    $('.button.delete').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/dialogue/page/delete/{{$page->id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    }
                });
            }
        }).modal('show');
    })
</script>