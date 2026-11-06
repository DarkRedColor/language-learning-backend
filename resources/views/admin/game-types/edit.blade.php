@extends('admin.layouts.admin-dashboard')

@section('content')

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-5 border-bottom">
        <h1 class="h2">Game types</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a class="btn btn-sm btn-outline-success" href="{{ route('game-type-index') }}">Back to game types</a>
            </div>
        </div>
    </div>
    <div class="row">
        {!! Form::open(['action' => ['App\Http\Controllers\GameTypeController@update', $gameType->id],
        'class' => 'col-4']) !!}


        <div class="form-group">
            {{ Form::label('name' ,'Name') }}
            {{ Form::text('name', $gameType->name, ['class' => 'form-control ' . ($errors->has('name') ? ' is-invalid' : ''), 'autocomplete'=>'off','required']) }}
            @if ($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('name') }}
                </div>
            @endif
        </div>


        <div class="form-group">
            {{ Form::label('status', 'Status') }}
            {{ Form::select('status',['don`t showed', 'showed'], $gameType->status, ['class' => 'form-control ' . ($errors->has('status') ? ' is-invalid' : '')]) }}
            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('status') }}
                </div>
            @endif
        </div>


        {{ Form::button('Update', ['type' => 'submit', 'class' => 'btn btn-primary mt-3', 'style'=>'float: right']) }}

        {!! Form::close() !!}
    </div>
@endsection
