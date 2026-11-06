<div class="modal__">
    @foreach($chapter as $aux)

        @foreach($nameLang as $langName)
            @if($langName === 'name_en')
                <div class="form-group">
                    {{ Form::label($langName,Str::headline($langName)) }}
                    {{ Form::text($langName, $aux->$langName, ['class' => 'form-control ' . ($errors->has($langName) ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required'=> 'true']) }}
                    @if ($errors->has($langName))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first($langName) }}
                        </div>
                    @endif
                </div>

            @else
                <div class="collapse" id="modalCollapse">
                    <div class="form-group">
                        {{ Form::label($langName, Str::headline($langName)) }}
                        {{ Form::text($langName, $aux->$langName, ['class' => 'form-control ' . ($errors->has($langName) ? ' is-invalid' : ''), 'autocomplete'=>'off']) }}
                        @if ($errors->has($langName))
                            <div class="invalid-feedback">
                                {{ $errors->getBag('default')->first($langName) }}
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
@endforeach
