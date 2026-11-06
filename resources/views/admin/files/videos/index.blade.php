@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div id="edit" class="fourteen wide column">
        <div id="bg">
            <h1 class="left">Video</h1>
            <div class="ui search">
                <div class="ui icon input">
                    <input id="filterInput" class="prompt" type="text" placeholder="Type something">
                    <i class="search icon"></i>
                </div>
            </div>
            <div class="edit-area video" id="filterList">
                <!--            Video preview            -->
                <form action="{{route('file-video-store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <label id="dropped" class="add-bg">
                        <input id="input" type="file" name="video" onchange="inputOnChange(this)"
                               accept="video/mp4"/>
                        <i class="plus icon big">
                            <video id="video"></video>
                        </i>
                        <button type="button" class="play-btn dis-img">
                            <i class="play icon"></i>
                        </button>
                        <button type="submit" class="upload-btn">upload</button>
                    </label>
                </form>
                <!--           End Video preview             -->
                @foreach($videos as $item)
                    <div class="bg-cards">
                        <button class="del-bg" id="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>
                        <i class="film icon big"></i>
                        <video class="video-card hid" src="{{Storage::disk('gcs')->url("video/$item->name")}}"></video>
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
        <h5 id="name_of_video_file"></h5>
        <form action="" method="post" enctype="multipart/form-data" id="update_video_form">
            @csrf
            <label id="dropped-edit" class="add-bg">
                <label for="edit-input" class="dis-img change-video">
                    <i class="sync alternate icon"></i>
                </label>
                <input id="edit-input" type="file" name="video" onchange="editInputOnChange(this)" accept="video/mp4"/>
                <i class="plus icon big">
                    <video id="edit-video" controls="true" src=""></video>
                </i>
            </label>
            <div class="buttons action-buttons">
                <button type="button" id="video_file_update" class="positive ui button save">SAVE</button>
                <a href="#" class="grey ui button dis">CANCEL</a>
            </div>
        </form>
    </div>
    <script type="application/javascript" src="{{asset('js/search_result.js')}}"></script>
    <script>
        window.onload = () => {
            // get fileName
            $('.bg-cards img').each(function () {
                return $(this).siblings('.bg-name').children('p')
                    .append($(this).attr('src').split('/').pop())
            })
        };

        input.onchange = function () {
            let video = document.getElementById('video');
            video.src = URL.createObjectURL(this.files[0]);
            video.onend = function () {
                URL.revokeObjectURL(this.src);
            }
            let editVideo = document.getElementById('edit-video');
            editVideo.src = URL.createObjectURL(this.files[0]);
            editVideo.onend = function () {
                URL.revokeObjectURL(this.src);
            }
        }

        function inputOnChange(item) {
            document.getElementById('video').src = window.URL.createObjectURL(item.files[0]);
        }

        function editInputOnChange(editItem) {
            document.getElementById('edit-video').src = window.URL.createObjectURL(editItem.files[0]);
        }

        // sidebar
        $('.ui.sidebar').sidebar({
            dimPage: true,
            transition: 'scale down'
        });
        $('.bg-cards .film.icon, .button.dis').click(() => {
            $('.ui.sidebar').sidebar('toggle');

        });

        $('.bg-cards').on('click', function () {
            let source = $(this).children('.video-card').attr('src');
            let name = $(this).children('.bg-name').text();
            let id = $(this).children('.del-bg').attr('id');
            $('#edit-video').attr('src', source);
            $('#name_of_video_file').empty().append(name);
            let updateForm = $('#update_video_form');
            updateForm.attr('action', '/admin/files/video/update/' + id);
        });

        // play - pause
        $('.play-btn').click(function () {
            $(this).toggleClass("stopped playing");
            if ($(this).is('.stopped')) {
                $('#video')[0].pause();
                $(this).children('i').addClass('play')
            } else {
                $('#video')[0].play();
                $(this).children('i').removeClass('play').addClass('pause')
            }
        }).addClass("stopped")
        $('.play-btn-edit').click(function () {
            $(this).toggleClass("stopped playing");
            if ($(this).is('.stopped')) {
                $('#edit-video')[0].pause();
                $(this).children('i').addClass('play')
            } else {
                $('#edit-video')[0].play();
                $(this).children('i').removeClass('play').addClass('pause')
            }
        }).addClass("stopped")


        $('#video_file_update').on('click', function () {
            $('#update_video_form').submit();
        })
        $('#dropped').on('change', function () {
            $('#dropped').removeClass('add-bg').addClass('vis')
        })

        $('.del-bg').on('click', function () {
            let del = $(this).attr('id');
            $('.delete-modal').modal({
                onApprove: function (){
                    $.ajax({
                        type: "post",
                        url: "video/delete/" + del,
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