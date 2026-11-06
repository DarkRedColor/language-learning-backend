<div class="edit">
    <h1>Add Words</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>New template</h3>
    </div>
    <form class="ui form add-words" action="{{route('practice-pages-store',$practiceId)}}" method="post"
          id="practice_game_templates_create">
        @csrf
        <div class="edit-area">
            <input name="array_of_game_templates" type="text" value="" hidden id="array_of_words"/>

            <select class="ui fluid search dropdown" multiple="">
                <option value="">Game Templates</option>
                @foreach($gameTemplates as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
                <i class="chevron down icon"></i>
            </select>
            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save" disabled>SAVE</button>
                <a href="#" class="grey ui button cancel-words">CANCEL</a>
            </div>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $('.save').on('click', function () {
        let arr = [];
        $('.search.dropdown >a').each(function () {
            arr.push($(this).attr('data-value'))
        })
        $('#array_of_words').val(JSON.stringify(arr));
        $('#practice_game_templates_create').submit();
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

    $(document).ready(function () {
        $('.search.dropdown').children().on('change', function () {

            if ($('.search.dropdown >a').length > 0) {
                $('.positive.save').attr('disabled', false)
                $('.form').addClass('contentChanged')
            } else {
                $('.positive.save').attr('disabled', true)

            }
        })
    })
</script>