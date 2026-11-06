<div class="modal__">
    @foreach($season as $aux)
        @foreach($nameLang as $langName)
            @if($langName === 'name_en')
                <div class="form-group">
                    {{ Form::label($langName,$displayNameLang[$loop->index]) }}
                    {{ Form::text($langName, $aux->$langName, ['class' => 'form-control ' . ($errors->has($langName) ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required'=> 'true']) }}
                    @if ($errors->has($langName))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first($langName) }}
                        </div>
                    @endif
                </div>
            @else
                <p>

                </p>
                <div class="collapse" id="modalCollapse">
                    <div class="form-group">
                        {{ Form::label($langName, $displayNameLang[$loop->index]) }}
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
        {{--        @foreach($descriptionLang as $langDescription)--}}
        {{--            <div class="form-group">--}}
        {{--                {{ Form::label($langDescription, Str::headline($langDescription)) }}--}}
        {{--                {{ Form::text($langDescription, $aux->$langDescription, ['class' => 'form-control ' . ($errors->has($langDescription) ? ' is-invalid' : ''), 'autocomplete'=>'off']) }}--}}
        {{--                @if ($errors->has('description_en'))--}}
        {{--                    <div class="invalid-feedback">--}}
        {{--                        {{ $errors->getBag('default')->first($langDescription) }}--}}
        {{--                    </div>--}}
        {{--                @endif--}}
        {{--            </div>--}}
        {{--        @endforeach--}}
    @endforeach
</div>