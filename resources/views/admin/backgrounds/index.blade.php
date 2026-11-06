@extends('admin.layouts.admin-intro-dashboard')

@section('content')

    <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
    <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
    <input type="text" hidden name="name" id="background_file_name"/>
    <div id="edit" class="fourteen wide column">
        <div id="bg">
            <h1 class="left">Backgrounds</h1>
            <div class="ui search">
                <div class="ui icon input">
                    <input id="filterInput" class="prompt" type="text" placeholder="Type something">
                    <i class="search icon"></i>
                </div>
            </div>
            <form action="{{route('background-store')}}" method="post" enctype="multipart/form-data" class="form_store"
                  id="background_store">
                @csrf
                <input hidden id="test">
                <div class="mt-4">
                    <input type="file" name="image[]" accept="image/png" multiple data-max-file-size="10MB"
                           data-max-files="5"
                           class="new-file-upload">
                    <button type="button" class="upload-btn img-store">upload</button>
                </div>
            </form>

            <div class="edit-area" id="filterList">

                @foreach($backgrounds as $item)
                    <div class="bg-cards">
                        <a class="share-bg" onclick="copyClip(this);">
                            <i class="linkify icon"></i>
                        </a>
                        <button type="button" class="del-bg @if(!Auth::user()->hasRole(['admin','qa','project','developer']))lena
                @endif" id="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>

                        <img src="{{Storage::disk('gcs')->url("backgrounds/$item->name"."_S.webp")}}" class="background_images"
                             data-value="{{$item->id}}">
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
        <h5 id="background_edit_name"></h5>
        <form action="/admin/background/update/" method="post" id="background_update" enctype="multipart/form-data" class="form_update">
            @csrf
            <input type="file" name="image[]" accept="image/png" multiple data-max-file-size="10MB"
                   data-max-files="1"
                   class="img-update-input">

            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save img-update" id="" data-value="" >UPDATE</button>
                <a href="#" class="ui button grey" id="background_cancel_edit_form">CANCEL</a>
            </div>
        </form>


    </div>
    <!-- Modal wait -->
    <div id="wait" class="ui basic modal">
        <div class="ui icon header">
            <h3>Please wait... </h3>
        </div>
        <div class="content">
            <canvas id="canvas" width="1250" height="1080"></canvas>
            {{--          <embed src="https://rive.app/s/oEAXaD2ea0ac8MQebzb2jg/embed" >--}}
        </div>
    </div>
    <script src={{asset("js/search_result.js")}} ></script>
    <script src="https://unpkg.com/@rive-app/canvas"></script>

    <script>
        function copyClip(elementClicked) {
            let $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(elementClicked).siblings('.background_images').attr('src')).select();
            document.execCommand("copy");
            $temp.remove();
        }

        // wait loading animation
        const r = new rive.Rive({
            src: '{{asset("css/langley_corp.riv")}}',
            canvas: document.getElementById('canvas'),
            autoplay: false,
            artboard: 'Langley_contacts',
            animations: 'stand_by',
            layout: new rive.Layout({
                fit: 'contain',
                alignment: 'topRight'
            }),
            useOffscreenRenderer: true
        });
        // END loading animation

        $('.dis-img').on('click', function () {
            $('.previewImg, .preview-img').attr('src', '')
            let dropped = $('#dropped');
            dropped.removeClass('vis');
            dropped.addClass('add-bg');
            $('#dropped-edit').removeClass('vis').addClass('edit-bg');
            $('#dropped .plus').removeClass('bg-error');
        })


        $('.upload-btn').on('click', function () {
            $("#wait").modal('show');
            r.play("stand_by");
        })

        $(document).ready(function () {
            $(document.body).addClass('pusher');

            $('.share-bg').popup({
                on: 'click',
                position: 'top center',
                content: 'Link is copied',
                onVisible: function () {
                    setTimeout(
                        function () {
                            $('.share-bg').popup('hide');
                        }, 500);
                }
            })
        })

        // update files sidebar
        $(".background_images").one('click', function () {
            let id = $(this).attr('data-value');
            $('#background_update').attr('action', '/admin/background/update/' + id)
            const inputElement = document.querySelector('input[class="img-update-input"]');
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileValidateType,
            );
            // Create a FilePond instance
            const pondSide = FilePond.create(inputElement, {
                acceptedFileTypes: ['image/png'],
                imagePreviewMaxHeight: 450,
                fileValidateTypeDetectType: (source, type) =>
                    new Promise((resolve, reject) => {
                        resolve(type);
                    }),
                files: [{
                    source: $(this).attr('alt'),
                    options: {
                        type: 'local',
                    },
                }],
            });

            FilePond.setOptions({
                server: {
                    url: '/admin/background/temp/upload',
                    revert: '/revert',
                    load:'?load=',
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                },
                labelIdle: 'select some files'
            })
            $('.img-update').addClass('disabled')
            pondSide.on('processfiles', () => {
                $('.img-update').removeClass('disabled')
            });
            pondSide.on('removefile', () => {
                if (pondSide.status === 0) {
                    $('.img-update').addClass('disabled')
                }
            });
        })

        $(".del-bg:not(.lena)").on('click', function () {
            let id = $(this).attr('id');
            $(".delete-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/background/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        }
                    })
                }
            }).modal('show')
        })

    </script>
@endsection
@section('scripts')
    <script>
        // FILE UPLOAD
        document.addEventListener('DOMContentLoaded', function () {
            const inputElement = document.querySelector('input[class="new-file-upload"]');
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileValidateType,
            );
            // Create a FilePond instance
            const pond = FilePond.create(inputElement, {
                acceptedFileTypes: ['image/png'],
                imagePreviewMaxHeight: 450,
                fileValidateTypeDetectType: (source, type) =>
                    new Promise((resolve, reject) => {
                        // Do custom type detection here and return with promise

                        resolve(type);
                    }),
            });

            FilePond.setOptions({
                server: {
                    url: '/admin/background/temp/upload',
                    revert: '/revert',
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                },

                labelIdle: 'select some files'

            })
            pond.on('processfiles', () => {

                $('.img-store').show()
                $('#background_store').addClass('contentChanged')
            });
            pond.on('removefile', () => {
                if (pond.status === 0) {
                    $('.img-store').hide()
                    $('#background_store').removeClass('contentChanged')
                }
            });
        })
        $('.main-menu a[href]:not([href$="\\#"]), .tree a[href]:not([href$="\\#"])').on('click', function () {
            if ($('#background_store').hasClass('contentChanged')) {
                //return window.confirm('Unsaved data \n You really want to continue?');
                event.preventDefault();
                var link = $(this).attr("href");
                $('.basic-error').modal({
                    onApprove: function () {
                        window.location = link;
                    }
                }).modal('show');
            }
        })

        $('.img-store').on('click', function () {
            $('.form_store').submit();
        })
        $('.img-update').on('click', function () {
            $('.form_update').submit();
        })
    </script>
@endsection

