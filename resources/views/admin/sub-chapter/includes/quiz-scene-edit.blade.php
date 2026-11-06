<h1>Edit</h1>
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
    <form action="{{route('sub-chapter-update',[$scene->id,$season->id,$episode->id, $chapter->id])}}"
          class="ui form quiz"
          id="scene_edit_form" method="post" data-value="{{$scene->id}}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tags" value="" id="intro_scene_tags">
        <input type="hidden" name="quiz" value="1">
        <input type="hidden" name="array_of_learnings" type="text" value="" id="array_of_learnings"/>
        <input type="hidden" name="array_of_other" type="text" value="0" id="array_of_other"/>

        <div class="two fields">
            <div class="ui field">
                <div class="field">
                    <label>Tags</label>
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
                <div class="field">
                    <label>Link dialogues</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="dialogue_id"
                               @if(isset($selectedDialogue)) value="{{$selectedDialogue->id}}" @endif>
                        @if(isset($selectedDialogue))
                            <div class="text">
                                {{$selectedDialogue->name_en}}
                            </div>
                        @else
                            <div class="default text">Dialogues</div>
                        @endif
                        <div class="menu">
                            @foreach($dialogues as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        {{--                        <i class="chevron down icon"></i>--}}
                        <i class="dropdown icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Link short dialogues</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="short_dialogue_id"
                               @if(isset($selectedShortDialogue)) value="{{$selectedShortDialogue->id}}" @endif>
                        @if(isset($selectedShortDialogue))
                            <div class="text">
                                {{$selectedShortDialogue->name_en}}
                            </div>
                        @else
                            <div class="default text">Short Dialogues</div>
                        @endif
                        <div class="menu">
                            @foreach($dialogues as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        {{--                        <i class="chevron down icon"></i>--}}
                        <i class="dropdown icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Link Image</label>
                    <div class="ui selection search dropdown qiz">
                        <input type="hidden" name="image_id" value="{{$scene->quiz_image}}">

                        <div class="default text">Image</div>
                        <div class="menu">
                            @foreach($quizImg as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name}}</div>
                            @endforeach
                        </div>
                        {{--                        <i class="chevron down icon"></i>--}}
                        <i class="dropdown icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Link learnings</label>
                    <div class="ui fluid multiple search selection dropdown learnings">
                        <i class="dropdown icon"></i>
                        @if(isset($scene->learnings))
                            @foreach($scene->learnings as $item)
                                @php
                                    $learning = $learnings->find($item);
                                    if (isset($learning)){
                                        $name = $learning->name;
                                    }
                                @endphp
                                <a class="ui label transition visible" data-value="{{$item}}">
                                    {{$name}}<i class="delete icon"></i></a>
                            @endforeach
                        @endif
                        <div class="default text">Learnings</div>
                        <div class="menu">
                            @foreach($learnings as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name}}</div>
                            @endforeach
                            @if(isset($scene->learnings))
                                @foreach($scene->learnings as $item)
                                    @php
                                        $learning = $learnings->find($item);
                                        if (isset($learning)){
                                            $name = $learning->name;
                                        }
                                    @endphp
                                    <div class="item active filtered" data-value="{{$item}}">{{$name}}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label>Link from other</label>
                    <div class="ui fluid multiple search selection dropdown other">
                        <i class="dropdown icon"></i>
                        @if(isset($scene->other))
                            @foreach($scene->other as $item)
                                @php
                                    $otherArray = [
                                        1 => "welcome",
                                        2 => "introduction",
                                        3 => "motivation",];
                                        $other = $otherArray[$item];
                                @endphp
                                <a class="ui label transition visible" data-value="{{$item}}">
                                    {{$other}}<i class="delete icon"></i></a>
                            @endforeach
                        @endif
                        <div class="default text">Select from: introduction / welcome / motivation</div>
                        <div class="menu">
                            @if(isset($scene->other))
                                <div class="item @if(in_array(1,$scene->other))active filtered @endif" data-value="1">welcome</div>
                                <div class="item @if(in_array(2,$scene->other))active filtered @endif" data-value="2">introduction</div>
                                <div class="item @if(in_array(3,$scene->other))active filtered @endif" data-value="3">motivation</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" id="scene_save_button">SAVE</button>
            <button class="grey ui button" type="button" id="scene_discard" disabled> DISCARD</button>

            @if(!Auth::user()->hasRole('content'))
                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="scene_delete_button"> DELETE PAGE
                </button>
            @endif
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $("body").on('DOMSubtreeModified', "#tags", function () {
        $('.form').addClass('contentChanged')
    });
    // prevent submit form on Enter keypress
    document.querySelector('form.quiz').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
    $(document).ready(function () {
        $('.search.dropdown').dropdown({
            clearable: true
        })
    })

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
    $('.discard.button').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    location.reload()
                }
            }).modal('show');
        } else {
            location.reload()
        }
    })
    $('.cancel-words').on('click', function () {
        if ($('.form').hasClass('contentChanged') && $('.search.dropdown >a').length > 0) {
            $('.discard-modal').modal({
                onApprove: function () {
                    location.reload();
                }
            }).modal('show')
        } else {
            location.reload()
        }
    })
    $('#scene_delete_button:not(.lena)').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                let id = $('#scene_edit_form').attr('data-value');
                $.ajax({
                    type: "post",
                    url: "/admin/sub-chapter/delete/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    }
                })
            }
        }).modal('show');
    });

    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }

    $("#scene_save_button").one('click', function () {
        let arr = [];
        $('#tags span').each(function () {
            arr.push($(this).text());
        });
        let array = [];
        $('.search.dropdown.learnings > a').each(function () {
            array.push($(this).attr('data-value'))
        })
        let otherArr = [];
        $('.search.dropdown.other > a').each(function () {
            otherArr.push($(this).attr('data-value'))
        })

        $('#intro_scene_tags').val(JSON.stringify(arr));
        $('#array_of_learnings').val(JSON.stringify(array));
        $('#array_of_other').val(JSON.stringify(otherArr));
        $('#scene_create_form').submit();
    })
</script>