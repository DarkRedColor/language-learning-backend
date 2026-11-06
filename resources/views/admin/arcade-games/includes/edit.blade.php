<h1>Edit</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="search-area" style="background: var(--type-fast);">
    <h3>
        Type Fast
    </h3>
</div>
<div class="edit-area">
    <form action="{{route('arcade-games-update',$arcade->id)}}" class="ui form" id="arcade_game_create_form"
          method="post">
        @csrf

        <input type="text" hidden name="type" id="" value="{{$arcade->type}}">

        <div class="field eight wide">
            <label>Background Start Game:</label>
            <div class="ui selection search dropdown">
                <input type="hidden" name="arcade_start_id" value="{{$arcade->start_background_id}}" id="arcade_start_id">
                @if(isset($arcade->start_background_id) && $bg->where('id',$arcade->start_background_id)->first() !== null )
                    <div class="text">{{$bg->where('id',$arcade->start_background_id)->first()->name}}</div>
                @else
                    <div class="default text">Background</div>
                @endif
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
                <input type="hidden" name="arcade_bg_id" value="{{$arcade->game_background_id}}" id="arcade_bg_id">
                @if(isset($arcade->game_background_id) && $bg->where('id',$arcade->game_background_id)->first() !== null )
                    <div class="text">{{$bg->where('id',$arcade->game_background_id)->first()->name}}</div>
                @else
                    <div class="default text">Background</div>
                @endif
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
                @foreach($arcade->arcadeThemes as $item)
                    <a class="ui label transition visible" data-value="{{$item->id}}"
                       style="display: inline-block !important;">{{$item->name_en}}<i class="delete icon"></i></a>
                @endforeach
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
            <button type="button" class="grey ui button" disabled id="discard_arcade_game" data-value="{{$arcade->id}}" >discard</button>
            <button class="negative ui button delete" type="button">delete game</button>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready( () => {
        $('.ui.dropdown').dropdown({
            onChange: function(){
                // ====== change form state
                $('#arcade_game_create_form').addClass('contentChanged')
                // ====== enable save / discard btn
                $('.save.button, #discard_arcade_game').attr('disabled', false)
            },
            clearable: true
        });
    })

    $('#discard_arcade_game').on('click', function () {
        const editAria = $('#arcade_storage_edit_create_zone')
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "get",
                        url: "/admin/arcade-games/edit/" + "{{$arcade->id}}",
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            editAria.empty();
                            editAria.append(res);
                            $('.form').removeClass("contentChanged");
                        },
                    });
                }
            }).modal('show')
        } else {
            $.ajax({
                type: "get",
                url: "/admin/arcade-games/edit/" + "{{$arcade->id}}",
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    editAria.empty();
                    editAria.append(res);
                },
            });
        }
    })

    $('.save[type="submit"]').one('click', function () {
        // ==== save Link Words Pack
        let arr = [];
        $('.link_words > a').each(function() {
            arr.push($(this).attr('data-value'))
        })
        $('#link_words_pack').val(JSON.stringify(arr));
        $('.form').submit();
    })

    // ===== Change Type
    var arcadeType = $('input[name="type"]').val();
    var switchType = $('.search-area');

    switch (arcadeType) {
        case "0":
            switchType.css('background', 'var(--type-fast)').after(function() {
                $(this).find('h3').text('Type Fast')
            })
            break;
        case "1":
            switchType.css('background', 'var(--find-words)').after(function() {
                $(this).find('h3').text('Find Words')
            })
            break;
        default:
            type.val("Select an option");
            break;
    }
    $('.button.delete').on('click', function () {
        $('.delete-modal').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/arcade-games/delete/{{$arcade->id}}",
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