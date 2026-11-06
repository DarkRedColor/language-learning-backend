<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>{{$word->word}}</h3>
    </div>
    <div class="edit-area words">
        <form action="{{route('word-update',[$word->id])}}" class="ui form" method="post" id="word_create_form" enctype="multipart/form-data">
            @csrf
            <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
            <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
            <input name="example_list" value="" hidden/>
            <input name="update-audio-word" value="false" hidden/>
            <input name="update-audio-letter" value="false" hidden/>
            <div class="two fields">
                <div class="ui field">
                    <div class="ui field accordion">
                        <div class="title inputs">
                            <div class="field main-local">
                                <label>Localization</label>
                                <div class="ui label search oxford-search">
                                    <i class="search icon"></i>
                                </div>
                                <textarea class="textarea" name="word" rows="2">{{$word->word}}</textarea>
                                <div class="ui label lang-iso">EN
                                    <i class="chevron down icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="content field inputs">
                            @foreach($languages as $item)
                                @if($item->lcid <> 'en')
                                    @php
                                        $iso = 'meaning_' . $item->lcid;
                                    @endphp
                                    <div class="field">
                                            <textarea class="textarea" rows="2"
                                                      name="meaning_{{$item->lcid}}">{{$word->$iso}}</textarea>
                                        <div class="ui label lang-iso">{{$item->lcid}}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="ui field">
                    <div class="field">
                        <label>Spelling</label>
                        <div class="ui icon buttons spell games word task-audio">
                            <span class="ui icon button spelling word">
                                <p>By word</p>
                            </span>
                            <button type="button" class="ui button large spell-word">
                                <i class="cloud download icon large"></i>
                            </button>
                            <button type="button" class="ui button negative large del-file">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                        <div class="ui icon buttons spell games letter task-audio">
                            <span class="ui icon button spelling letter">
                                <p>By letter</p>
                            </span>
                            <button type="button" class="ui button large spell-letter">
                                <i class="cloud download icon large"></i>
                            </button>
                            <button type="button" class="ui button negative large del-file">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="ten wide field">
                        <label>Transcription</label>
                        <input placeholder="Transcription word" name="transcription" type="text"
                               value="{{$word->transcription}}">
                    </div>
                    <div class="ten wide field">
                        <div class="inline field file">
                            <label id="dropped" class="add-bg ">
                                <input type="file" name="image" accept="image/png"
                                       onchange="previewFile(this);$('#delete_flag_of_language').remove()"
                                       placeholder="IMG">
                                <i class="upload icon">
                                    @if(isset($word->image))
                                        <img class="previewImg" data-value="" src="{{Storage::disk('gcs')->url("word/$word->id" . "_S.webp")}}">
                                    @else
                                        <img class="previewImg" data-value="" src="">

                                    @endif
                                </i>
                                <a href="#" class="dis-img" onclick="delLanguageFlag()"><i
                                            class="close icon"></i></a>
                            </label>
                        </div>
                    </div>
                    <div class="field">
                        <label>Definitions</label>
                        <input placeholder="Word definition" name="definition_en" type="text"
                               value="{{$word->definition_en}}">
                    </div>
                    <div class="field">
                        <label>Examples</label>
                        <div class="editor words-editor">
                            <div id="toolbar-container"></div>
                            <div id="editor" class="words-editor">
                                @if(isset($word->example_list))
                                    @foreach($word->example_list as $item)
                                        {!! $item  !!}
                                    @endforeach
                                @endif
                            </div>
                            <p class="hint">*You didn't meet all interface requirements</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">

                <input type="hidden" name="theoryId" value="{{$theoryId ?? null}}">
                <button type="button" href="#" class="positive ui button save" id="word_save_button">SAVE</button>
                <button class="grey ui button discard" type="button">DISCARD</button>

                @if(!Route::is("theory-words-edit"))
                    <button type="button" class="negative ui button delete
                    @if (!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))
                    lena
                @endif" id="word_delete_button"> DELETE PAGE

                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script defer src="{{asset('js/keditor.js')}}"></script>
