<div class="modal__">
    @foreach($episode as $aux)
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
        @foreach($descriptionLang as $langDescription)
            @if($langName === 'description_en')
                <div class="form-group">
                    {{ Form::label($langDescription,Str::headline($langDescription)) }}
                    {{ Form::text($langDescription, $aux->$langDescription, ['class' => 'form-control ' . ($errors->has($langDescription) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                    @if ($errors->has($langDescription))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first($langDescription) }}
                        </div>
                    @endif
                </div>
            @else
                <div class="collapse" id="modalCollapse">
                    <div class="form-group" id="modalCollapse">
                        {{ Form::label($langDescription,Str::headline($langDescription)) }}
                        {{ Form::text($langDescription, $aux->$langDescription, ['class' => 'form-control ' . ($errors->has($langDescription) ? ' is-invalid' : ''), 'autocomplete'=>'off', ]) }}
                        @if ($errors->has($langDescription))
                            <div class="invalid-feedback">
                                {{ $errors->getBag('default')->first($langDescription) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
            <div class="form-group">
                {{ Form::text('file', '', ['class' => 'form-control ' . ($errors->has('file') ? ' is-invalid' : ''),'autocomplete' => 'off', 'hidden'=>'true', 'id' => 'file']) }}
                @if ($errors->has('file'))
                    <div class="invalid-feedback">
                        {{ $errors->getBag('default')->first('file') }}
                    </div>
                @endif
            </div>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#modalCollapse" aria-expanded="false" aria-controls="modalCollapse"
                style="margin-top: 15px">
            Other languages
        </button>
    @endforeach
        <div class="col-8">
            <div class="col-md-12">
                <h2>Image</h2>
                <div class="file-drop-area" style="border: #e53e3e solid 0.5px">
                    <button class="choose-file-button btn btn-outline-secondary">
                        Choose files
                    </button>
                    <span
                            class="file-message">
                    or drag and drop files here
                </span>
                    <input class="file-input" type="file" id="files">
                </div>
            </div>
        </div>
</div>
