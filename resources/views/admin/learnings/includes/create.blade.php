<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Create new learning</h3>
    </div>
    <div class="edit-area grid">
        <form action="{{route('learning-store')}}" class="ui form learning add-words" method="post">
            @csrf
            <div class="two fields">
                <div class="field">
                    <label>Name Learning</label>
                    <input placeholder="learning_name" type="text" name="name">
                </div>
                <div class="field">
                    <label>Games count</label>
                    <input placeholder="games count number" type="number" min="0" max="15" name="game_count">
                </div>
            </div>
            <div class="field">
                <label>Link Theory</label>
                <input name="theory" value="" id="theory" hidden/>
                <select class="ui fluid search dropdown theory" multiple="">
                    <option value="">Theory</option>
                    @foreach($theories as $item)
                        <option value="{{$item->id}}">@if(isset($item->name) and !empty($item->name)){{$item->name}}@else{{$item->description_en}}@endif</option>
                    @endforeach
                    <i class="chevron down icon"></i>
                </select>
            </div>
            <div class="field">
                <label>Link Practice</label>
                <input name="practice" value="" id="practice" hidden/>
                <select class="ui fluid search dropdown practice" multiple="">
                    <option value="">Practice</option>
                    @foreach($practices as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                    <i class="chevron down icon"></i>
                </select>
            </div>
            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save" disabled>SAVE</button>
                <button type="button" class="grey ui button create-cancel">CANCEL</button>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('js/main.js')}}"></script>
<script>
    $(document).ready(function () {
        // check if theory
        var numInput = $('form input[name="game_count"]');
        numInput.attr('disabled', true).val('0');
        $('.dropdown.theory').dropdown({
            onChange: function() {
                if ($(this).dropdown('get value').length === 0) {
                    numInput.attr('disabled', true).val('0')
                } else {
                    numInput.attr('disabled', false)
                }
            }
        });

        $('.save').one('click', function () {
            let arr = [];
            $('.search.dropdown.practice > a').each(function () {
                arr.push($(this).attr('data-value'))
            })
            $('#practice').val(JSON.stringify(arr));
            arr = [];
            $('.search.dropdown.theory > a').each(function () {
                arr.push($(this).attr('data-value'))
            })
            $('#theory').val(JSON.stringify(arr));
            $('.form').submit();
        })
        // type just numbers
        $(function () {
            $("input[type='number']").on('input', function () {
                var value = $(this).val();
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
                if ((value !== '') && (value.indexOf('.') === -1)) {
                    $(this).val(Math.max(Math.min(value, 150), -150));
                }
            });
        });
        // add class && show button
        $('.form').on('change keyup click', function () {
            $('.form').addClass('contentChanged')
            var inputText = $('.form input[type="text"]:not([type="hidden"])').val().length;
            var inputNumber = $('.form input[type="number"]').val().length;
            var dropPractice = $('.form .practice a').length;
            var dropTheory = $('.form .theory a').length;
            if (inputText > 0 && dropPractice > 0 ) {
                $('.form .button.save').attr('disabled', false)
            } else {
                $('.form .button.save').attr('disabled', true)
            }
        });
    })

    $('.create-cancel').on('click', function(){
        if($('.form').hasClass('contentChanged')){
            $('.discard-modal').modal({
                onApprove: function(){
                    location.reload();
                }
            }).modal('show')
        }else{
            location.reload();
        }
    })


</script>