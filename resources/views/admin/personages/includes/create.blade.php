<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit" id="character_exit_button"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>New Character</h3>
    </div>
    <div class="edit-area">
        <form action="{{route('personage-store')}}" class="ui form" method="post" id="character_create_form">
            @csrf
            <div class="eight wide field">
                <label>Personage voice</label>
                <input type="text" placeholder="voice name" name="voice" value="">
            </div>
            <div class="ui field accordion char-name">
            <div class="field">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Name</label>
                            <input type="text" placeholder="Character Name - English" name="name_en"
                                   id="name_en_field_of_character_create_form" autocomplete="false">
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                <div class="field">
                                    <input type="text" name="name_{{$item->lcid}}"
                                           placeholder="Character Name - {{$item->display_name}}">
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button" id="character_save_button" disabled>SAVE</button>
                <a href="#" class="negative ui button" id="character_cancel_button">CANCEL</a>
            </div>
        </form>
    </div>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>

<script>
    $("#name_en_field_of_character_create_form").on('keyup', function () {
        if ($(this).val() === "") {
            $('#character_save_button').attr('disabled', true);
        }
    })
    $('#character_cancel_button').on('click', function () {
        if ($(".form").hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    location.reload();
                }
            }).modal('show');
        } else {
            location.reload();
        }
    })
    // $("#character_exit_button").on('click',function (){
    //     $("#name_en_field_of_character_create_form").removeClass('contentChanged');
    //     location.reload();
    // })
</script>
