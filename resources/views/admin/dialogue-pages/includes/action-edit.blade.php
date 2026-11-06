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

        <div class="ui selection dropdown" style="background: var(--action);">
            <input type="hidden" name="type" value="action" id="dialogue_type_select">
            <div class="text">Action</div>
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

        <form action="{{route('dialogue-page-store',$page->id)}}" class="ui form" method="POST"
              id="dialogue_edit_form" data-value="{{$page->id}}">
            @csrf
            <input type="hidden" name="tree" value="" id="create_tree">
            <input type="hidden" name="content_type" value="action" id="content_type">
            <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
            <input type="hidden" name="parent_id" value="" id="dialogue_parent_id">
            <div class="two fields">
                <div class="field options">
                    <div class="field">
                        <label>Character Emotions</label>
                        <div class="ui selection search dropdown">
                            <input type="hidden" name="emotion_id" value="{{$page->emotion_id}}" id="emotion_id">
                            @if(isset($page->pesonage_id))
                                <div class="text">
                                    <span>{{$personages->where('id',$page->pesonage_id)->first()->name_en}}</span>{{$personages->where('id',$page->pesonage_id)->emotions->where('id',$page->emotion_id)->first()->name}}
                                </div>
                            @else
                                <div class="default text">Character and Emotions</div>
                            @endif
                            <div class="menu">
                                @foreach($personages as $item)
                                    @foreach($item->emotions as $emotion)
                                        <div class="item" data-value="{{$emotion->id}}" id="personage">
                                            <span>{{$item->name_en}}</span>{{$emotion->name}}</div>
                                    @endforeach
                                @endforeach
                            </div>
                            <i class="chevron down icon"></i>
                        </div>
                    </div>

                    <div class="field">
                        <label>Position</label>

                        <div class="ui selection dropdown">
                            @if($page->position == 1)
                                <input type="hidden" name="position" value="right">
                                <div class=" text">Right</div>
                            @else
                                <input type="hidden" name="position" value="left">
                                <div class=" text">Left</div>
                            @endif
                            <div class="menu">
                                <div class="item default" data-value="left">Left</div>
                                <div class="item" data-value="right">Right</div>
                            </div>
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <label>Background</label>
                        <div class="ui selection search dropdown">
                            <input type="hidden" name="background_id" value="{{$page->background_id}}">
                            @if(isset($page->background_id) && $bg->where('id',$page->background_id)->first() !== null )
                                <div class="text">{{$bg->where('id',$page->background_id)->first()->name}}</div>
                            @else
                                <div class="default text">Background</div>
                            @endif
                            <div class="menu">
                                @foreach($bg as $item)
                                    <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                                @endforeach
                            </div>
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                    <div class="field ">
                        <label>Animation</label>
                        <div class="ui search selection dropdown animation">
                            <input type="hidden" name="animation_id" value="{{$page->animation_id}}">
                            <div class="default text">Animation pack and name</div>
                            <div class="menu">
                                @foreach($animations as $item)
                                    @foreach($item->animation as $emotion)
                                        <div class="item" data-value="{{$emotion->id}}" id="animation_id">
                                            <span>{{$item->name}}</span>{{$emotion->name}}
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                            <i class="dropdown icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <p class="filling-type">Hide Name</p>
                        <div class="ui buttons thumb">
                            <button type="button" class="ui button manual">Show</button>
                            <input type="hidden" id="tumbler" value="{{$page->hide_name}}" name="hide_name">
                            <div class="or"></div>
                            <button type="button" class="ui button selective">Hide</button>
                        </div>
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
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" id="save">SAVE</button>
                <button type="button" class="discard grey ui button" id="discard">DISCARD</button>
                <button class="negative ui button delete @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" type="button">DELETE PAGE
                </button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}}></script>
<script>
    var selectiveBtn = $('.selective'),
        manualBtn = $('.manual'),
        pair = $('.loc-thumb');
    $(document).ready(function () {
        @if($page->hide_name == 0)
        $('.button.manual').addClass('blue')
        @else
        $('.button.selective').addClass('blue')
        @endif
        pair.find('button:first-child').addClass('blue').after(function () {
            pair.hide();
        })
        // --------------- animation dropdown
        $('.dropdown.animation').dropdown({
            clearable: true
        })
        // --------------- animation dropdown
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
    manualBtn.click(function () {
        switchButton(manualBtn);
        $('#tumbler').val(0)
    });
    selectiveBtn.click(function () {
        switchButton(selectiveBtn);
        $('#tumbler').val(1)
    });
    function switchButton(newButton) {

        newButton.siblings().removeClass('blue');
        newButton.addClass('blue');
        currentButton = newButton;
        pair.toggle();
        // selective.toggle();
        // manual.toggle();
        // rulesThumb.toggle();
    }
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

    $('.button.delete:not(.lena)').on('click', function () {
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
