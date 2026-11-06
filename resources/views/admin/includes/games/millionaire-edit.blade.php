<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2 border-bottom">
    <h5>Millionaire</h5>
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
@foreach($variables[2] as $item)
    @if($item->lcid === 'en')
        <div class="form-group">
            {{ Form::label('mission_en' ,'Question ('.$item->lcid.')') }}
            {{ Form::text('mission_en', $variables[0]['mission_en'], ['class' => 'form-control ' . ($errors->has('mission_en') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
            @if ($errors->has('mission_en'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('mission_en') }}
                </div>
            @endif

        </div>
    @else
        <div class="collapse" id="modalCollapse">
            <div class="form-group">
                {{ Form::label('mission_'.$item->lcid ,'Question ('.$item->lcid.')') }}
                {{ Form::text('mission_'.$item->lcid, $variables[0]['mission_'.$item->lcid ], ['class' => 'form-control ' . ($errors->has('mission_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off']) }}
                @if ($errors->has('mission_'.$item->lcid))
                    <div class="invalid-feedback">
                        {{ $errors->getBag('default')->first('mission_'.$item->lcid) }}
                    </div>
                @endif

            </div>
        </div>
    @endif
@endforeach
<button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
        data-bs-target="#modalCollapse" aria-expanded="false" aria-controls="modalCollapse"
        style="margin-top: 15px">
    Other languages
</button>
<div class="form-group">
    {{ Form::label('answer' ,'Answer') }}
    {{ Form::text('answer[][]', $variables[0]['answer'][0][0], ['class' => 'form-control ' . ($errors->has('answer') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
    @if ($errors->has('answer'))
        <div class="invalid-feedback">
            {{ $errors->getBag('default')->first('answer') }}
        </div>
    @endif
</div>
<div class="form-group">
    {{ Form::label('wrong' ,'Wrong words') }}
    <div style="display: flex">
        {{ Form::text('wrong[]', $variables[0]['wrong'][0], ['class' => 'form-control ' . ($errors->has('wrong') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('wrong'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('wrong') }}
            </div>
        @endif
        {{ Form::text('wrong[]', $variables[0]['wrong'][1], ['class' => 'form-control ' . ($errors->has('wrong') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('wrong'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('wrong') }}
            </div>
        @endif


        {{ Form::text('wrong[]', $variables[0]['wrong'][2], ['class' => 'form-control ' . ($errors->has('wrong') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('wrong'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('wrong') }}
            </div>
        @endif
    </div>
</div>
<div class="form-group mb-2" id="append" style="display: flex;flex-direction: column">
    @if($variables[0]['name_en'] === 'text')

    @else

        <div class="appended mt-3" style="display: flex;flex-direction: column">
            <div class="_append mt-3" style="display: flex;flex-direction: row">
                {{ Form::label('clue' ,'Clue') }}
                <input class="form-control " autocomplete="off" style="margin-right: 15px;" name="link_words"
                       type="text" value="{{$variables[0]['link_words']}}">
            </div>
        </div>

    @endif
</div>
<script>
    $('#mission_en').on('keyup', function () {
        let text = this.value;
        let components = 0;
        let childs = $('#append > div').length;
        for (let i = 0; i < text.length - 1; i++) {
            let str = text[i] + text[i + 1];
            if (str === '__') {
                components = 1;
                i++;
            }
        }
        if (components < childs) {
            $('#append').children().last().remove();
        }
        if (childs < components) {
            $('#append').append(
                '<div class="appended mt-3" style="display: flex;flex-direction: column">' +
                '<div class="_append mt-3" style="display: flex;flex-direction: row">' +
                '{{ Form::label('clue' ,'Clue') }}' +
                '<input class="form-control " autocomplete="off" style="margin-right: 15px;" name="link_words" type="text">' +
                '</div>' +
                '</div>'
            );
        }
    })
</script>



