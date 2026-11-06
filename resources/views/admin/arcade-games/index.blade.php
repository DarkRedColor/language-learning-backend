@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree" id="arcade_store_lists">
        <h1 id="adventure-h">Arcade Games</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search ..." id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="character-list ui buttons fluid ordered list results gramm-pages" id="filterList">
            <button class="add ui basic button" id="add_new_arcade_game_btn" data-value="1">
                <i class="plus icon"></i>add new arcade game
            </button>
            @foreach($arcades as $item)
                <div class="char ui left labeled button" id="">
                    <a href="{{route('arcade-games-list',$item->id)}}" class="ui basic item label" data-value="">
                        @if($item->type == 0)
                            <p>Type Fast</p>
                        @else
                            <p>Find Words</p>
                        @endif
                    </a>
                    <button class="ui icon button edit" data-value="{{$item->id}}">
                        <i class="edit icon"></i>
                    </button>
                    <button class="ui icon button negative delete arcade_storage_delete_button" data-value="{{$item->id}}">
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
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        $("#add_new_arcade_game_btn").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-games/create",
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
                    url: "/admin/arcade-games/create",
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
        $(".edit.button").on('click', function () {
            let arcadeGame = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-games/edit/" + arcadeGame,
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
                    url: "/admin/arcade-games/edit/" + arcadeGame,
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
        $('.arcade_storage_delete_button').on('click',  function () {
            let id = $(this).attr('data-value')
            console.log(id)
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/arcade-games/delete/" + id,
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