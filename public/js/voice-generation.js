
setTimeout(function(){
    charVoice = $('.char_emotion .text .voice').text();
    console.log(charVoice)

}, 1000)
// ============= play generated
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
                    $('input.voice-sound').remove();
                    audioWordSpell = ''
                }
                check()
                $('.button.save').attr('disabled', false);

                // playGenerated(playBtn, audio, resetBtn)
            }
        }).modal("show");
    });
};
// =============== audio API
function spellFunctionality(spellType, inputType, resetBtn) {
    let inputVal;
    let thisAudio;
    $(spellType).click(function() {
        inputVal = $('.title textarea[name="text_en"]').val()
        thisAudio = $('.main-local textarea').attr('readonly', true);

        $(spellType).siblings('.spelling').append('<input class="voice-sound" type="file" name="voice" accept="audio/mpeg3">')

        $(spellType).html('<i class="spinner loading icon large"></i>');
        $('.spelling').addClass('disabled');
        $('.spelling').siblings('button').css('pointer-events', 'none')

        var requestData = {
            prompt: inputVal,
            voice: charVoice
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
                $('.button.save, .button.discard').attr('disabled', false)
                $('.spelling').siblings('button').css('pointer-events', 'auto')
                $('form').addClass('contentChanged');

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

spellFunctionality('.spell-word', '.voice-sound', '.spell.word .del-file');

// =============== audio API end