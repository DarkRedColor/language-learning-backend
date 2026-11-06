@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree" id="arcade_store_lists">
        <h1 id="adventure-h">Arcade Theme</h1>
        <div id="filter" class="field">
            <label>Filter</label>
            <div class="ui selection dropdown">
                <input type="hidden" name="filter" value="" id="arcade_filter">
                <div class="text">All</div>
                <div class="menu">
                    <div class="item default" data-value="all">All</div>
                    <div class="item" data-value="unlock">Unlock</div>
                </div>
            </div>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search ..." id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="ui buttons fluid" id="filterList">
            <button class="add ui basic button" id="add_new_arcade_storage_button" data-value="1">
                <i class="plus icon"></i>add new arcade theme
            </button>
            @foreach($themes as $theme)
                <div class="ui left labeled button season adv"
                     data-value=""
                     id="">
                    <a href="{{route('arcade-storage-theme',$theme->id)}}"
                       class="ui basic label">
                        <span style="background-color: var(--chapter)">A</span>
                        <p>{{$theme->name_en}}</p>
                    </a>
                    <button class="ui icon button theme_edit_button" data-value="{{$theme->id}}">
                        <i class="edit outline icon"></i>
                    </button>
                    <button class="negative ui icon button arcade_storage_delete_button" data-value="{{$theme->id}}">
                        <i class="trash alternate icon"></i>
                    </button>
                </div>
            @endforeach

        </div>
    </div>
    <div id="edit" class="ten wide column">
        <div class="edit" id="arcade_storage_edit_create_zone">

        </div>
    </div>
    </button>
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        $("#add_new_arcade_storage_button").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-storage/create",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#arcade_storage_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/arcade-storage/create",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#arcade_storage_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })
        $(".theme_edit_button").on('click', function () {
            let episodeId = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-storage/edit/" + episodeId,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#arcade_storage_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/arcade-storage/edit/" + episodeId,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#arcade_storage_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })
        $('.arcade_storage_delete_button').on('click', function () {
            let id = $(this).attr('data-value')
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/arcade-storage/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        }
                    })
                }
            }).modal('show');
        })
    </script>

@endsection