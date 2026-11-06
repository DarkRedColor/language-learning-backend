@extends('admin.layouts.admin-dashboard')

@section('content')

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-5 border-bottom">
        <h1 class="h2">Game Type</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a class="btn btn-sm btn-outline-success" href="{{ route('game-type-index') }}">Back to game types</a>
            </div>
        </div>
    </div>
    <div class="row">
        {!! Form::open(['action' => ['App\Http\Controllers\GameTypeController@store'],'method' => 'POST',
        'class' => 'col-4']) !!}

        <div class="form-group">
            {{ Form::label('name' ,'Name') }}
            {{ Form::text('name', null, ['class' => 'form-control ' . ($errors->has('name') ? ' is-invalid' : ''), 'autocomplete'=>'off', 'required']) }}
            @if ($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('name') }}
                </div>
            @endif
        </div>
        <div class="form-group">
            {{ Form::label('status', 'Status') }}
            {{ Form::select('status',['don`t showed', 'showed'], null, ['class' => 'form-control ' . ($errors->has('status') ? ' is-invalid' : '')]) }}
            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('status') }}
                </div>
            @endif
        </div>
{{--        <div class="form-group">--}}
{{--            <input type="checkbox" id="mission_" name="mission_" value="1">--}}
{{--            <label for="mission_"> Mission </label><br>--}}

{{--            <input type="checkbox" id="othe_games_id" name="othe_games_id" value="1">--}}
{{--            <label for="othe_games_id"> Other Game </label><br>--}}

{{--            <input type="checkbox" id="vehicle3" name="vehicle3" value="Boat">--}}
{{--            <label for="vehicle3"> I have a boat</label><br>--}}
{{--        </div>--}}




        {{ Form::button('Create', ['type' => 'submit', 'class' => 'btn btn-primary mt-2',  'style'=>"float: right"]) }}

        {!! Form::close() !!}

    </div>

@endsection
