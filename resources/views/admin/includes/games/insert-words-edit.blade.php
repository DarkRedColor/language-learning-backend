<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2 border-bottom">
    <h5>Insert words</h5>
</div>
<div class="form-group">
    {{ Form::label('description_id', 'Description') }}
    {{ Form::select('description_id',$variables[3], $variables[0]['description_id'], ['class' => 'form-control ' . ($errors->has('description_id') ? ' is-invalid' : '')]) }}
    @if ($errors->has('description_id'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('description_id') }}
        </div>
    @endif
</div>
<div class="form-group">
    {{ Form::label('theory_id', 'Theory') }}
    {{ Form::select('theory_id',$variables[1], $variables[0]['theory_id'], ['class' => 'form-control ' . ($errors->has('theory_id') ? ' is-invalid' : '')]) }}
    @if ($errors->has('theory_id'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('theory_id') }}
        </div>
    @endif
</div>

<div class="form-group">
    {{ Form::label('mission_en' ,'Question') }}
    {{ Form::text('mission_en', $variables[0]['mission_en'], ['class' => 'form-control ' . ($errors->has('mission_en') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
    @if ($errors->has('mission_en'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('mission_en') }}
        </div>
    @endif
</div>

<div class="form-group mb-2" id="append" style="display: flex;flex-direction: column">
    @for($i = 0; $i<count($variables[0]['answer']);$i++)
        <div class="appended mt-3" style="display: flex;flex-direction: column">
            <div class="_append mt-3" style="display: flex;flex-direction: row">
                {{ Form::label('clue' ,'Clue') }}
                <input class="form-control " autocomplete="off" style="margin-right: 15px;"
                       name="link_words[]" type="text"
                       value="{{$variables[0]['link_words'][$i]}}">
                {{ Form::label('answer' ,'Answer') }}
                <input class="form-control " autocomplete="off" required="" style="margin-right: 15px;"
                       name="answer[][]" type="text" value="{{$variables[0]['answer'][$i][0]}}">
            </div>

        </div>
    @endfor
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
                    '<input class="form-control " autocomplete="off" style="margin-right: 15px;" name="link_words['+childs+'][]" type="text">' +
                    '{{ Form::label('answer' ,'Answer') }}' +
                    '<input class="form-control " autocomplete="off" required="" style="margin-right: 15px;" name="answer['+childs+'][]" type="text">' +
                    '</div>' +
                    '</div>'
                );
            }
        }
    })


</script>




