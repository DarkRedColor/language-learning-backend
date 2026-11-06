<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Dialogue name</h3>
    </div>
    <div class="edit-area">
        <form action="{{route('dialogue-store')}}" class="ui form" method="post" id="dialogue_create_form">
            @csrf
            <input type="hidden" value="3" name="type">
            <div class="two fields">
                <div class="field">
                    <label>Dialogue name</label>
                    <input type="text" name="name_en" placeholder="Story name" autocomplete="off" id="name_en_field_of_dialogue_create_form">
                </div>
                <div class="ui field accordion">
                    <div class="ui title inputs">
                        <div class="field main-local">
                            <label>Summary</label>
                            <textarea class="textarea" id="summary_en_field_of_dialogue_create_form" name="description_en"
                                      rows="2"></textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($languages as $item)
                            @if($item->lcid <> 'en')
                                <div class="field">
                                    <textarea class="textarea" rows="2" name="description_{{$item->lcid}}"></textarea>
                                    <div class="ui label lang-iso">{{$item->lcid}}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" id="dialogue_save_button" disabled>SAVE</button>
                <button type="button" class="negative ui button" id="dialogue_cancel_button">CANCEL</button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>

<script>
    $('#dialogue_cancel_button').on('click',function (){
        if ($('form').hasClass('contentChanged')){
            $(".basic-error").modal({
                onApprove:function (){
                    location.reload()
                }
            }).modal('show');
        } else {
            location.reload()
        }
    })
$('#name_en_field_of_dialogue_create_form').on('keyup',function () {
    if ($(this).val() === ''){
        $('#dialogue_save_button').attr('disabled',true);
    }
})

</script>

