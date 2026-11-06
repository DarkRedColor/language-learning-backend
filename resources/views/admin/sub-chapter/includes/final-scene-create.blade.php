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
    <div class="ui selection dropdown" style="background: var(--scene);">
        <label>TYPE:</label>
        <input type="hidden" name="type" value="Scene">
        <div class="text">Scene</div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="{{route('sub-chapter-store',[$season_id,$episode_id, $chapter_id])}}" class="ui form quiz "
          id="scene_create_form" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tags" value="" id="intro_scene_tags">
        <input type="hidden" name="quiz" value="3">

        {{--        --------------------------------------------------------------------------------------------}}
        {{--        Inputu timer tre sa fie cu name timer, in bd se salveaza ca string asa ca formatu nu are importanta--}}



        {{--       ---------------------------------------------------------------------------------------------- --}}

        <input type="hidden" name="name_en" value="Final Quiz">
        <input type="hidden" name="array_of_learnings" type="text" value="" id="array_of_learnings"/>

        <div class="two fields">
            <div class="ui field">
                <div class="field">
                    <label>Tags</label>
                    <div id="tags">
                        <input type="text" placeholder="Add a tag"/>
                        <input class="hidden"/>
                    </div>
                </div>
                <div class="field">
                    <label>Link dialogues</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="dialogue_id">
                        <div class="default text">Dialogues</div>
                        <div class="menu">
                            @foreach($dialogues as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Link short dialogues</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="short_dialogue_id">
                        <div class="default text">Short Dialogues</div>
                        <div class="menu">
                            @foreach($dialogues as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name_en}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Link Image</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="image_id">
                        <div class="default text">Image</div>
                        <div class="menu">
                            @foreach($quizImg as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="field">
                    <label>Link learning</label>
                    <div class="ui fluid multiple search selection dropdown learnings">
                        <i class="dropdown icon"></i>
                        @if(isset($scene->learnings))
                            @foreach($scene->learnings as $item)
                                <a class="ui label transition visible" data-value="{{$item->id}}">
                                    {{$item->name}}<i class="delete icon"></i></a>
                            @endforeach
                        @endif
                        <div class="default text">Learnings</div>
                        <div class="menu">
                            @foreach($learnings as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->name}}</div>
                            @endforeach
                            @if(isset($scene->learnings))
                                @foreach($scene->learnings as $item)
                                    @php $learning = $learnings->find($item); if (isset($learning)){ $name = $learning->name; }
                                    @endphp
                                    <div class="item active filtered" data-value="{{$item->id}}">{{$name}}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="ui field">
                <div class="field">
                    <label>Games count</label>
                    <input placeholder="games count number" type="number" min="0" max="100" name="count">
                </div>
                <div class="field timer">
                    <label>Timer</label>
                    <input id="timer-in-sec" type="number" name="timer" hidden>
                    <div class="ui input">
                        <input type="number" placeholder="min" id="timer-minutes">
                        <span>:</span>
                        <input type="number" placeholder="sec" id="timer-seconds">
                    </div>
                </div>
            </div>
        </div>

        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" id="scene_save_button">SAVE</button>
            <a href="#" class="negative ui button discard" id="scene_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    {{--  timer  --}}
    $(document).ready(function(){
        // ======= validation =========
        $('#timer-minutes, #timer-seconds').on('input', function(){
            var minutes = $('#timer-minutes').val();
            var seconds = $('#timer-seconds').val();
            var isEmpty = minutes === '' || seconds === '';

            if (!isEmpty) {
                if(minutes < 0 || minutes > 59 || isNaN(minutes) ||
                    seconds < 0 || seconds > 59 || isNaN(seconds)){
                    $(this).val($(this).data('last-valid') || '');
                } else {
                    $(this).data('last-valid', $(this).val());
                }
            }

            var invalidInput = isEmpty ||
                minutes < 0 || minutes > 59 || isNaN(minutes) ||
                seconds < 0 || seconds > 59 || isNaN(seconds);
            $('#scene_save_button').prop('disabled', invalidInput);

            var saveTo = $('#timer-in-sec');
            var secondsInMin = parseInt(minutes) * 60 ;
            var totalSeconds = secondsInMin + parseInt(seconds);
            saveTo.val(totalSeconds);
        });
    });
    {{--  timer  --}}

    $("body").on('DOMSubtreeModified', "#tags", function () {
        $('.form').addClass('contentChanged')
    });

    // prevent submit form on Enter keypress
    document.querySelector('form.quiz').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

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
        if ($('.form').hasClass('contentChanged') && $('.search.dropdown >a').length > 0){
            $('.discard-modal').modal({
                onApprove:function () {
                    location.reload();
                }
            }).modal('show')
        }else{
            location.reload()
        }
    })

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
        let otherArr = [];
        $('.search.dropdown.other > a').each(function () {
            otherArr.push($(this).attr('data-value'))
        })
        let array = [];
        $('.search.dropdown.learnings >a').each(function () {
            array.push($(this).attr('data-value'))
        })
        $('#intro_scene_tags').val(JSON.stringify(arr));
        $('#array_of_learnings').val(JSON.stringify(array));
        $('#array_of_other').val(JSON.stringify(otherArr));
        $('#scene_create_form').submit();
    })
</script>