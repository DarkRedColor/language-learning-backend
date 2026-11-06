<div class="edit">
    <h1>Preview</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Game</h3>
        <button class="ui icon basic button legend-btn">
            <i class="info icon"></i>
        </button>
    </div>
    <div class="edit-area grid games">
        <form action="{{route('game-store',[$practiceId, $templateId, $learningId ])}}"
              class="ui form game-audio" method="post" enctype="multipart/form-data">
            @csrf
            <div class="ui two column grid">
                <div class="column">
                    <div class="field game-description">
                        <label>Game description</label>
                        <div class="ui left labeled button field sixteen wide" tabindex="0">
                            <div class="ui selection search dropdown description-dropdown">
                                <input type="hidden" name="description"
                                       value="{{$description->game_description_id ?? null}}">
                                @php
                                    $des= 'select something';
                                    $elem = null;
                                    if(isset($description)){
                                        $elem = $descriptions->find($description->game_description_id);
                                    }
                                    if($elem != null){
                                        $des = $elem->name;
                                    }
                                @endphp

                                <div class="default text">{{$description}}</div>
                                <div class="menu">
                                    @foreach($descriptions as $item)
                                        <div class="item" data-value="{{$item->id}}">{{$item->description_en}}</div>
                                    @endforeach
                                </div>
                                <i class="chevron down icon"></i>
                            </div>
                            <div class="ui icon button default-description">
                                <i class="sync alternate icon"></i>
                            </div>
                        </div>
                        <label><span>Main description: </span></label>
                    </div>
                    @if(!in_array($template->answer,['true_false']))
                        <input type="hidden" name="correct_answer" value="" id="correct_answer">
                        <input type="hidden" name="clue" value="" id="clue">
                        <input type="hidden" name="incorrect_answer" value="" id="incorrect_answer">
                    @endif

                    @switch($template->task)
                        @case("text")
                            @if(in_array($template->answer,["erudite","sentence",'type']))
                                @include('admin.game-templates.html-inputs.input-text-localisation',[$allWordsOfTheory,$selectedWord,$languages])
                            @elseif(in_array($template->answer,["words"]))
                                <input hidden name="task" class="tasks">
                                <input hidden name="clues" class="clues">
                                <input hidden name="answers" class="answers">
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @else
                                @include('admin.game-templates.html-inputs.input-text',[$allWordsOfTheory,$selectedWord])
                            @endif
                            @break
                        @case("image")
                            @include('admin.game-templates.html-inputs.input-image',[$allWordsOfTheory,$selectedWord,$languages])
                            @break
                        @case("audio")
                            @if(in_array($template->answer,["erudite","sentence"]))
                                @include('admin.game-templates.html-inputs.input-audio-localisation',[$allWordsOfTheory,$selectedWord,$languages])
                            @else
                                @include('admin.game-templates.html-inputs.input-audio',[$allWordsOfTheory,$selectedWord])
                            @endif
                            @break
                        @case("space")
                            @if($template->additional_task == 'text')
                                <input hidden name="task" class="tasks">
                                <input hidden name="clues" class="clues">
                                <input hidden name="answers" class="answers">
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @elseif($template->additional_task == 'audio')
                                <input hidden name="task" class="tasks">
                                <input hidden name="clues" class="clues">
                                <input hidden name="answers" class="answers">
                                @include('admin.game-templates.html-inputs.input-space-audio')
                            @else
                                <input hidden name="task" class="tasks">
                                <input hidden name="clues" class="clues">
                                <input hidden name="answers" class="answers">
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @endif
                            @break
                        @case("video")
                            <div class="edit-area adventure soon">
                                <h1>Video Coming soon</h1>
                            </div>
                            @break
                        @case("symbols")
                            @if($template->additional_task == 'text')
                                @include('admin.game-templates.html-inputs.input-text',[$allWordsOfTheory,$selectedWord])
                            @else
                                @include('admin.game-templates.html-inputs.input-audio',[$allWordsOfTheory,$selectedWord])
                            @endif
                            @break
                        @default
                            {{--                @include('admin.coming-soon')--}}
                    @endswitch

                    @switch($template->additional_task)
                        @case("text")
                            @if(!in_array($template->task,['link_words','space','symbols','list']))
                                @include('admin.game-templates.html-inputs.input-text-additional')
                            @endif
                            @break
                        @case("image")
                            @include('admin.game-templates.html-inputs.input-image-additional')
                            @break
                        @case(null)
                            @break
                        @default
                            @if(!in_array($template->task,['link_words','space','symbols','list']))
                                @include('admin.game-templates.html-inputs.input-audio-additional')
                            @endif
                    @endswitch

                    @switch($template->answer)
                        @case("type")
                            @if($template->task == "space")
                                @include('admin.game-templates.html-inputs.input-correct-answer-space')
                            @elseif($template->task == "text")
                                @include('admin.game-templates.html-inputs.input-localized-correct-answer',$languages)
                            @elseif($template->task == "audio")
                                @include('admin.game-templates.html-inputs.input-localized-correct-answer',$languages)
                            @elseif($template->task == "image")
                                @include('admin.game-templates.html-inputs.input-localized-correct-answer',$languages)
                            @else
                                @include('admin.game-templates.html-inputs.input-type')
                            @endif
                            @break
                        @case("true_false")
                            <div class="ui field">
                                <h5>Answers</h5>
                                @if($template->task == "space")
                                    @include('admin.game-templates.html-inputs.input-correct-answer-space')
                                @else
                                    @include('admin.game-templates.html-inputs.input-type')
                                @endif
                            </div>
                            @break
                        @case("remove")
                            <div class="ui field">
                                <h5>Answers</h5>
                                @include('admin.game-templates.html-inputs.input-remove')
                            </div>
                            @break
                        @case("erudite")
                            <div class="ui field erudit">
                                <h5>Answers</h5>
                                @include('admin.game-templates.html-inputs.input-erudite')
                            </div>
                            @break
                        @case("words")
                            <div class="ui field">
                                <h5>Answers</h5>
                                @include('admin.game-templates.html-inputs.input-correct-answer-space')
                            </div>
                            <div class="ui field">
                                <h5>Wrong Answers</h5>
                                @include('admin.game-templates.html-inputs.input-wrong-answer-tags')
                            </div>
                            @break
                        @case("sentence")
                            <div class="ui field">
                                <h5>Answers</h5>
                                @include('admin.game-templates.html-inputs.input-erudite')
                            </div>
                            <div class="ui field">
                                <h5>Wrong Answers</h5>
                                @include('admin.game-templates.html-inputs.input-wrong-answer-tags')
                            </div>
                            @break
                        @case("2")
                            <div class="edit-area adventure soon">
                                <h1>This number of answer Coming soon</h1>
                            </div>
                            @break
                        @case("3")
                            <div class="ui field">
                                <h5>Answers</h5>
                                @if($template->task == "space")
                                    @include('admin.game-templates.html-inputs.input-correct-answer-space')
                                @else
                                    @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                @endif
                                @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                                @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                            </div>
                            @break
                        @case("4")
                            @if($template->task == "link_words")
                                <div class="ui field ">
                                    @if($template->additional_task == 'audio')
                                        <div class="two fields">
                                            <div class="field">
                                                <label class="type-text">Example: Audio</label>
                                            </div>
                                            <div class="field">
                                                <label class="type-text">Pair</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="two fields">
                                            <div class="field">
                                                <label class="type-text">Example: Text</label>
                                            </div>
                                            <div class="field">
                                                <label class="type-text">Pair</label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                </div>
                            @else
                                <div class="ui field">
                                    <label>Answers</label>
                                    <div class="two fields">
                                        @if($template->task == "space")
                                            @include('admin.game-templates.html-inputs.input-correct-answer-space')
                                        @else
                                            @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @endif
                                        @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                                    </div>
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                                    </div>
                                </div>
                            @endif
                            @break
                        @case("5")
                            @if($template->task == "link_words")
                                <div class="ui field ">
                                    @if($template->additional_task == 'audio')
                                        <div class="two fields">
                                            <div class="field">
                                                <label class="type-text">Example: Audio</label>
                                            </div>
                                            <div class="field">
                                                <label class="type-text">Pair</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="two fields">
                                            <div class="field">
                                                <label class="type-text">Example: Text</label>
                                            </div>
                                            <div class="field">
                                                <label class="type-text">Pair</label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link',[$allWordsOfTheory,$selectedWord])
                                    </div>
                                </div>
                            @endif
                            @break
                        @case("matched_not")
                            @if($template->additional_task == 'text')
                                @include('admin.game-templates.html-inputs.input-text-matched')
                            @elseif($template->additional_task == 'audio')
                                @include('admin.game-templates.html-inputs.input-audio-matched')
                            @endif
                            @break
                        @case("list")
                            @if($template->additional_task == 'text')
                                @include('admin.game-templates.html-inputs.input-text-list')
                            @elseif($template->additional_task == 'audio')
                                @include('admin.game-templates.html-inputs.input-audio-list')
                            @endif
                            @break
                        @default
                            <div class="edit-area adventure soon">
                                <h1>Coming soon</h1>
                            </div>
                    @endswitch
                    <div class="field">
                        <label>Link Theory</label>
                        <input name="theory" value="" id="theory" hidden/>
                        <select class="ui fluid search dropdown theory" multiple="">
                            <option value="">Theory</option>
                            @foreach($theories as $item)
                                <option value="{{$item->id}}">@if(isset($item->name) and !empty($item->name)){{$item->name}}@else{{$item->description_en}}@endif</option>
                            @endforeach
                            <i class="chevron down icon"></i>
                        </select>
                    </div>
                </div>
                <div class="column">
                    <p class="filling-type">Filling</p>
                    <div class="ui buttons thumb">
                        <button type="button" class="ui button manual">Manual</button>
                        <input type="hidden" id="tumbler" value="manual" name="input_type">
                        <div class="or"></div>
                        <button type="button" class="ui button selective">Selective</button>
                    </div>
                    <p class="hint-type">Hint</p>
                    <div class="ui buttons thumb">
                        <button type="button" class="ui button hint-manual">Manual</button>
                        <input type="hidden" id="hint-tumbler" value="manual" name="hint_option">
                        <div class="or"></div>
                        <button type="button" class="ui button hint-selective">Selective</button>
                    </div>
                    @if($template->task == "link_words")
                        <div class="loc-thumb">
                            <input type="hidden" value="1" name="localised" id="localised">
                            <p class="pair-type">Pair</p>
                            <div class="ui buttons pair">
                                <button type="button" class="ui button pair_en">EN/LOC</button>
                                <div class="or"></div>
                                <button type="button" class="ui button pair_loc">EN/EN</button>
                            </div>
                        </div>
                    @endif
                    @if(in_array($template->task, ['text', 'space']) and $template->answer == "type")
                        <div class="rules-thumb">
                            <p class="rules-type">Rules</p>
                            <input type="hidden" value="0" name="capital" id="capital-rule">
                            <input type="hidden" value="0" name="sign" id="sign-rule">
                            <div class="rules-wrap">
                                <div class="ui buttons rules">
                                    <button type="button" class="ui button non-capital blue">Non-capital</button>
                                    <div class="or"></div>
                                    <button type="button" class="ui button capital ">Capital</button>
                                </div>
                                <div class="ui buttons rules">
                                    <button type="button" class="ui button non-signs blue">Non-signs</button>
                                    <div class="or"></div>
                                    <button type="button" class="ui button signs ">Signs</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save">SAVE</button>
                <button type="button" class="grey ui button cancel-btn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $('.default-description').on('click', function () {
        $('.ui.selection.search.dropdown').dropdown('restore defaults');
    });
    $(document).ready(function () {
        // --------- character counter ----------
        var inputGame = $('.games form input[type="text"], form textarea').not('#correct input, #incorrect input, input[class="hid"]')
        inputGame.parent().append('<span class="num"></span>')

        // onload show character count
        inputGame.each(function(){
            var wordsLength = $(this).val().length
            $(this).siblings('.num').text(wordsLength)
        })

        // on keyup/click update character count
        inputGame.on('keyup keydown', function(){
            var wordsLength = $(this).val().length
            $(this).siblings('.num').text(wordsLength)
            // -------- check counter state
            $('form .num').each(function(){
                var thisNum = $(this).text()
                var thisInt = parseInt(thisNum)
                var thisItem = $(this)
                if(thisInt < 70){
                    thisItem.removeClass('green orange red')
                }else if(thisInt < 100){
                    thisItem.addClass('green')
                    thisItem.removeClass('red orange')
                }else if(thisInt < 150){
                    thisItem.addClass('orange')
                    thisItem.removeClass('red green')
                }else if(thisInt > 150){
                    thisItem.addClass('red')
                    thisItem.removeClass('orange green')
                }
            })
        })

        // ckEditor character count
        $('form .game-editor').parent().append('<span class="num"></span>')
        $('.game-editor').siblings('.num').text($('form .ck-editor p').text().length)
        $('form .ck-content').on('keyup click', function(){
            var wordsLength = $(this).text().length
            $(this).parents('.game-editor').siblings('.num').text(wordsLength)
            // -------- check counter state
            $('form .num').each(function(){
                var thisNum = $(this).text()
                var thisInt = parseInt(thisNum)
                var thisItem = $(this)
                if(thisInt < 70){
                    thisItem.removeClass('green orange red')
                }else if(thisInt < 100){
                    thisItem.addClass('green')
                    thisItem.removeClass('red orange')
                }else if(thisInt < 150){
                    thisItem.addClass('orange')
                    thisItem.removeClass('red green')
                }else if(thisInt > 150){
                    thisItem.addClass('red')
                    thisItem.removeClass('orange green')
                }
            })
        })
        // ------------------------------------
        $('.legend-accordion').accordion();
        $('.legend-accordion .title, .legend-accordion .content').removeClass('active')
        var type = $('.game_creation h5');
        switch ($('.game_creation h5').attr('class')) {
            case 'type-text':
                type.next('.fields').find('.field:first-child').prepend('<label>Example: Text</label>')
                type.next('.fields').find('.field:last-child').prepend('<label>Pair</label>')
                break;
            case 'type-audio':
                type.next('.fields').find('.field:first-child').prepend('<label>Example: Audio</label>')
                type.next('.fields').find('.field:last-child').prepend('<label>Pair</label>')
                break;
            default:
                console.log('no one')
        }
        $('.form').on('keyup', function () {
            var form = $(this);
            var inputs = $('.form').find('input').not('[type=hidden]');
            var answer = $('#correct .remove-span');

            if (inputs.val().length > 0 && !answer) {
                form.addClass('contentChanged')
            } else {
                form.removeClass('contentChanged')
            }
        });
        // info tooltip
        $('.info-btn').popup({
            popup: $('.info-popup'),
            on: 'click'
        });

        // add to correct answer
        $('.ck-content').on('DOMSubtreeModified', function () {
            if ($('.ck-content i').length) {
                $('.button.save').attr('disabled', false);
            } else {
                $('.button.save').attr('disabled', true);
            }

            var italicText = $('.ck-content').find('i');
            $('.correct-fields').children().remove();
            italicText.each(function () {
                $('.correct-fields').append('<p class="correct-answer">' + $(this).text() + '</p>')
            })
        })

        $('.form').on('keyup', function () {
            //$(this).addClass('contentChanged')
            var inputCorrect = $(this).find('input[name^="correct"]').not('input[name*="link"]')
            var arr = []
            inputCorrect.each(function () {
                arr.push($(this).val())
            })

            $.each(arr, function (value) {
                if (value === '' || value === undefined) {
                    $('.button.save').attr('disabled', true)
                } else {
                    $('.button.save').attr('disabled', false)
                }
            });
        });
        $('.form').on('keyup', function () {
            if ($(this).find('input:not([type="hidden"]), .textarea').val() !== '' || $('img').attr('src') !== '') {
                $('.form').addClass('contentChanged')
                $('.button.save, .discard-theory, .cancel-btn').attr('disabled', false)
            } else {

                $('.button.save, .discard-theory, .cancel-btn').attr('disabled', true)
            }
        })
        // ================== answer tags inputs
        var counter = $('.erudit .word > span').length;
        if ($('.word.erudite').parents('.erudit').length) {
            $(this).find('#correct input').on({
                focusout: function() {
                    var txt = this.value.replace(/[^a-z0-9+-.!?@;:'$ ]/ig, '');
                    if (counter < 7 || this.value === ' ') {
                        if (txt) $("<span/>", {
                            text: txt,
                            insertBefore: this
                        });
                        this.value = "";
                        counter++;
                    }
                    if ($('#correct span').last().text() === ' ') {
                        counter = 0;
                    }
                },
                keyup: function(ev) {
                    if (/(13)/.test(ev.which)) $(this).focusout();
                    if (ev.keyCode === 8 && this.value.length <= 0) {
                        $(this).parent().children('span').last().remove()
                        counter -= 1;
                    }
                }
            });
        }else{
            $(".ui.field .word input").on({
                focusout: function () {
                    var txt = this.value.replace(/[^a-z0-9+-.':;_!?@ ]/ig, ''); // allowed characters
                    if (txt) $("<span/>", {text: txt, insertBefore: this});
                    this.value = "";
                },
                keyup: function (ev) {
                    if (/(13)/.test(ev.which)) $(this).focusout();
                    if (ev.keyCode === 8 && this.value.length <= 0) {
                        $(this).parent().children('span').last().remove()
                    }
                }
            });
        }

        // ================== toggle clue in answers
        $('#correct.erudite').on('click', 'span', function () {
            $(this).toggleClass('clue');
        });
        $('#correct.remove-w').on('click', 'span', function () {
            $(this).toggleClass('remove-span');
            // check remove-word
            if ($('#correct.remove-w span').hasClass('remove-span')) {
                $('.form').addClass('contentChanged')
            } else {
                $('.form').removeClass('contentChanged')
            }
        });

        $('.form').on('DOMSubtreeModified', function () {
            if ($(this).hasClass('contentChanged')) {
                $('.button.save').attr('disabled', false);
            } else {
                $('.button.save').attr('disabled', true);
            }
        })

        $('.button.save').one('click', function () {
            switch ($('#tumbler').val()) {
                case ('manual'):
                    $('.selective-way').remove();
                    break;
                case ('selective'):
                    $('.manual-way').remove();
                    break;
            };

            var correct = [],
            clue = [],
            incorrect = [],
            correctAnswer = [],
            ckContent = $('.ck-content'),
            elements = $('.ck-content p');

            // remove unused elements
            elements.each(function(){
                var text = $(this).html();
                text = text.replace(/’/g, "'")
                    .replace(/‘/g, "'")
                    .replace(/&nbsp;/g, '')
                    .replace(/�/g, '');
                $(this).html(text);
            })

            $('#correct span').each(function () {
                correct.push($(this).text());
            });
            $('#correct span.clue, #correct span.remove-span').each(function () {
                clue.push($(this).index())
            });
            $('#incorrect span').each(function () {
                incorrect.push($(this).text());
            });

            ckContent.find('i').each(function () {
                correctAnswer.push($(this).text())
            })
            var taskAnswer = []
            ckContent.children().each(function () {
                taskAnswer.push($(this).html().replace(/<i>(.*?)<\/i>/g, "@").replace(/ {(.*?)}/g, "$"))
            })

            var taskClue = []
            ckContent.children().each(function () {
                taskClue.push($(this).text().match(/{(.*?)}/g))
            })
            let symbolArr = []
            $('.symbol-boolean').each(function () {
                let text = $(this).text(),
                    inputValue = $(this).next().val();
                switch (text) {
                    case 'False':
                        symbolArr.push(['F', "@@"+inputValue])
                        break;
                    case 'True':
                        symbolArr.push(['T', "@@"+inputValue])
                        break;
                }
            })
            if (symbolArr[0] != null) {

                $('input[name="link_answers[]"]').each(function (index) {
                    $(this).val(symbolArr[index])
                })
            }
            var arr = [];
            $('.search.dropdown.theory > a').each(function () {
                arr.push($(this).attr('data-value'))
            })

            $('#theory').val(JSON.stringify(arr));
            $('.tasks').val(JSON.stringify(taskAnswer))
            $('.clues').val(JSON.stringify(taskClue))
            $('.answers').val(JSON.stringify(correctAnswer))
            $('#correct_answer').val(JSON.stringify(correct));
            $('#clue').val(JSON.stringify(clue));
            $('#incorrect_answer').val(JSON.stringify(incorrect));

            $('.form').submit();
        })

        $('.cancel-btn').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.discard-modal').modal({
                    onApprove: function () {
                        location.reload();
                    }
                }).modal('show')
            } else {
                location.reload();
            }
        })


    })
    $(".add-localization").on('click', function () {
        $(".localized:first-child").clone().appendTo(".localized-answer").after(function () {
            $(this).find('textarea').val('')
            $(this).find('.num').text('0').removeClass('green orange red')
            $(this).find('.main-local').append(
                `
                <div class="ui button negative answer-del">
                    <i class="trash alternate icon"></i>
                </div>
                `
            )
        });
        $('.ui.accordion').accordion({
            selector: {
                trigger: '.lang-iso'
            }
        });
        // character counter
        $('.localized textarea').on('keyup click', function(){
            var thisVal = $(this).val().length;
            $(this).siblings('.num').text(thisVal)
            // -------- check counter state
            $('form .num').each(function(){
                var thisNum = $(this).text()
                var thisInt = parseInt(thisNum)
                var thisItem = $(this)
                if(thisInt < 70){
                    thisItem.removeClass('green orange red')
                }else if(thisInt < 100){
                    thisItem.addClass('green')
                    thisItem.removeClass('red orange')
                }else if(thisInt < 150){
                    thisItem.addClass('orange')
                    thisItem.removeClass('red green')
                }else if(thisInt > 150){
                    thisItem.addClass('red')
                    thisItem.removeClass('orange green')
                }
            })
        })
        $('.answer-del').on('click', function () {
            if ($(this).parents('.localized').find('textarea').val().length > 0) {
                var thisLocal = $(this);
                $('.delete-modal').modal({
                    onApprove: function () {
                        $(thisLocal).parents('.localized').remove()
                    }
                }).modal('show');
            } else {
                $(this).parents('.localized').remove()
            }
        })
    });
    // ============ tumbler logic start
    var selective = $('.selective-way'),
        manual = $('.manual-way'),
        selectiveHint = $('.selective-hint'),
        manualHint = $('.manual-hint'),
        inputs = $('.manual-way input:not([type=hidden]), .selective-way input:not([type=hidden]), .manual-way textarea'),
        manualBtn = $('.manual'),
        selectiveBtn = $('.selective'),
        hintManualBtn = $('.hint-manual'),
        hintSelectiveBtn = $('.hint-selective'),
        spellTumb = $('input[name="read_type"]'),
        currentButton = manualBtn,
        pair = $('.loc-thumb'),
        rulesThumb = $('.rules-thumb');

    $(document).ready(function (){
        selective.hide();
        selectiveHint.hide();
        $('#tumbler').val('manual')
        $('#hint-tumbler').val(0)
        $('.button.manual, .button.hint-manual').addClass('blue')
        pair.find('button:first-child').addClass('blue').after(function () {
            pair.hide();
        })
    })

    inputs.on('keyup change', function () {
        $('.form').addClass('contentChanged')
    })
    $('.search.dropdown').on('change', function () {
        $('.form').addClass('contentChanged')
    })


    manualBtn.click(function () {
        switchButton(manualBtn);
        $('#tumbler').val('manual')
    });

    selectiveBtn.click(function () {
        switchButton(selectiveBtn);
        $('#tumbler').val('selective')
    });

    hintManualBtn.click(function(){
        switchHint(hintManualBtn);
        $('#hint-tumbler').val(0)
    });

    hintSelectiveBtn.click(function(){
        switchHint(hintSelectiveBtn);
        $('#hint-tumbler').val(1)
    })

    function switchHint(newButton) {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    inputs.val('');
                    $('.task-audio-dropdown').dropdown('restore defaults');
                    $('.form').removeClass('contentChanged');
                    $('.answer-del').parents('.localized').remove();
                    pair.toggle();
                    selectiveHint.toggle();
                    manualHint.toggle();
                    newButton.siblings().removeClass('blue');
                    newButton.addClass('blue');
                    currentButton = newButton;
                    rulesThumb.toggle();
                    $('.main-local textarea').attr('readonly', false);
                    $('.task-audio-dropdown, .task-audio input, .button.spelling').removeClass('disabled').attr('disabled', false);
                }
            }).modal('show');
        } else {
            newButton.siblings().removeClass('blue');
            newButton.addClass('blue');
            currentButton = newButton;
            pair.toggle();
            selectiveHint.toggle();
            manualHint.toggle();
        }
    }
    function switchButton(newButton) {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    inputs.val('');
                    $('.task-audio-dropdown').dropdown('restore defaults');
                    $('.form').removeClass('contentChanged');
                    $('.answer-del').parents('.localized').remove();
                    pair.toggle();
                    selective.toggle();
                    manual.toggle();
                    newButton.siblings().removeClass('blue');
                    newButton.addClass('blue');
                    currentButton = newButton;
                    rulesThumb.toggle();
                    $('.main-local textarea').attr('readonly', false);
                    $('.task-audio-dropdown, .task-audio input, .button.spelling').removeClass('disabled').attr('disabled', false);
                }
            }).modal('show');
        } else {
            newButton.siblings().removeClass('blue');
            newButton.addClass('blue');
            currentButton = newButton;
            pair.toggle();
            selective.toggle();
            manual.toggle();
            rulesThumb.toggle();
        }
    }

    // =============== tumbler logic end

    // =============== start spelling
    $('.spelling').off().click(function () {
        if ($(this).children('p').text() === 'by word') {
            $(this).text(function (i, text) {
                return text === "by letter" ? "by word" : "by letter";
            })
        } else {
            $(this).text(function (i, text) {
                return text === "by word" ? "by letter" : "by word";

            })
        }
        $(this).toggleClass('word letter');
        if ($(this).hasClass('word')) {
            spellTumb.val(0);
        } else {
            spellTumb.val(1);
        }
    })
    // =============== end spelling

    // =============== audio API
    function spellFunctionality(selector, inputType, resetBtn) {
        let inputVal;
        let thisAudio;
        $(selector).click(function() {
                if($('.ck.ck-editor').length){
                    inputVal = $('.ck-content').text().replace(/\{[^}]+\}/g, '');
                    thisAudio = $('.ck.ck-content');

                }else{
                    inputVal = $('.audio-spell > input').val().trim().replace(/\s+/g, ' ');
                    thisAudio = $('.audio-spell > input');
                }
                if($('.spelling').hasClass('letter')) {
                    function byLetter(input) {
                        let parsed = input.split('').join('-');
                        return '(' + parsed.split('-').join(')-(') + ')';
                    }
                    inputVal = byLetter(inputVal)
                }

            $(selector).parent().append('<input class="sound" type="file" name="audio" accept="audio/mpeg3">')
            thisAudio.attr('readonly', true);
            thisAudio.attr('contenteditable', 'false');
            $(selector).html('<i class="spinner loading icon large"></i>');
            $('.spelling').addClass('disabled');

            var requestData = {
                prompt: inputVal,
                voice: 'Paid_Dude'
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
                    $(selector).off("click");
                    $(selector).removeClass(selector).addClass("spell-play");
                    $(selector).html('<i class="play icon large"></i>');

                    $(selector).click(function() {
                        if (audioElement.paused) {
                            audioElement.play();
                            $(selector).html('<i class="pause icon large"></i>');
                        } else {
                            audioElement.pause();
                            $(selector).html('<i class="play icon large"></i>');
                        }
                        audioElement.addEventListener('ended', function() {
                            $(selector).html('<i class="play icon large"></i>');
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
                    $(selector).off("click");
                    $(selector).removeClass("spell-play");
                    $(selector).html('<i class="cloud download icon large"></i>');
                    $(selector).siblings('input').remove();
                    // $(inputType).attr("src", "");
                    $('.task-audio-dropdown, .task-audio input, .button.spelling').removeClass("disabled").attr("disabled", false);
                    $(inputType).removeAttr('src');
                    thisAudio.attr('contenteditable', 'true')
                    thisAudio.attr('readonly', false);

                    spellFunctionality(selector, inputType, resetBtn);
                }
            }).modal("show");
        });
    }

    spellFunctionality('.spell-word', '.sound', '.del-file');

    // =============== audio API end

    // =============== START localization tumbler logic
    $('.loc-thumb button').on('click', function () {
        $(this).addClass('blue')
        $(this).siblings().removeClass('blue');
        if ($(this).hasClass('pair_en')) {
            $('#localised').val(1);
        } else {
            $('#localised').val(0);
        }
    })
    // =============== END localization tumbler logic

    // =============== rules
    $('.rules-thumb button').on('click', function(){
        $(this).addClass('blue');
        $(this).siblings().removeClass('blue');
        var capitals = $('#capital-rule'),
            signs = $('#sign-rule');

        $(this).each(function(){
            switch (true) {
                case $(this).hasClass('capital'):
                    capitals.val(1);
                    break;
                case $(this).hasClass('non-capital'):
                    capitals.val(0);
                    break;
                case $(this).hasClass('signs'):
                    signs.val(1);
                    break;
                case $(this).hasClass('non-signs'):
                    signs.val(0);
                    break;
            }
        })
    })
    // ===============

    // =============== dropdown restore to default button
    $('.restore-default').on('click', function () {
        $(this).siblings().dropdown('restore defaults');
    });
    // =============== games legend
    $('.legend-btn').on('click', function () {
        $('.tree').animate({
            'opacity': '0',
            'margin-left': "-25%",
        }, 750);
        $('#legend .exit').animate({
            'opacity': '0'
        })
        $('.legend').animate({
            'opacity': '1',
            'margin-right': '0'
        }, 750)
    });
    $('.legend-exit').on('click', function () {
        $('.tree').animate({
            'opacity': '1',
            'margin-left': "0",
        }, 750);
        $('#legend .exit').animate({
            'opacity': '1'
        })
        $('.legend').animate({
            'opacity': '0',
            'margin-right': "-25%"
        }, 750)
    })
</script>