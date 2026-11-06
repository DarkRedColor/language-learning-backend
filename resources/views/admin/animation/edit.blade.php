@extends('admin.layouts.admin-dashboard')

@section('content')
    <style>

        .file-drop-area {
            position: relative;
            display: flex;
            align-items: center;
            width: 450px;
            max-width: 100%;
            padding: 25px;
            border: 1px dashed rgba(255, 255, 255, 0.4);
            border-radius: 3px;
            transition: 0.2s
        }

        .choose-file-button {
            flex-shrink: 0;
            background-color: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            padding: 8px 15px;
            margin-right: 10px;
            font-size: 12px;
            text-transform: uppercase
        }

        .file-message {
            font-size: small;
            font-weight: 300;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            cursor: pointer;
            opacity: 0
        }
    </style>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-5 border-bottom">
        <h1 class="h2">Background</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a class="btn btn-sm btn-outline-success" href="{{ route('background-index') }}">Back to
                    backgrounds</a>
            </div>
        </div>
    </div>
    <div style="display:flex;flex-direction: row">
        {!! Form::open(['action' => ['App\Http\Controllers\BackgroundController@update',$bg->id],'method' => 'POST',
        'class' => 'col-4']) !!}
        <div class="form-group">
            {{ Form::label('name', 'Name') }}
            {{ Form::text('name', $bg['name'], ['class' => 'form-control ' . ($errors->has('name') ? ' is-invalid' : ''),'autocomplete' => 'off', 'required']) }}
            @if ($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->getBag('default')->first('name') }}
                </div>
            @endif
        </div>
        <div class="form-group mb-3">
            {{ Form::text('file', '', ['class' => 'form-control ' . ($errors->has('file') ? ' is-invalid' : ''),'autocomplete' => 'off', 'hidden'=>'true', 'id' => 'file',]) }}
            {{--            @if ($errors->has('file'))--}}
            {{--                <div class="invalid-feedback">--}}
            {{--                    {{ $errors->getBag('default')->first('file') }}--}}
            {{--                </div>--}}
            {{--            @endif--}}
        </div>
        {{ Form::button('Create', ['type' => 'submit', 'class' => 'btn btn-primary mt-2',  'style'=>"float: right; margin-top:50px"]) }}

        {!! Form::close() !!}

        <div style="display: flex;flex-direction: column; margin-left: 20px">
            <h2>Background</h2>
            <div class="file-drop-area" style="border: #e53e3e solid 0.5px" id="img-div">
                <button class="choose-file-button btn btn-outline-secondary">
                    Choose files
                </button>
                <span
                        class="file-message">
                    or drag and drop files here
                </span>
                <input class="file-input" type="file" id="files" value="">
            </div>
            <img id="blah" src="{{$bg['background'] . "_2x"}}" alt="your image"
                 style="height: 300px; width: auto; margin-top: 10px"/>
        </div>
    </div>
{{--    <script>--}}
{{--        @if ($errors->has('file'))--}}
{{--        $('#img-div').ready(function () {--}}
{{--            $('#img-div').css("background-color", "#e53e3e");--}}
{{--        })--}}
{{--        @endif--}}
{{--    </script>--}}
    <script>
        $(document).on('change', '.file-input', function () {
            var img = document.querySelector('input[type="file"]').files[0];
            let imgInput = $('#blah')
            imgInput.attr('src', URL.createObjectURL(img));
            imgInput.removeAttr('hidden');
            $('#img-div').css("background-color", "");
            $('.file-message').text(img.name)
            var reader = new FileReader();
            reader.onloadend = function () {
                $("#file").val(reader.result);
            }
            reader.readAsDataURL(img);
        });
    </script>

@endsection
