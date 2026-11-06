<div class="ui field">
    <label>Mission</label>
    <div class="symbols-list">
        @if(isset($game))
            @if(json_decode($game->link_words) <> null)
                @foreach(json_decode($game->link_words) as  $item)
                    <div class="ui right labeled input symbol">
                        @if($item[0] == "F")
                            <button type="button" class="ui button label symbol-boolean negative">False</button>
                        @else
                            <button type="button" class="ui button label symbol-boolean positive">True</button>
                        @endif
                        <input type="text" placeholder="New mission" name="link_answers[]" value="{{$item[1]}}">
                        <button type="button" class="ui button label negative symbol-del">
                            <i class="icon close"></i>
                        </button>
                    </div>
                @endforeach
            @endif
        @else
            <div class="ui right labeled input symbol">

                <button type="button" class="ui button label symbol-boolean negative">False</button>
                <input type="text" placeholder="New mission" name="link_answers[]">
                <button type="button" class="ui button label negative symbol-del">
                    <i class="icon close"></i>
                </button>

            </div>
        @endif
    </div>
    <a class="add ui basic button add-symbol"><i class="plus icon"></i>Add one more mission</a>
</div>

<script>
    $(function(){
        $('.symbol-del').on('click', function () {
            var thisSymbol = $(this);
            if ($(this).parents('.symbol').find('input').val().length > 0) {
                $('.delete-modal').modal({
                    onApprove: function () {
                        thisSymbol.parents('.symbol').remove()
                    }
                }).modal('show');
            } else {
                thisSymbol.parents('.symbol').remove()
            }
        })
        $('.symbol-boolean').off().on('click', function () {
            $(this).toggleClass('positive negative');
            $(this).text(function (i, text) {
                return text === "True" ? "False" : "True";
            })
        })
    })
    $(".add-symbol").on('click', function () {
        $(".symbol:first-child").clone().appendTo(".symbols-list").after(function () {
            $(this).find('input').val('')
        });
        $('.symbol-del').on('click', function () {
            var thisSymbol = $(this);
            if ($(this).parents('.symbol').find('input').val().length > 0) {
                $('.delete-modal').modal({
                    onApprove: function () {
                        thisSymbol.parents('.symbol').remove()
                    }
                }).modal('show');
            } else {
                thisSymbol.parents('.symbol').remove()
            }
        })
        // true/false
        $('.symbol-boolean').off().on('click', function () {
            $(this).toggleClass('positive negative');
            $(this).text(function (i, text) {
                return text === "True" ? "False" : "True";
            })
        })
    });
</script>