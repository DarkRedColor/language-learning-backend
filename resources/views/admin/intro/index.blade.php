@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <input hidden id="pos">
    <input hidden id="down" value="0">
    <input hidden id="prev_position_type">
    <div class="four wide column tree">
        <h1>Intro</h1>
        <button class="ui icon button" id="intro_edit_button">
            <i class="edit outline icon"></i>
        </button>
        @foreach($scenes as $item)
            <div class="ui styled accordion intro-scene">
                <div class="title">
                    <div class="ui left labeled button scene-name" tabindex="0">
                        <i class="chevron down icon"></i>
                        <a href="#" class="ui label scene_edit_button" data-value="{{$item->id}}">
                            <h3>Scene {{$loop->index + 1}}</h3>
                        </a>
                        <div class="red ui icon button del" id="delete_scene" name="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </div>
                    </div>

                </div>

                @php
                    $intros = $item->intros;
                $sceneId = $item->id;
                @endphp

                @include('admin.intro.includes.pages',[$intros,$contentTypes,$sceneId])

            </div>
        @endforeach

        <div class="active title">
            <a href="{{route('scene-store')}}" class="add ui basic button" id="add_scene"><i class="plus icon"></i>ADD
                NEW SCENE</a>
        </div>
    </div>

    <div id="edit" class="ten wide column">
        <div class="edit" id="intro_edit_create_area">

        </div>
    </div>

    <script>
        $(document).ready(function () {
            let selectedId = "{{session("id") ?? null}}";
            if (selectedId === "") {
                $(".basic").on('click', function () {
                    $('.check').removeClass('check')
                    $('.title').removeClass('active')
                    $(this).addClass("check");
                    let subId = $(this).attr('data-value');
                    let id = $(this).attr("id");
                    let classList = $(this).attr("class");
                    let classArr = classList.split(/\s+/);
                    let type = classArr[0];
                    let grand = classArr[classArr.length - 2]
                    if (grand === "grand") {
                        type = 'grand';
                    }
                    let typeArr = ['line', 'action', 'notification', 'choice', 'grand'];
                    let tree = $(this).parent().children(":not(#add-page)");
                    let data = [];
                    for (let i = 0; i < tree.length; i++) {
                        if (tree[i].id === "choice") {
                            let classList = $(tree[i]).attr("class");
                            let classArr = classList.split(/\s+/);
                            data[i] = classArr[0]
                        } else {
                            data[i] = tree[i].id;
                        }
                    }
                    if (typeArr.includes(type)) {
                        // if ($('.form').length !== 0) {
                        if ($('.form').hasClass("contentChanged")) {
                            $('.basic-error').modal({
                                closable: false,
                                onApprove: function () {
                                    ajax(id, type, data, subId)
                                },
                                success: function () {
                                    $(this).addClass('check')
                                }
                            }).modal('show');
                        } else {
                            ajax(id, type, data, subId)
                        }
                        // } else {
                        //     ajax(id, type, data, subId)
                        // }
                    }
                })
            } else {
                $(".basic").on('click', function () {
                    let subId = $(this).attr('data-value');
                    $('.check').removeClass('check')
                    $(this).addClass("check");
                    let id = $(this).attr("id");
                    let classList = $(this).attr("class");
                    let classArr = classList.split(/\s+/);
                    let type = classArr[0];
                    let grand = classArr[classArr.length - 2]
                    if (grand === "grand") {
                        type = 'grand';
                    }
                    let typeArr = ['line', 'action', 'notification', 'choice', 'grand'];
                    let tree = $(this).parent().children(":not(#add-page)");
                    let data = [];
                    for (let i = 0; i < tree.length; i++) {
                        if (tree[i].id === "choice") {
                            let classList = $(tree[i]).attr("class");
                            let classArr = classList.split(/\s+/);
                            data[i] = classArr[0]
                        } else {
                            data[i] = tree[i].id;
                        }
                    }
                    if (typeArr.includes(type)) {
                        if ($('.form').hasClass("contentChanged")) {
                            $('.basic-error').modal({
                                closable: false,
                                onApprove: function () {
                                    ajax(id, type, data, subId)
                                },
                                success: function () {
                                    $(this).addClass('check')
                                }
                            }).modal('show');
                        } else {
                            ajax(id, type, data, subId)
                        }
                    }
                })

                let subId = $("#" + selectedId).attr('data-value');
                let elem = $("#" + selectedId);
                elem.addClass("check");
                let id = selectedId;
                let classList = elem.attr("class");
                let classArr = classList.split(/\s+/);
                let type = classArr[0];
                let grand = classArr[classArr.length - 2]
                if (grand === "grand") {
                    type = 'grand';
                }
                let typeArr = ['line', 'action', 'notification', 'choice', 'grand'];
                let tree = $(this).parent().children(":not(#add-page)");
                let data = [];
                for (let i = 0; i < tree.length; i++) {
                    if (tree[i].id === "choice") {
                        let classList = $(tree[i]).attr("class");
                        let classArr = classList.split(/\s+/);
                        data[i] = classArr[0]
                    } else {
                        data[i] = tree[i].id;
                    }
                }
                if (typeArr.includes(type)) {
                    ajax(id, type, data, subId)
                }

            }
            // Scroll to views
            if ($('.button.check')) {
                $('.button.check').parents().addClass('active')
                $('.tree').animate({
                    scrollTop: ($('.tree .ui.basic.button.check').offset().top - 150),
                }, 500);
            }

        })

        function ajax(id, type, data, subId) {
            $.ajax({
                type: "get",
                url: "/admin/intro/edit/modal/" + id + "/" + type + "/" + subId,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $("#" + id).addClass('check')
                    let edit = $('#intro_edit_create_area');
                    edit.empty();
                    edit.append(res);
                    $("#tree").val(JSON.stringify(data))
                }
            });
        }

        $('.add-page').on('click', function () {
            let type = "line";
            let id = $(this).attr('name');
            let next_id = $(this).attr('next_id')
            let initial = $(this).attr('initial');
            let subId = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "POST",
                            url: "/admin/intro/create/modal/" + type + "/" + id + "/" + next_id + "/" + initial + "/" + subId,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let edit = $('#intro_edit_create_area');
                                edit.empty();
                                edit.append(res);

                            }
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "POST",
                    url: "/admin/intro/create/modal/" + type + "/" + id + "/" + next_id + "/" + initial + "/" + subId,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let edit = $('#intro_edit_create_area');
                        edit.empty();
                        edit.append(res);

                    }
                });
            }
        });
        // onClick to another scene
        $(".scene_edit_button").on('click', function () {
            $('.title').removeClass('active');
            const active = $(this).closest('.title');
            active.addClass('active');
            $('.basic').removeClass('check');
            let id = $(this).attr('data-value');
            if ($('form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/scene/edit/" + id,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                active.addClass('active');
                                let edit = $('#intro_edit_create_area');
                                edit.empty();
                                edit.append(res);
                            }
                        });
                    }
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/scene/edit/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let edit = $('#intro_edit_create_area');
                        edit.empty();
                        edit.append(res);
                    }
                });
            }
        })

        $("#intro_edit_button").on('click', function () {
            $.ajax({
                type: "get",
                url: "/admin/intro/edit",
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let edit = $('#intro_edit_create_area');
                    edit.empty();
                    edit.append(res);
                }
            });

        });

        $(".del").on('click', function () {
            let thisDel = $(this);
            $('.delete-modal').modal({
                onApprove: function () {
                    let id = thisDel.attr('name');
                    $.ajax({
                        type: "post",
                        url: "/adim/scene/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        }
                    });
                }
            }).modal('show');
        })


    </script>
@endsection