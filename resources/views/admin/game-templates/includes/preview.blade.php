<div class="edit">
    <h1>Preview</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>{{$item->name}}</h3>
    </div>
    <div class="edit-area grid games">
        @if($item->task == "space")
            <div class="info-section">
                <div class="circular ui icon button info-btn">
                    <i class="info big circle icon"></i>
                </div>
                <div class="ui info-popup bottom left transition hidden">
                    <ol>
                        <li>For Space - <strong>Italic</strong></li>
                        <li>For Clue - <span class="clue">{clue}</span></li>
                        <li>For 2 answers - /</li>
                    </ol>
                    <p><strong><u>Example: </u></strong><br>I <strong>am/‘m</strong><span class="clue">{to be}</span>
                        a teacher.</p>
                </div>
            </div>
        @endif
        @if($item->answer == "erudite")
            <div class="info-section">
                <div class="circular ui icon button info-btn">
                    <i class="info big circle icon"></i>
                </div>
                <div class="ui info-popup bottom left transition hidden">
                    <ol>
                        <li>If you want to delete - click on <b><u>backspace</u></b></li>
                        <li>For Clue - click on words</li>
                    </ol>
                </div>
            </div>
        @endif
        <form action="" class="ui form game-audio">
            <div class="ui two column grid">
                <div class="column">
                    @switch($item->task)
                        @case("text")
                            @if(in_array($item->answer,["erudite","sentence"]))
                                @include('admin.game-templates.html-inputs.input-text-localisation',$languages)
                            @elseif(in_array($item->answer,["words"]))
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @else
                                @include('admin.game-templates.html-inputs.input-text',[$allWordsOfTheory,$selectedWord])
                            @endif
                            @break
                        @case("image")
                            @include('admin.game-templates.html-inputs.input-image')
                            @break
                        @case("audio")
                            @if(in_array($item->answer,["erudite","sentence"]))
                                @include('admin.game-templates.html-inputs.input-audio-localisation',$languages)
                            @elseif(in_array($item->answer,["words"]))
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @else
                                @include('admin.game-templates.html-inputs.input-audio')
                            @endif

                            @break
                        @case("space")
                            @if($item->additional_task == 'text')
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @elseif($item->additional_task == 'audio')
                                @include('admin.game-templates.html-inputs.input-space-audio')
                            @else
                                @include('admin.game-templates.html-inputs.input-space-text')
                            @endif
                            @break
                        @case("video")
                            <div class="edit-area adventure soon">
                                <h1>Video Coming soon</h1>
                            </div>
                            @break
                        @case("symbols")
                            @include('admin.game-templates.html-inputs.input-text',[$allWordsOfTheory,$selectedWord])
                            @break
                        @default
                            {{--                @include('admin.coming-soon')--}}
                            @break
                    @endswitch
                    @switch($item->additional_task)
                        @case("text")
                            @if(!in_array($item->task,['link_words','space','symbols','list']))
                                @include('admin.game-templates.html-inputs.input-text',[$allWordsOfTheory,$selectedWord])
                            @endif
                            @break
                        @case("image")
                            @include('admin.game-templates.html-inputs.input-image')
                            @break
                        @case(null)
                            @break
                        @default
                            @if(!in_array($item->task,['link_words','space','symbols','list']))
                                @include('admin.game-templates.html-inputs.input-audio')
                            @endif
                    @endswitch
                    @switch($item->answer)
                        @case("type")
                            @if($item->task == "space")
                                @include('admin.game-templates.html-inputs.input-correct-answer-space')
                            @elseif($item->task == "text")
                                @include('admin.game-templates.html-inputs.input-localized-correct-answer',$languages)
                            @else
                                @include('admin.game-templates.html-inputs.input-type')
                            @endif
                            @break
                        @case("true_false")
                            <div class="ui field ">
                                <h5>Answers</h5>
                                @if($item->task == "space")
                                    @include('admin.game-templates.html-inputs.input-correct-answer-space')
                                @else
                                    @include('admin.game-templates.html-inputs.input-type')
                                @endif
                            </div>
                            @break
                        @case("remove")
                            @include('admin.game-templates.html-inputs.input-remove')
                            @break
                        @case("erudite")
                            <div class="ui field">
                                <label>Answers</label>
                                @include('admin.game-templates.html-inputs.input-erudite')
                            </div>
                            @break
                        @case("words")
                            <div class="ui field ">
                                <h5>Answers</h5>
                                @include('admin.game-templates.html-inputs.input-correct-answer-space')
                            </div>
                            <div class="ui field ">
                                <h5>Wrong Answers</h5>
                                @include('admin.game-templates.html-inputs.input-wrong-answer-tags')
                            </div>
                            @break
                        @case("sentence")
                            <div class="ui field ">
                                <h5>Answers</h5>
                                @include('admin.game-templates.html-inputs.input-erudite')
                            </div>
                            <div class="ui field ">
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
                            <div class="ui field ">
                                <h5>Answers</h5>
                                @if($item->task == "space")
                                    @include('admin.game-templates.html-inputs.input-correct-answer-space')
                                @else
                                    @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                @endif
                                @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                                @include('admin.game-templates.html-inputs.input-wrong-answer',[$allWordsOfTheory,$selectedWord])
                            </div>
                            @break
                        @case("4")
                            @if($item->task == "link_words")
                                <div class="ui field ">
                                    @if($item->additional_task == 'audio')
                                        <h5 class="type-audio">Example: Audio</h5>
                                    @else
                                        <h5 class="type-text">Example: Text</h5>
                                    @endif
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                </div>
                            @else
                                <div class="ui field ">
                                    <h5>Answers</h5>
                                    <div class="two fields">
                                        @if($item->task == "space")
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
                            @if($item->task == "link_words")
                                <div class="ui field ">
                                    @if($item->additional_task == 'audio')
                                        <h5 class="type-audio">Example: Audio</h5>
                                    @else
                                        <h5 class="type-text">Example: Text</h5>
                                    @endif
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>

                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>
                                    <div class="two fields">
                                        @include('admin.game-templates.html-inputs.input-correct-answer',[$allWordsOfTheory,$selectedWord])
                                        @include('admin.game-templates.html-inputs.input-correct-answer-link')
                                    </div>
                                </div>
                            @endif
                            @break
                        @case("matched_not")
                            @if($item->additional_task == 'text')
                                @include('admin.game-templates.html-inputs.input-text-matched')
                            @elseif($item->additional_task == 'audio')
                                @include('admin.game-templates.html-inputs.input-audio-matched')
                            @endif
                            @break
                        @case("list")
                            @if($item->additional_task == 'text')
                                @include('admin.game-templates.html-inputs.input-text-list')
                            @elseif($item->additional_task == 'audio')
                                @include('admin.game-templates.html-inputs.input-audio-list')
                            @endif
                            @break
                        @default
                            <div class="edit-area adventure soon">
                                <h1>Coming soon</h1>
                            </div>
                            @break
                    @endswitch
                </div>
                <div class="column">
                    <p class="filling-type">Filling</p>
                    <div class="ui buttons thumb">
                        <button type="button" class="ui button manual">Manual</button>
                        <div class="or"></div>
                        <button type="button" class="ui button selective">Selective</button>
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons ">
                <button type="button" href="#" class="grey ui button cancel">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    // --------- character counter ----------
    var inputGame = $('.games form input[type="text"], form textarea').not('#correct input, #incorrect input, input[class="hid"]')
    inputGame.parent().append('<span class="num"></span>')

    // onload show character count
    inputGame.each(function(){
        var wordsLength = $(this).val().length
        $(this).siblings('.num').text(wordsLength)
    })

    // on keyup/click update character count
    inputGame.on('keyup click', function(){
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
    var ckeditorGame = $('form .game-editor').parent().append('<span class="num"></span>')
    $('.game-editor').siblings('.num').text($('form .ck-editor p').text().length)
    $('form .ck-content').on('keyup click', function(){
        var wordsLength = $(this).text().length
        $('.num').text(wordsLength)
    })
    // ------------------------------------
    $('.cancel').on('click', function () {
        location.reload();
    })
    $(document).ready(function () {
        var type = $('.game_creation h5');
        switch (type.attr('class')) {
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


        // info tooltip
        $('.info-btn').popup({
            popup: $('.info-popup'),
            on: 'click'
        });

        // add to correct answer
        $('.ck-content').on('DOMSubtreeModified', function () {
            if ($('.ck-content strong').length) {
                $('.button.save').attr('disabled', false);
            } else {
                $('.button.save').attr('disabled', true);
            }

            var strongText = $('.ck-content').find('i');
            $('.correct-fields').children().remove();
            strongText.each(function () {
                $('.correct-fields').append('<p class="correct-answer">' + $(this).text() + '</p>')
            })
        })

        // ================== answer tags inputs
        var counter = $('.word > span').length;
        $(".word input").on({
            focusout: function () {
                var txt = this.value.replace(/[^a-z0-9+-.!?@;:'$ ]/ig, '');
                if (counter < 7 || this.value === ' ') {
                    if (txt) $("<span/>", {text: txt, insertBefore: this});
                    this.value = "";
                    counter++;
                }
                if ($('#correct span').last().text() === ' ') {
                    counter = 0;
                }
            },
            keyup: function (ev) {
                if (/(13)/.test(ev.which)) $(this).focusout();
                if (ev.keyCode === 8 && this.value.length <= 0) {
                    $(this).parent().children('span').last().remove()
                    counter -= 1;
                }

            }
        });
        // ================== toggle clue in answers
        $('#correct').on('click', 'span', function () {
            $(this).toggleClass('clue');
        });

        $('.button.save').on('click', function () {
            var correct = [];
            var clue = [];
            var incorrect = [];
            var correctAnswer = [];
            var ckContent = $('.ck-content');

            $('#correct span').each(function () {
                correct.push($(this).text());
            });
            $('#correct span.clue, #correct span.remove-span').each(function () {
                clue.push($(this).index())
            });
            $('#incorrect span').each(function () {
                incorrect.push($(this).text());
            });

            ckContent.find('strong').each(function () {
                correctAnswer.push($(this).text())
            })
            var taskAnswer = []
            ckContent.children().each(function () {
                taskAnswer.push($(this).html().replace(/<strong>(.*?)<\/strong>/g, "@"))
            })

            var taskClue = []
            ckContent.children().each(function () {
                taskClue.push($(this).text().match(/{(.*?)}/g))
            })
        })
    })
    $(".add-localization").on('click', function () {
        $(".localized:first-child").clone().appendTo(".localized-answer").after(function () {
            $(this).find('textarea').val('')
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
        $('.answer-del').on('click', function () {
            if ($(this).parents('.localized').find('textarea').val().length > 0) {
                $('.delete-modal').modal({
                    onApprove: function () {
                        $(this).parents('.localized').remove()
                    }
                }).modal('show');
            } else {
                $(this).parents('.localized').remove()
            }
        })
    });

    // ============ tumbler logic start
    var selective = $('.selective-way');
    var manual = $('.manual-way');
    var inputs = $('.manual-way input:not([type=hidden]), .selective-way input:not([type=hidden])');

    // ============ tumbler logic start
    var selective = $('.selective-way'),
        manual = $('.manual-way'),
        inputs = $('.manual-way input:not([type=hidden]), .selective-way input:not([type=hidden])'),
        textArea = $('.manual-way textarea');

    $(function () {
        selective.hide();
        $('.button.manual').addClass('blue')
    })

    inputs.on('keyup change', function () {
        $('.form').addClass('contentChanged')
    })
    textArea.on('keyup change', function () {
        $('.form').addClass('contentChanged')
    })
    $('.search.dropdown').on('change', function () {
        $('.form').addClass('contentChanged')
    })
    var manualBtn = $('.manual'),
        selectiveBtn = $('.selective'),
        currentButton = manualBtn;

    manualBtn.click(function () {
        switchButton(manualBtn);
    });

    selectiveBtn.click(function () {
        switchButton(selectiveBtn);
    });

    function switchButton(newButton) {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    inputs.val('');
                    textArea.val('');
                    $('.task-audio-dropdown').dropdown('restore defaults');
                    $(selective).toggle();
                    $(manual).toggle();
                    $('.form').removeClass('contentChanged');
                    currentButton.removeClass('blue');
                    newButton.addClass('blue');
                    currentButton = newButton;
                }
            }).modal('show');
        } else {
            currentButton.removeClass('blue');
            newButton.addClass('blue');
            currentButton = newButton;
            $(selective).toggle();
            $(manual).toggle();
        }
    }

    // =============== tumbler logic end

    // =============== dropdown restore to default button
    $('.restore-default').on('click', function () {
        $(this).siblings().dropdown('restore defaults');
    });
    // =============== dropdown restore

    // =============== true/false
    $('.spelling').off().click(function () {
        $(this).text(function (i, text) {
            return text === "by letter" ? "by word" : "by letter";
        })
        $(this).toggleClass('word letter');
    })
    // =============== end true/false

</script>