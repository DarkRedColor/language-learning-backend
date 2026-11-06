<h1>Create arcade game</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div class="info">
            <p>
                Type of the game
            </p>
        </div>
    </div>

    <div class="ui selection dropdown" style="background: var(--intro);">
        <input type="hidden" name="type" value="type-fast" id="arcade-type">
        <div class="default text">Type</div>
        <div class="menu">
            <div class="item" data-value="type-fast">Type Fast</div>
            <div class="item" data-value="find-words">Find Words</div>
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>
<div class="edit-area">
    <form action="{{route('arcade-games-store')}}" class="ui form" id="arcade_game_create_form"
          method="post">
        @csrf
        <input type="text" hidden name="type" id="" value="0">

        <div class="field eight wide">
            <label>Background Start Game:</label>
            <div class="ui selection search dropdown">
                <input type="hidden" name="arcade_start_id" value="" id="arcade_start_id">
                <div class="text default">Select background for Start Game</div>
                <i class="dropdown icon"></i>
                <div class="menu">
                    @foreach($bg as $item)
                        <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="field eight wide">
            <label>Background For Game:</label>
            <div class="ui selection search dropdown">
                <input type="hidden" name="arcade_bg_id" value="" id="arcade_bg_id">
                <div class="text default">Select background for Game</div>
                <i class="dropdown icon"></i>
                <div class="menu">
                    @foreach($bg as $item)
                        <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="field sixteen wide">
            <label>Link Words Pack:</label>
            <input name="link_words" value="" id="link_words_pack" hidden/>
            <div class="ui fluid multiple search selection dropdown link_words">
                <i class="dropdown icon"></i>
                <div class="default text">Select words pack</div>
                <div class="menu">
                    @foreach($themes as $item)
                        <div class="item" data-value="{{$item['id']}}">{{$item['name_en']}}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" disabled id="arcade_game_save_button">SAVE</button>
            <a href="#" class="negative ui button" id="arcade_game_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready( () => {
        $('.ui.dropdown').dropdown({
            onChange: function(){
                // ====== change form state
                $('.form').addClass('contentChanged')
                // ====== enable save / discard btn
                $('.save.button').attr('disabled', false)
            },
            clearable: true
        });
    })
    $('.save[type="submit"]').one('click', function () {
        // ===== save type of the game
        var arcadeType = $('#arcade-type').val();
        var type = $('input[name="type"]');

        switch (arcadeType) {
            case "type-fast":
                type.val("0");
                break;
            case "find-words":
                type.val("1");
                break;
            default:
                type.val("Select an option");
                break;
        }
        // ==== save Link Words Pack
        let arr = [];
        $('.link_words > a').each(function() {
            arr.push($(this).attr('data-value'))
        })
        $('#link_words_pack').val(JSON.stringify(arr));
        $('.form').submit();
    })
    $('#arcade_game_cancel_button').on('click', function () {
        var clearForm = $('#arcade_storage_edit_create_zone');
        if ($('.form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    clearForm.empty();
                }
            }).modal('show');
        } else {
            clearForm.empty();
        }
    })
</script>