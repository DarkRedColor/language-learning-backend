<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2 border-bottom">
    <h5>Type words</h5>
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

@foreach ($variables[2] as $item )
    @if($item->lcid === 'en')
        <div class="form-group">
            {{ Form::label('mission_en' ,'Question en') }}
            {{ Form::text('mission_en', null, ['class' => 'form-control ' . ($errors->has('mission_en') ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
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
                {{ Form::text('mission_'.$item->lcid , null, ['class' => 'form-control ' . ($errors->has('mission_'.$item->lcid ) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                @if ($errors->has('mission_'.$item->lcid ))
                    <div class="invalid-feedback">
                        {{ $errors->getBag('default')->first('mission_'.$item->lcid ) }}
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

<div class="appended mt-3" style="display: flex;flex-direction: column">
    <div class="_append mt-3" style="display: flex;flex-direction: row">
        {{ Form::label('answer' ,'Answer') }}
        <input class="form-control " autocomplete="off" required="" style="margin-right: 15px;" name="answer[]"
               type="text">
        <a class="btn btn-outline-success" id="answer-add" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square"
                 viewBox="0 0 16 16">
                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
        </a>
    </div>
</div>
<div class="form-group mb-2" id="append" style="display: flex;flex-direction: column">

</div>

<script>
    $(document).on('click', "#answer-add", function () {
        $(this).closest('div.appended').append(
            '<div class="_append mt-3" style="display: flex;flex-direction: row">' +
            '{{ Form::label('answer' ,'Answer') }}' +
            '<input class="form-control " autocomplete="off" required="" style="margin-right: 15px;" name="answer[]" type="text">' +
            '<a class="btn btn-outline-danger" id="answer-remove" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-apple" viewBox="0 0 16 16">' +
            '<path d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-.108-.003-.483-.095-1.254.116-.508.139-1.653.589-1.968.607-.316.018-1.256-.522-2.267-.665-.647-.125-1.333.131-1.824.328-.49.196-1.422.754-2.074 2.237-.652 1.482-.311 3.83-.067 4.56.244.729.625 1.924 1.273 2.796.576.984 1.34 1.667 1.659 1.899.319.232 1.219.386 1.843.067.502-.308 1.408-.485 1.766-.472.357.013 1.061.154 1.782.539.571.197 1.111.115 1.652-.105.541-.221 1.324-1.059 2.238-2.758.347-.79.505-1.217.473-1.282z"/>' +
            '<path d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-.108-.003-.483-.095-1.254.116-.508.139-1.653.589-1.968.607-.316.018-1.256-.522-2.267-.665-.647-.125-1.333.131-1.824.328-.49.196-1.422.754-2.074 2.237-.652 1.482-.311 3.83-.067 4.56.244.729.625 1.924 1.273 2.796.576.984 1.34 1.667 1.659 1.899.319.232 1.219.386 1.843.067.502-.308 1.408-.485 1.766-.472.357.013 1.061.154 1.782.539.571.197 1.111.115 1.652-.105.541-.221 1.324-1.059 2.238-2.758.347-.79.505-1.217.473-1.282z"/>' +
            '</svg></a>' +
            '</div>'
        )
    })

    $(document).on('click', "#answer-remove", function () {
        $(this).closest('._append').remove();
    })


</script>




