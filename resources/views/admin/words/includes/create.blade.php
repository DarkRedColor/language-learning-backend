<div class="edit">

    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>New Word</h3>
    </div>
    <div class="edit-area words">
        <form action="{{route('word-store')}}" class="ui form" method="post" id="word_create_form" enctype="multipart/form-data">
            @csrf
            <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
            <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
            <input name="example_list" value="" hidden/>
            <div class="two fields">
                <div class="ui field">
                    <div class="ui field accordion">
                        <div class="title inputs">
                            <div class="field main-local">
                                <label>Localization</label>
                                <div class="ui label search oxford-search">
                                    <i class="search icon"></i>
                                </div>
                                <textarea class="textarea" name="word" rows="2"></textarea>
                                <div class="ui label lang-iso">EN
                                    <i class="chevron down icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="content field inputs">
                            @foreach($languages as $item)
                                @if($item->lcid <> 'en')
                                    <div class="field">
                                            <textarea class="textarea" rows="2"
                                                      name="meaning_{{$item->lcid}}"></textarea>
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
                        <input placeholder="Transcription word" name="transcription" type="text">
                    </div>
                    <div class="ten wide field">
                        <div class="inline field file">
                            <label id="dropped" class="add-bg ">
                                <input type="file" name="image" accept="image/png"
                                       onchange="previewFile(this);$('#delete_flag_of_language').remove()"
                                       placeholder="IMG">
                                <i class="upload icon">
                                    <img class="previewImg" data-value="" src="">
                                </i>
                                {{--                                <a href="#" class="dis-img" onclick="delLanguageFlag()"><i--}}
                                {{--                                            class="close icon"></i></a>--}}
                            </label>
                        </div>
                    </div>
                    <div class="field">
                        <label>Definitions</label>
                        <input placeholder="Word definition" name="definition_en" type="text">
                    </div>
                    <div class="field">
                        <label>Examples</label>
                        <div class="editor">
                            <div id="toolbar-container"></div>
                            <div id="editor"></div>
                        </div>
                        <p class="hint">*You didn't meet all interface requirements</p>
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="button" href="#" class="positive ui button save" id="word_save_button">SAVE</button>
                <a href="#" class="negative ui button discard">CANCEL</a>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script src="{{asset('js/keditor.js')}}"></script>


<script>
    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }

    $('.button.discard').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    $('.edit').empty();
                }
            }).modal('show');
        } else {
            $('.edit').empty();
        }
    })

    $(document).ready(function () {
        // Oxford API search
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
                'margin-right': '0'  }, 750)
        });
        $('.api-exit').on('click', function () {
            $('.tree').animate({
                'opacity': '1',
                'margin-left': "0",
            }, 750);
            $('#edit .exit').animate({
                'opacity': '1'  })
            $('.oxford').animate({
                'opacity': '0',
                'margin-right': "-25%"  }, 750)
        })
        // Show / hide message
        $('.button.save, .button.discard').attr('disabled', true)
        if ($(".form .main-local .textarea").val().length && $('.ck-content strong').length) {
            $('.hint').hide();
            $('.ck.ck-editor').removeClass('check');
        } else {
            $('.hint').show();
            $('.ck.ck-editor').addClass('check');
            $('.form').removeClass('contentChanged');
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
    })

    // =============== audio API
    function spellFunctionality(spellType, inputType, resetBtn) {
        let inputVal;
        let thisAudio;
        $(spellType).click(function() {
            inputVal = $('.main-local textarea').val();
            thisAudio = $('.main-local textarea').attr('readonly', true);

            if(spellType === '.spell-letter') {
                function byLetter(input) {
                    let parsed = input.split('').join('-');
                    return '(' + parsed.split('-').join(')-(') + ')';
                }
                inputVal = byLetter(inputVal)
            }
            if(inputType === '.sound-word'){
                $(spellType).siblings('.spelling').append('<input class="sound-word" type="file" name="audio-word" accept="audio/mpeg3">')
            }else{
                $(spellType).siblings('.spelling').append('<input class="sound-letter" type="file" name="audio-letter" accept="audio/mpeg3">')
            }
            // thisAudio.addClass('disabled').attr('disabled', true);
            thisAudio.siblings('.spelling').addClass('disabled');
            $(spellType).html('<i class="spinner loading icon large"></i>');
            $('.spelling').addClass('disabled');

            var requestData = {
                prompt: inputVal,
                voice: 'Josh'
            };
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'https://speech-service.langly.ai/api/v2/generate_voiceover', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.responseType = 'arraybuffer';

            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = xhr.response;
                    var blob = new Blob([response], { type: 'audio/mpeg3' });
                    var audioUrl = URL.createObjectURL(blob);

                    var dataTransfer = new DataTransfer();
                    var file = new File([blob], 'audio.mp3', { type: 'audio/mpeg3' });
                    dataTransfer.items.add(file);
                    $(inputType).prop('files', dataTransfer.files);

                    var audioElement = new Audio(audioUrl);
                    $(spellType).off("click");
                    $(spellType).removeClass(spellType).addClass("spell-play");
                    $(spellType).html('<i class="play icon large"></i>');

                    $(spellType).click(function() {
                        if (audioElement.paused) {
                            audioElement.play();
                            $(spellType).html('<i class="pause icon large"></i>');
                        } else {
                            audioElement.pause();
                            $(spellType).html('<i class="play icon large"></i>');
                        }
                        audioElement.addEventListener('ended', function() {
                            $(spellType).html('<i class="play icon large"></i>');
                        });
                    });
                }
            };


            xhr.onerror = function() {
                console.log('Error:', xhr.statusText);
            };

            xhr.send(JSON.stringify(requestData));
        });

        $(resetBtn).click(function() {
            $(".spell-modal").modal({
                onApprove: function() {
                    $(spellType).off("click");
                    $(spellType).removeClass("spell-play");
                    $(spellType).html('<i class="cloud download icon large"></i>');
                    $(spellType).siblings('.spelling').find('input').remove();
                    $('.main-local textarea').attr('readonly', false);
                    $('.spelling').removeClass('disabled');

                    $(inputType).each(function() {
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

    spellFunctionality('.spell-word', '.sound-word', '.spell.word .del-file');
    spellFunctionality('.spell-letter', '.sound-letter', '.spell.letter .del-file');

    // =============== audio API end

    $('#word_save_button').on('click', function () {
        let elements = $(".ck-content > p");
        let toInput = $('input[name="example_list"]');
        let arr = [];
        for (let i = 0; i < elements.length; i++) {
            arr.push(elements[i].outerHTML);
        }
        toInput.val(JSON.stringify(arr))
        $('#word_create_form').submit();
    })

    $("div.search").on('click', function () {
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
</script>
