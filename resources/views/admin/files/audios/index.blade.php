@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div id="edit" class="fourteen wide column">
        <div id="bg">
            <h1 class="left">Audio</h1>
            <div class="ui search">
                <div class="ui icon input">
                    <input id="filterInput" class="prompt" type="text" placeholder="Type something">
                    <i class="search icon"></i>
                </div>
            </div>
            <div class="edit-area audio" id="filterList">
                <!--            Sound preview            -->
                <form action="{{route('file-audio-store')}}" method="post" enctype="multipart/form-data" id="audio_form">
                    @csrf
                    <input type="hidden" value="{{$host}}" name="host">
                    <label id="dropped" class="add-bg">
                        <input id="input" type="file" name="audio" onchange="inputOnChange(this)"
                               accept="audio/mpeg3"/>
                        <i class="plus icon big">
                            <audio id="sound"></audio>
                        </i>
                        <button type="button" class="play-btn dis-img">
                            <i class="play icon"></i>
                        </button>
                        <button type="button" id="submit_audio_button" class="upload-btn">upload</button>
                    </label>
                </form>
                <!--           End Sound preview             -->    
                @foreach($audios as $item)
                    <div class="bg-cards">
                        <button class="del-bg" id="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>
                        <i class="music icon big"></i>
                        <audio class="audio-card" src="{{Storage::disk('gcs')->url("audio/$item->name")}}"></audio>
                        <span class="bg-name">
                            <p>{{$item->name}}</p>
                        </span>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <div class="ui right vertical sidebar">
        <h2>Edit</h2>
        <h5 id="name_of_audio_file">WA_01_U01_Lesson_1_2_Exercises_2_and_3.mp3</h5>
        <input type="hidden" id="id_of_selected_audio" value="66">
        <form action="/admin/files/audio/update/" method="post" enctype="multipart/form-data" id="update_audio_form">
            @csrf
            <input type="hidden" value="{{$host}}" name="host">
            <input type="hidden" name="name" id="updated_file_name">
            <label id="dropped-edit" class="add-bg">
                <input id="edit-input" type="file" name="audio" accept="audio/mpeg3" onchange="editInputOnChange(this)" />
                <i class="plus icon big">
                    <audio id="edit-sound" controls="true" src=""></audio>
                </i>
            </label>
            <div class="buttons action-buttons">
                <button type="button" id="update_audio_button" class="positive ui button save" disabled >SAVE</button>
                <button type="button" href="#" class="grey ui button dis">CANCEL</button>
            </div>
        </form>
    </div>
    <script type="application/javascript" src="{{asset('js/search_result.js')}}"></script>
    <script>
        input.onchange = function () {
            let sound = document.getElementById('sound');
            sound.src = URL.createObjectURL(this.files[0]);
            sound.onend = function () {
                URL.revokeObjectURL(this.src);
            }
            let editSound = document.getElementById('edit-sound');
            editSound.src = URL.createObjectURL(this.files[0]);
            editSound.onend = function () {
                URL.revokeObjectURL(this.src);
            }
        }

        function inputOnChange(item) {
            document.getElementById('sound').src = window.URL.createObjectURL(item.files[0]);
            // get fileName
        }

        $('#submit_audio_button').on('click', function () {
            $('#audio_form').submit();
        })
        $('#update_audio_button').on('click', function () {
            $('#updated_file_name').val($('#name_of_audio_file').text());
            $('#update_audio_form').submit();
        })

        function editInputOnChange(editItem) {
            document.getElementById('edit-sound').src = window.URL.createObjectURL(editItem.files[0]);
            $('.sidebar .button.save').attr('disabled', false)
        }

        // append scr from card to preview
        $('.bg-cards').on('click', function() {
            let source = $(this).children('.audio-card').attr('src');
            let name = $(this).children('.bg-name').text();
            let id = $(this).children('.del-bg').attr('id');
            $('#edit-sound').attr('src', source);
            $('#name_of_audio_file').empty().append(name);
            $('#id_of_selected_audio').attr('value', id);
            let updateForm = $('#update_audio_form');
            updateForm.attr('action',updateForm.attr('action') + id);
        })
        // sidebar
        $('.ui.sidebar').sidebar({
            dimPage: true,
            transition: 'scale down'
        });
        $('.bg-cards .music.icon, .button.dis').click(() => {
            $('.ui.sidebar').sidebar('toggle');
        });

        // play - pause

        $('.play-btn').click(function () {
            $(this).toggleClass("stopped playing");
            if ($(this).is('.stopped')) {
                $('#sound')[0].pause();
                $(this).children('i').addClass('play')
            } else {
                $('#sound')[0].play();
                $(this).children('i').removeClass('play').addClass('pause')
            }
        }).addClass("stopped")
        $('.play-btn-edit').click(function () {
            $(this).toggleClass("stopped playing");
            if ($(this).is('.stopped')) {
                $('#edit-sound')[0].pause();
                $(this).children('i').addClass('play')
            } else {
                $('#edit-sound')[0].play();
                $(this).children('i').removeClass('play').addClass('pause')
            }
        }).addClass("stopped")


        $('#dropped').on('change', function () {
            $('#dropped').removeClass('add-bg').addClass('vis')
        })

        $('.del-bg').on('click', function () {
            let del = $(this).attr('id');
            $('.delete-modal').modal({
                onApprove: function (){
                    $.ajax({
                        type: "post",
                        url: "audio/delete/" + del,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload()
                        }
                    })
                }
            }).modal('show')
        })

    </script>
@endsection