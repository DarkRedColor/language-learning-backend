<h1>New language</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<form class="ui form" action="/admin/main/menu/language/store/modal" method="post">
    @csrf
    <div class="field lang-choice">
        <div class="ui selection dropdown">
            <input type="hidden" name="id" id="id_of_existing_language">
            <i class="chevron down icon"></i>
            <div class="default text">Choose Language</div>
            <div class="menu transition hidden" tabindex="-1">
                @foreach($languages as $item)
                    <div class="item" data-value="{{$item->id}}">{{$item->display_name}}</div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="buttons action-buttons">
        <button type="submit" class="positive ui button save" id="main_menu_language_save_button" disabled>SAVE</button>
        <a href="#" class="negative ui button" id="cancel_adding_new_language">CANCEL</a>
    </div>
</form>

<script>
    $("#cancel_adding_new_language").on('click',function (){
        location.reload();
    })
    $(".exit").on('click',function (){
        location.reload();
    })

    $('#id_of_existing_language').on('change', function (){
        $("#main_menu_language_save_button").removeAttr('disabled');
    })

</script>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>