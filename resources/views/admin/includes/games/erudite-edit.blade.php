<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2 border-bottom">
    <h5>Erudite</h5>
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
@foreach ($variables[2] as $item )
    @if($item->lcid === 'en')
        <div class="form-group">
            {{ Form::label('mission_en' ,'Question '. $item->lcid ) }}
            {{ Form::text('mission_en', $variables[0]['mission_en'], ['class' => 'form-control ' . ($errors->has('mission_en') ? ' is-invalid' : ''), 'autocomplete'=>'off','required']) }}
            @if ($errors->has('mission_en'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('mission_en') }}
                </div>
            @endif
        </div>
    @else
        <div class="collapse" id="modalCollapse">
            <div class="form-group">
                {{ Form::label('mission_'.$item->lcid ,'Question '. $item->lcid) }}
                {{ Form::text('mission_'.$item->lcid, $variables[0]['mission_' . $item->lcid], ['class' => 'form-control ' . ($errors->has('mission_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
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
    {{ Form::label('answer' ,'Word') }}
    {{ Form::text('answer[][]', $variables[0]['answer'][0][0], ['class' => 'form-control ' . ($errors->has('answer.0') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required','id' => 'answer']) }}
    <div class="" id="invalide-feedback" hidden>
        <p style="color: red">The Word must not be greater than 9 characters.</p>
    </div>
</div>


@if($variables[0]['name_en'] == null)
    <div class="form-group mt-3 mb-3">
        {{ Form::label('erudit_tip' ,'Tip') }}
        {{ Form::checkbox('erudit_tip', 0 ) }}
        @if ($errors->has('erudit_tip'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('erudit_tip') }}
            </div>
        @endif
    </div>
@else
    <div class="form-group mt-3 mb-3">
        {{ Form::label('erudit_tip' ,'Tip') }}
        {{ Form::checkbox('erudit_tip', 0, true ) }}
        @if ($errors->has('erudit_tip'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('erudit_tip') }}
            </div>
        @endif
    </div>
@endif

<script>
    $('#answer').on('keyup', function () {
        if (this.value.length > 9) {
            $('#invalide-feedback').removeAttr('hidden');
        } else {
            $('#invalide-feedback').attr('hidden', 'true');
        }
    })
</script>



