@extends('admin.layouts.admin-dashboard')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-5 border-bottom">
        <h1 class="h2">User</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a class="btn btn-sm btn-outline-success" href="{{ route('user-index') }}">Back to Users</a>
            </div>
        </div>
    </div>
    <div class="row">
        {!! Form::open(['action' => ['App\Http\Controllers\UserController@update',$user[0]->id],
        'class' => 'col-4']) !!}
        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            {{ Form::text('email', $user[0]->email,['class' => 'form-control ' . ($errors->has('role') ? ' is-invalid' : ''), 'autocomplete'=>'off','disabled']) }}
            @if ($errors->has('email'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('email') }}
                </div>
            @endif
        </div>
        <div class="form-group">
            {{ Form::label('role', 'Role') }}
            {{ Form::select('role', $roles, $user[0]->roles->first()->id, ['class' => 'form-control ' . ($errors->has('role') ? ' is-invalid' : ''), 'autocomplete'=>'off']) }}
            @if ($errors->has('role'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('role') }}
                </div>
            @endif
        </div>

        {{ Form::button('Update', ['type' => 'submit', 'class' => 'btn btn-primary mt-3', 'style'=>'float: right']) }}

        {!! Form::close() !!}
    </div>
@endsection