<script>

    $(document).ready(function () {
        // =============== audio API
        let genByWord = $('.cloud.download').parent('.spell-word');
        let genByLetter = $('.cloud.download').parent('.spell-letter');

        let audioUrl = 'https://storage.googleapis.com/php_statics_public/word/audio/';

        let audioWordSpell = new Audio(audioUrl + '{{$word->word}}_word.mp3');
        let audioLetterSpell = new Audio(audioUrl + '{{$word->word}}_letter.mp3');
        // ======= check if file is in here

        console.log(audioWordSpell.src);
        

        function check(){
            fetch(audioWordSpell.src).then(res => {
                if(res.status === 200){
                    playGenerated('.spell-word',  audioWordSpell, '.word .del-file');
                } else {
                    spellFunctionality(genByWord, '.sound-word', '.spell.word .del-file');
                }
            });
            fetch(audioLetterSpell.src).then(res => {
                if(res.status === 200){
                    playGenerated('.spell-letter',  audioLetterSpell, '.letter .del-file');
                } else {
                    spellFunctionality(genByLetter, '.sound-letter', '.spell.letter .del-file');
                }
            });
        }
        check()


        // ======= end check


            function playGenerated(playBtn, audio, resetBtn){
                let thisAudio = $('.main-local textarea').attr('readonly', true);
                $('.spelling').addClass('disabled');
                $(playBtn).off("click");
                $(playBtn).removeClass(playBtn).addClass("spell-play");
                $(playBtn).html('<i class="play icon large"></i>');
                $(playBtn).click(function () {
                    if (audio.paused) {
                        audio.play();
                        $(playBtn).html('<i class="pause icon large"></i>');
                    } else {
                        audio.pause();
                        $(playBtn).html('<i class="play icon large"></i>');
                    }
                    audio.addEventListener('ended', function () {
                        $(playBtn).html('<i class="play icon large"></i>');
                    });
                });
                $(resetBtn).click(function () {
                    $(".spell-modal").modal({
                        onApprove: function () {
                            $(playBtn).off("click");
                            $(playBtn).removeClass("spell-play");
                            $(playBtn).siblings('.spelling').find('input').remove();
                            $(playBtn).html('<i class="cloud download icon large"></i>');
                            $('.spelling').removeClass('disabled');
                            $('.main-local textarea').attr('readonly', false);


                            if(resetBtn === '.word .del-file'){
                                $('input[name="update-audio-word"]').attr('value', true);
                                $('input.sound-word').remove();
                                audioWordSpell = ''
                            }else{
                                $('input[name="update-audio-letter"]').attr('value', true);
                                $('input.sound-letter').remove();
                                audioLetterSpell = ''
                            }
                            check();
                            $('.button.save').attr('disabled', false);

                            // playGenerated(playBtn, audio, resetBtn)
                        }
                    }).modal("show");
                });
            };
            // playGenerated('.spell-letter',  audioLetterSpell, '.letter .del-file');
            // playGenerated('.spell-word',  audioWordSpell, '.word .del-file');


            function spellFunctionality(spellType, inputType, resetBtn) {
                let inputVal;
                let thisAudio;
                if($('.spell-letter .cloud').length || $('.spell-word .cloud').length){
                $(spellType).click(function () {
                    inputVal = $('.main-local textarea').val();
                    thisAudio = $('.main-local textarea').attr('readonly', true);
                    $('.button.save').attr('disabled', false);

                    if (spellType === genByLetter) {
                        function byLetter(input) {
                            let parsed = input.split('').join('-');
                            return '(' + parsed.split('-').join(')-(') + ')';
                        }

                        inputVal = byLetter(inputVal)
                    }
                    if(inputType === '.sound-word'){
                        $('input[name="update-audio-word"]').attr('value', true);
                        $(spellType).siblings('.spelling').append('<input class="sound-word" type="file" name="audio-word" accept="audio/mpeg3">')
                    }else{
                        $('input[name="update-audio-letter"]').attr('value', true);
                        $(spellType).siblings('.spelling').append('<input class="sound-letter" type="file" name="audio-letter" accept="audio/mpeg3">')
                    }
                    // thisAudio.addClass('disabled').attr('disabled', true);
                    thisAudio.siblings('.spelling').addClass('disabled');
                    $(spellType).html('<i class="spinner loading icon large"></i>');

                    var requestData = {
                        prompt: inputVal,
                        voice: 'Josh'
                    };
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'https://speech-service.langly.ai/api/v2/generate_voiceover', true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.responseType = 'arraybuffer';

                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            var response = xhr.response;
                            var blob = new Blob([response], {type: 'audio/mpeg3'});
                            var audioUrl = URL.createObjectURL(blob);

                            var dataTransfer = new DataTransfer();
                            var file = new File([blob], 'audio.mp3', {type: 'audio/mpeg3'});
                            dataTransfer.items.add(file);
                            $(inputType).prop('files', dataTransfer.files);

                            var audioElement = new Audio(audioUrl);
                            $(spellType).off("click");
                            $(spellType).removeClass(spellType).addClass("spell-play");
                            $(spellType).html('<i class="play icon large"></i>');

                            $(spellType).click(function () {
                                if (audioElement.paused) {
                                    audioElement.play();
                                    $(spellType).html('<i class="pause icon large"></i>');
                                } else {
                                    audioElement.pause();
                                    $(spellType).html('<i class="play icon large"></i>');
                                }
                                audioElement.addEventListener('ended', function () {
                                    $(spellType).html('<i class="play icon large"></i>');
                                });
                            });
                        }
                    };


                    xhr.onerror = function () {
                        console.log('Error:', xhr.statusText);
                    };

                    xhr.send(JSON.stringify(requestData));
                });
                }
                $(resetBtn).click(function () {
                    $(".spell-modal").modal({
                        onApprove: function () {
                            $(spellType).off("click");
                            $(spellType).removeClass("spell-play");
                            $(spellType).html('<i class="cloud download icon large"></i>');
                            $(spellType).siblings('.spelling').find('input').remove();
                            $('.main-local textarea').attr('readonly', false);
                            $('.task-audio-dropdown, .task-audio input, .button.spelling').removeClass("disabled").attr("disabled", false);

                            if(resetBtn === '.word .del-file'){
                                $('input[name="update-audio-word"]').attr('value', true);
                                audioWordSpell = ''
                            }else{
                                $('input[name="update-audio-letter"]').attr('value', true);
                                audioLetterSpell = ''
                            }
                            check()

                            $(inputType).each(function () {
                                if ($(this).attr('src')) {
                                    $('.main-local textarea').attr('disabled', true);
                                } else {
                                    $('.main-local textarea').attr('disabled', false);
                                }
                            });
                            spellFunctionality(spellType, inputType, resetBtn);
                        }
                    }).modal("show");
                });
            }
            // spellFunctionality(genByWord, '.sound-word', '.spell.word .del-file');
            // spellFunctionality(genByLetter, '.sound-letter', '.spell.letter .del-file');



        // =============== audio API end


        $('.button.save, .button.discard').attr('disabled', true)
        $('.button.discard').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".discard-modal").modal({
                    onApprove: function () {

                        $.ajax({
                            type: "get",
                            url: @if(isset($theoryId))"/admin/theory/words/edit/" + {{$word->id}} + "/{{$theoryId ?? null}}"
                            @else"/admin/word/edit/" +{{$word->id}}@endif,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                console.log('meow')
                                let form = $("#edit");
                                form.empty();
                                form.append(res)
                            },
                        });
                        $('.form').removeClass('contentChanged');
                        $('.button.save, .button.discard').attr('disabled', true);
                    }
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: @if(isset($theoryId))"/admin/theory/words/edit/" + {{$word->id}} + "/{{$theoryId ?? null}}"
                    @else"/admin/word/edit/" +{{$word->id}}@endif,

                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#edit");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        });
        // Show / hide message
        $('.button.save, .button.discard').attr('disabled', true)
        if ($(".form .main-local .textarea").val().length && $('.ck-content strong').length) {
            $('.hint').hide();
            $('.ck.ck-editor').removeClass('check');
        } else {
            $('.hint').show();
            $('.ck.ck-editor').addClass('check');
            $('.form').addClass('contentChanged')
        }

        $('.form').on('keyup', function () {
            $('.form').addClass('contentChanged')
            if ($(".form .main-local .textarea").val().length && $('.ck-content strong').length) {
                $('.button.save, .button.discard').attr('disabled', false);
            } else {
                $('.button.save, .button.discard').attr('disabled', true);
            }
        });

        $('.ck-button').attr('aria-labelledby', 'ck-editor__aria-label_e66602d9c582c544a15df7ef3eb7fcdd4').on('click', function () {
            $('.form').addClass('contentChanged');
            if ($(".form .main-local .textarea").val().length && $('.ck-content strong').length) {
                $('.hint').hide();
                $('.ck.ck-editor').removeClass('check');

                $('.button.save, .button.discard').attr('disabled', false);
            } else {
                $('.hint').show();
                $('.ck.ck-editor').addClass('check');
                $('.button.save, .button.discard').attr('disabled', true);
            }
        });
    });
    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }


        $('.oxford-search').on('click', function () {
            $('.tree').animate({
                'opacity': '0',
                'margin-left': "-25%",
            }, 750);
            $('#edit .exit').animate({
                'opacity': '0',
            })
            $('.oxford').animate({
                'opacity': '1',
                'margin-right': '0'
            }, 750)
        });
        $('.api-exit').on('click', function () {
            $('.tree').animate({
                'opacity': '1',
                'margin-left': "0",
            }, 750);
            $('#edit .exit').animate({
                'opacity': '1'
            })
            $('.oxford').animate({
                'opacity': '0',
                'margin-right': "-25%"
            }, 750)
        })



    $('#word_save_button').on('click', function () {
        let elements = $(".ck-content > p");
        let toInput = $('input[name="example_list"]');
        let arr = [];
        for (let i = 0; i < elements.length; i++) {
            arr.push(elements[i].outerHTML.replaceAll(/&nbsp;/g, ' '));
        }
        toInput.val(JSON.stringify(arr))
        $('#word_create_form').submit();

    })

    $(".oxford-search").on('click', function () {
        let value = $('textarea[name="word"]').val();
        let data = {word: value}
        $.ajax({
            type: "post",
            url: "{{route('get-oxford-data')}}",
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let form = $(".oxford");
                form.empty();
                form.append(res)
            },
        });
    })

    $('.button.delete:not(.lena)').on('click', function () {
        $(".delete-modal").modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "{{route('word-destroy',$word->id)}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        let form = $("#edit");
                        form.empty();
                        $("#{{$word->id}}").parent().remove();
                    },
                });
            }
        }).modal('show');
    })

</script>
