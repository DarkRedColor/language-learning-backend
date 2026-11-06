<button class="ui icon basic button api-exit"><i class="close icon"></i></button>
<h1>API Oxford</h1>

@if($oxfordApiRes)
    <form class="ui form">
        <div class="field">
            <label>Word</label>
            <p onclick="copyClip(this);">{{$oxfordApiRes['word']}}</p>
        </div>
        <div class="field">
            <label>Transcription</label>
            <p onclick="copyClip(this);">{{$oxfordApiRes['phoneticSpelling']}}</p>
        </div>
        <div class="ui field">

            @foreach($oxfordApiRes['defAndExam'] as $item)
                <div class="ui horizontal divider" style="color: red">main_definition_{{$loop->index + 1}}</div>
                @foreach($item as $values)
                    <div class="ui horizontal divider">definition_{{$loop->index + 1}}</div>
                    <div class="field">
                        <label>Definition</label>
                        <p onclick="copyClip(this);">{{$values['definition']}}</p>
                    </div>
                    @if(isset($values['examples']))
                        @foreach($values['examples'] as $examples)
                            <div class="field">
                                <label>Examples</label>
                                <p onclick="copyClip(this);">{{$examples}}</p>
                            </div>
                        @endforeach
                    @endif

                @endforeach
            @endforeach

        </div>
    </form>
@else
    <p>Word not found</p>
@endif
<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    function copyClip(element) {
        const $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).html()).select();
        document.execCommand("copy");
        $temp.remove();
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
    $(document.body).addClass('pusher');

    $('.oxford p, .oxford textarea').popup({
        on: 'click',
        position: 'bottom center',
        content: 'Link is copied',
        onVisible: function () {
            setTimeout(
                function () {
                    $('.oxford p, .oxford textarea').popup('hide');
                }, 500);
        }
    })

</script>

