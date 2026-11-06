@extends('admin.layouts.admin-intro-dashboard')

@section('content')

    <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
    <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
    <input type="text" hidden name="name" id="background_file_name"/>
    <div id="edit" class="fourteen wide column">
        <div id="bg">
            <h1 class="left">Animations</h1>
            <div class="ui search">
                <div class="ui icon input">
                    <input id="filterInput" class="prompt" type="text" placeholder="Type something">
                    <i class="search icon"></i>
                </div>
            </div>
            <div class="edit-area animations" id="filterList">
                <div class="anim-drop">
                    <input type="text" hidden name="anim-list">
                    <label id="dropped" class="add-bg">
                        <input type="file" name="rive" accept=".riv" >
                        <i class="plus icon big"></i>
                        <button class="upload-btn anim" id="background_upload_button">upload</button>
                        <a href="#" class="dis-img" id="anim-del"><i class="close icon"></i></a>
                    </label>
                    <div class="ui floating labeled icon dropdown button anim-dropdown">
                        <i class="play icon"></i>
                        <span class="text selected-anim">Animation</span>
                        <div class="menu">
                            <div class="header">
                                <i class="play icon"></i>
                                Choose animation
                            </div>
                            <div class="divider"></div>
                        </div>
                    </div>
                </div>

                @foreach($backgrounds as $item)
                    <div class="anim-card">
                        <div class="bg-cards">
{{--                            <a class="share-bg" onclick="copyClip(this);">--}}
{{--                                <i class="linkify icon"></i>--}}
{{--                            </a>--}}
                            @if (Auth::check() && Auth::user()->hasRole(['admin','qa','project','developer']))
                                <button class="del-bg" id="{{$item->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            @endif
                            <i class="play circle outline icon big"></i>
                            <input src="{{Storage::disk('gcs')->url("animations/$item->name")}}" type="file" class="animation-name" hidden data-value="{{$item->id}}">
                            <span class="bg-name">
                                <p>{{$item->name}}</p>
                            </span>
                        </div>
                        <div class="ui floating labeled icon dropdown button anim-dropdown">
                            <i class="list icon"></i>
                            <span class="text selected-anim">Animations list</span>
                            <div class="menu">
                                @foreach($item->animation as $emotion)
                                    <div class="item">{{$emotion->name}}</div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <div class="ui right vertical sidebar">
        <h2>Edit</h2>
        <div class="anim-preview">
            <a href="#" class="dis-img" id="preview-anim-del"><i class="close icon"></i></a>
                <input type="file" name="rive-preview" accept=".riv" >
                <i class="plus icon big"></i>
            <div class="ui floating labeled icon dropdown button anim-preview-dropdown">
                <i class="play icon"></i>
                <span class="text selected-preview-anim">Animation</span>
                <div class="menu preview-list">
                    <div class="header">
                        <i class="play icon"></i>
                        Choose animation
                    </div>
                    <div class="divider"></div>
                </div>
            </div>
        </div>
        <h5 id="animation_edit_name"></h5>
        <form action="/admin/animation/update/" method="post" id="background_update" enctype="multipart/form-data"
              class="form_update">
            @csrf
            <input type="file" name="rive-update" accept=".riv" class="anim-update-input">

            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save anim-update" id="" data-value="">UPDATE</button>
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
        </div>
    </div>
    <script src={{asset("js/search_animation.js")}} ></script>
    <script src="https://unpkg.com/@rive-app/canvas"></script>

    <script>
        // -------------- RIVE animation
        let riveName;
        $('input[name="rive"]').on('change', function() {
            let canvas = ('<canvas id="anim"  width="500" height="500"></canvas>')
            $(canvas).insertAfter(this);
            var file = $(this).prop('files')[0];
            var reader = new FileReader();

            reader.onload = function(event) {
                var arrayBuffer = event.target.result;
                var uint8Array = new Uint8Array(arrayBuffer);
                var blob = new Blob([uint8Array], { type: 'application/octet-stream' });
                riveName = URL.createObjectURL(blob);

                const r2 = new rive.Rive({
                    src: riveName,
                    canvas: document.getElementById('anim'),
                    autoplay: true,
                    layout: new rive.Layout({fit: 'cover', alignment: 'center center'}),
                    onLoad: () => {
                        r2.contents.artboards.forEach((artboard) => {
                            artboard.stateMachines.forEach((stateMachine) => {
                                var animList = (`<div class="item anim-choose">${stateMachine.name}</div>`);
                                $(animList).insertAfter('.anim-drop .anim-dropdown .menu .divider')
                            })
                        });
                        $('.selected-anim').on('DOMSubtreeModified', function(){
                            var artName = $(this).text();

                            console.log(artName.length)
                            r2.initData(artName);
                            setTimeout(function(){
                                r2.play(artName)
                            }, 2000)
                        })
                    }
                })
            };
            reader.readAsArrayBuffer(file);
        })
        // --------------- end animation
        // --------------- preview upload rive
        let rivePreview;
        $('input[name="rive-preview"]').on('change', function() {
            $('.anim-update').removeClass('disabled');
            let canvas = ('<canvas id="canvas-preview" width="500" height="833"></canvas>');
            $(canvas).insertAfter(this);
            var filePreview = $(this).prop('files')[0];
            var reader = new FileReader();

            reader.onload = function(event) {
                var arrayBuffer = event.target.result;
                var uint8Array = new Uint8Array(arrayBuffer);
                var blob = new Blob([uint8Array], { type: 'application/octet-stream' });
                rivePreview = URL.createObjectURL(blob);

                const r4 = new rive.Rive({
                    src: rivePreview,
                    canvas: document.getElementById('canvas-preview'),
                    autoplay: true,
                    layout: new rive.Layout({fit: 'cover', alignment: 'center center'}),
                    onLoad: () => {
                        r4.contents.artboards.forEach((artboard) => {
                            artboard.stateMachines.forEach((stateMachine) => {
                                var animList = (`<div class="item anim-list-preview">${stateMachine.name}</div>`);
                                $(animList).insertAfter('.anim-preview .anim-preview-dropdown .menu .divider')
                            })
                        });
                        $('.selected-preview-anim').on('DOMSubtreeModified', function(){
                            var artName = $(this).text();

                            console.log(artName)
                            r4.initData(artName);
                            setTimeout(function(){
                                r4.play(artName)
                            }, 2000)
                        })
                    }
                })

            };
            reader.readAsArrayBuffer(filePreview);
        })

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

        $('#anim-del').on('click', function () {
            var thisElem = $(this).parents('.anim-drop');
            thisElem.find('input[name="rive"]').val('');
            thisElem.find('.anim-choose').remove();
            thisElem.find('#anim').remove();
            // thisElem.find('.anim-dropdown .selected-anim').text('Animation');
            let dropped = $('#dropped');
            dropped.removeClass('vis');
            dropped.addClass('add-bg');
            $('#dropped-edit').removeClass('vis').addClass('edit-bg');
            $('#dropped .plus').removeClass('bg-error');
        })

        // ---- save animation
        $('.upload-btn').one('click', function() {
            $("#wait").modal('show');
            r.play('stand_by');

            var animArr = [];
            $('.anim-choose').each(function() {
                animArr.push($(this).text());
            });
            var filePath = $('input[name="rive"]').val();
            var fileName = filePath.split('\\').pop().split('.')[0];
            var formData = new FormData();
            formData.append('file', $('input[name="rive"]').prop('files')[0]);
            formData.append('animation-list', JSON.stringify(animArr));
            formData.append('name', JSON.stringify(fileName));

            $.ajax({
                type: "POST",
                url: "{{route('animation-store')}}",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    $('#dropped .plus').addClass('bg-error');
                    $('.upload-btn, .dis-img').show();
                    $("#wait").modal('hide');
                }
            });
        });
        // ------ end save
        // ------ update animation
        $('.anim-update').one('click', function(){
            $("#wait").modal('show');
            r.play('stand_by');

            var animArr = [];
            $('.anim-list-preview').each(function() {
                animArr.push($(this).text());
            });
            let previewId = $(".anim-preview").attr('data-value');
            var filePath = $('input[name="rive-preview"]').val();
            var fileName = filePath.split('\\').pop().split('.')[0];
            var formData = new FormData();
            formData.append('file', $('input[name="rive-preview"]').prop('files')[0]);
            formData.append('animation-list', JSON.stringify(animArr));
            formData.append('name', JSON.stringify(fileName));

            $.ajax({
                type: "POST",
                url: "/admin/animation/update/" + previewId,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    $('#dropped .plus').addClass('bg-error');
                    $('.upload-btn, .dis-img').show();
                    $("#wait").modal('hide');
                }
            });
        })
        // ------ end update anim animation


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
        // ------ edit mode
        // aside edit toggle
        $('.bg-cards .play.icon, .button.dis').click(() => {
            $('.ui.sidebar').sidebar('toggle');
        });
        // append data from card to preview
        $('.bg-cards').on('click', function() {
            let source = $(this).children('.animation-name').attr('src');
            let name = $(this).children('.bg-name').text();
            $('.anim-update').addClass('disabled');

            $('#animation_edit_name').empty().append(name);
            let id = $(this).children('.animation-name').attr('data-value');
            $('.anim-preview').attr('data-value', id)
            let previewCanvas = ('<canvas id="canvas-preview" width="500" height="833"></canvas>')
            $(previewCanvas).insertAfter('#preview-anim-del');
            // ----- copy animations list
            var items = $(this).siblings('.anim-dropdown').find('.menu .item');
            var divider = $('.anim-preview .anim-preview-dropdown .menu .divider');
            items.clone().insertAfter(divider);

            // ----- init RIVE preview
            const r3 = new rive.Rive({
                src: source,
                canvas: document.getElementById('canvas-preview'),
                autoplay: true,
                layout: new rive.Layout({fit: 'cover', alignment: 'center center'}),
            })
            $('.selected-preview-anim').on('DOMSubtreeModified', function(){
                var artName = $(this).text();
                r3.initData(artName);
                setTimeout(function(){
                    r3.play(artName)
                }, 2000)
            })
        })
        //----- delete preview info
        $('#preview-anim-del').on('click', function(){
            $('#canvas-preview').remove();
            $('.anim-preview .anim-preview-dropdown .item').remove();
            $('.anim-preview .selected-preview-anim').text('Animations');
        })

        $('#background_cancel_edit_form').on('click', function(){
            $('#canvas-preview').remove();
            $('.anim-preview .anim-preview-dropdown .item').remove();
            $('.anim-preview .selected-preview-anim').text('Animations');
            $(".anim-preview").attr('data-value', '');
        })

        $(document).ready(function(){

        $('.ui.sidebar').sidebar({
            onHide: function() {
                $('#canvas-preview').remove();
                $('.anim-preview .anim-preview-dropdown .item').remove();
                $('.anim-preview .selected-preview-anim').text('Animations');
                $(".anim-preview").attr('data-value', '');
            }
        });
        })

        $(".del-bg:not(.lena)").on('click', function () {
            let id = $(this).attr('id');
            $(".delete-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/animation/delete/" + id,
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
{{--@section('scripts')--}}
{{--    <script>--}}
{{--        // FILE UPLOAD--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            const inputElement = document.querySelector('input[class="new-file-upload"]');--}}
{{--            FilePond.registerPlugin(--}}
{{--                FilePondPluginImagePreview,--}}
{{--                FilePondPluginImageExifOrientation,--}}
{{--                FilePondPluginFileValidateType,--}}
{{--            );--}}
{{--            // Create a FilePond instance--}}
{{--            const pond = FilePond.create(inputElement, {--}}
{{--                acceptedFileTypes: ['image/png'],--}}
{{--                imagePreviewMaxHeight: 450,--}}
{{--                fileValidateTypeDetectType: (source, type) =>--}}
{{--                    new Promise((resolve, reject) => {--}}
{{--                        // Do custom type detection here and return with promise--}}

{{--                        resolve(type);--}}
{{--                    }),--}}
{{--            });--}}

{{--            FilePond.setOptions({--}}
{{--                server: {--}}
{{--                    url: '/admin/animation/temp/upload',--}}
{{--                    revert: '/revert',--}}
{{--                    headers: {--}}
{{--                        'X-CSRF-TOKEN': "{{csrf_token()}}"--}}
{{--                    },--}}
{{--                },--}}

{{--                labelIdle: 'select some files'--}}

{{--            })--}}
{{--            pond.on('processfiles', () => {--}}
{{--                $('.img-store').show()--}}
{{--                $('#background_store').addClass('contentChanged')--}}
{{--            });--}}
{{--            pond.on('removefile', () => {--}}
{{--                if (pond.status === 0) {--}}
{{--                    $('.img-store').hide()--}}
{{--                    $('#background_store').removeClass('contentChanged')--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}
{{--        $('.main-menu a[href]:not([href$="\\#"]), .tree a[href]:not([href$="\\#"])').on('click', function () {--}}
{{--            if ($('#background_store').hasClass('contentChanged')) {--}}
{{--                //return window.confirm('Unsaved data \n You really want to continue?');--}}
{{--                event.preventDefault();--}}
{{--                var link = $(this).attr("href");--}}
{{--                $('.basic-error').modal({--}}
{{--                    onApprove: function () {--}}
{{--                        window.location = link;--}}
{{--                    }--}}
{{--                }).modal('show');--}}
{{--            }--}}
{{--        })--}}

{{--        $('.img-store').on('click', function () {--}}
{{--            $('.form_store').submit();--}}
{{--        })--}}
{{--        $('.img-update').on('click', function () {--}}
{{--            $('.form_update').submit();--}}
{{--        })--}}
{{--    </script>--}}
{{--@endsection--}}

