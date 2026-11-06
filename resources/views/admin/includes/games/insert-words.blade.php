<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2 border-bottom">
    <h5>Insert words</h5>
</div>
<div class="form-group">
    {{ Form::label('description_id', 'Description') }}
    {{ Form::select('description_id',$variables[0], null, ['class' => 'form-control ' . ($errors->has('description_id') ? ' is-invalid' : '')]) }}
    @if ($errors->has('description_id'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('description_id') }}
        </div>
    @endif
</div>
<div class="form-group">
    {{ Form::label('theory_id', 'Theory') }}
    {{ Form::select('theory_id',$variables[1], null, ['class' => 'form-control ' . ($errors->has('theory_id') ? ' is-invalid' : '')]) }}
    @if ($errors->has('theory_id'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('theory_id') }}
        </div>
    @endif
</div>

<div class="form-group">
    {{ Form::label('mission_en' ,'Question') }}
    {{ Form::text('mission_en', null, ['class' => 'form-control ' . ($errors->has('mission_en') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
    @if ($errors->has('mission_en'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('mission_en') }}
        </div>
    @endif
</div>

{{--<div class="form-group mt-3 mb-4">--}}
{{--    {{ Form::label('final words' ,'Final question') }}--}}
{{--    <p id="in-app"></p>--}}

{{--    <a href="#" id="final-generate"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">--}}
{{--            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>--}}
{{--            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>--}}
{{--        </svg></a>--}}

{{--</div>--}}

<div class="form-group mb-2" id="append" style="display: flex;flex-direction: column">

</div>

<script>
    $('#mission_en').on('keyup', function () {
        let text = this.value;
        let components = 0;
        let nStr = text;
        let childs = $('#append > div').length;
        for (let i = 0; i < text.length - 1; i++) {
            let str = text[i] + text[i + 1];
            if (str === '__') {
                components++;
                i++;
            }
        }

        $('#in-app').text(nStr.toString());

        if (components < childs) {
            $('#append').children().last().remove();
        }

        if (childs < components) {
            for (childs; childs < components; childs++) {
                $('#append').append(
                    '<div class="appended mt-3" style="display: flex;flex-direction: column">' +
                    '<div class="_append mt-3" style="display: flex;flex-direction: row">' +
                    '{{ Form::label('clue' ,'Clue') }}' +
                    '<input class="form-control " autocomplete="off" style="margin-right: 15px;" name="link_words[]" type="text">' +
                    '{{ Form::label('answer' ,'Answer') }}' +
                    '<input class="form-control " autocomplete="off" required="" style="margin-right: 15px;" name="answer['+childs+'][]" type="text">' +
                    '</div>' +
                    '</div>'
                );
            }
        }
    })


</script>




