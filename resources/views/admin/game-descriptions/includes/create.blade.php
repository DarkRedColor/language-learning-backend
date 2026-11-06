<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h5>Game description</h5>
    </div>
    <div class="edit-area">
        <form action="{{route('game-description-store')}}" class="ui form" method="post">
            @csrf
            <div class="ui field accordion">
                <div class="title inputs">
                    <div class="field main-local">
                        <label>Localization</label>
                        <textarea class="textarea" rows="2" name="description_en"
                                  id="name_en_field_of_text_create_form"></textarea>
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
            <div class="buttons action-buttons">
                <button type="submit" href="#" class="positive ui button save" disabled>SAVE</button>
                <button type="button" href="#" class="grey ui button discard">CANCEL</button>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('js/main.js')}}"></script>
<script>
    $(document).ready(function () {
        // add class && show button
        $('.form .main-local textarea').on('keyup', function () {
            if ($(this).val() !== '') {
                $('.form').addClass('contentChanged')
                $('.button.save, .button.discard').attr('disabled', false)
            } else {
                $('.button.save').attr('disabled', true)
            }
        })
    })
    $('.button.discard').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                closable: false,
                onApprove: function () {
                    location.reload();
                }
            }).modal('show')
        } else {
            location.reload();
        }
    })

</script>