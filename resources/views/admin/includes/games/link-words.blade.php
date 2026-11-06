<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2 border-bottom">
    <h5>Link words</h5>
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
    {{ Form::label('link_words' ,'Words (en)') }}
    <div style="display: flex">
        {{ Form::text('link_words[]', null, ['class' => 'form-control ' . ($errors->has('link_words') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('link_words'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('link_words') }}
            </div>
        @endif
        {{ Form::text('link_words[]', null, ['class' => 'form-control ' . ($errors->has('link_words') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('wrong'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('wrong') }}
            </div>
        @endif


        {{ Form::text('link_words[]', null, ['class' => 'form-control ' . ($errors->has('link_words') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('link_words'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('link_words') }}
            </div>
        @endif
        {{ Form::text('link_words[]', null, ['class' => 'form-control ' . ($errors->has('link_words') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
        @if ($errors->has('link_words'))
            <div class="invalid-feedback">
                {{ $errors->getBag('default')->first('link_words') }}
            </div>
        @endif
    </div>
</div>
@foreach ($variables[2] as $item )
    @if($item->lcid === 'en')
        @continue
    @else
        @if($item->lcid === 'ru')
            <div class="form-group">
                {{ Form::label('linked_words' ,'Words('.$item->lcid.')') }}
                <div style="display: flex">
                    {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                    @if ($errors->has('linked_words_'.$item->lcid))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                        </div>
                    @endif
                    {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                    @if ($errors->has('linked_words_'.$item->lcid))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                        </div>
                    @endif


                    {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                    @if ($errors->has('linked_words_'.$item->lcid))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                        </div>
                    @endif
                    {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                    @if ($errors->has('linked_words_'.$item->lcid))
                        <div class="invalid-feedback">
                            {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="collapse" id="modalCollapse">

                <div class="form-group">
                    {{ Form::label('linked_words' ,'Words('.$item->lcid.')') }}
                    <div style="display: flex">
                        {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                        @if ($errors->has('linked_words_'.$item->lcid))
                            <div class="invalid-feedback">
                                {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                            </div>
                        @endif
                        {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                        @if ($errors->has('linked_words_'.$item->lcid))
                            <div class="invalid-feedback">
                                {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                            </div>
                        @endif


                        {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                        @if ($errors->has('linked_words_'.$item->lcid))
                            <div class="invalid-feedback">
                                {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                            </div>
                        @endif
                        {{ Form::text('linked_words_'.$item->lcid.'[]', null, ['class' => 'form-control ' . ($errors->has('linked_words_'.$item->lcid) ? ' is-invalid' : ''), 'autocomplete'=>'off',]) }}
                        @if ($errors->has('linked_words_'.$item->lcid))
                            <div class="invalid-feedback">
                                {{ $errors->getBag('default')->first('linked_words_'.$item->lcid) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
@endforeach
<button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
        data-bs-target="#modalCollapse" aria-expanded="false" aria-controls="modalCollapse"
        style="margin-top: 15px">
    Other languages
</button>




