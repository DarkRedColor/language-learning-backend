@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree" id="arcade_store_lists">
        <div class="head-list">
            <h1 id="adventure-h">{{$arcade->name_en}}</h1>
            <div class="import-export">
            <form method="post" action="{{route('arcade-theme-word-import')}}" id="import"
                  enctype="multipart/form-data">
                @csrf
                <label class="ui icon button upload-file">
                    <input type="file" class="import-new-text" name="import-text"
                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <i class="file excel icon large"></i>
                    <input type="hidden" name="arcadeThemeId" value="{{$id}}">
                    </label>
            </form>
            </div>
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
            <button onclick="location.href = '{{route('arcade-storage')}}'" class="ui left labeled icon button back">
                <i class="left arrow icon"></i>
                Back to themes
            </button>
            <div class="ui search">
                <div class="ui icon input">
                    <input class="prompt" type="text" placeholder="Search ..." id="filterInput">
                    <i class="search icon"></i>
                </div>
            </div>
            <button class="add ui basic button" id="add_new_word_button" data-value="1">
                <i class="plus icon"></i>add new word in theme
            </button>
        </div>
        <div class="character-list ui buttons fluid ordered list results gramm-pages" id="filterList">
            @foreach($words as $word)
            <div class="char ui left labeled button" id="">
                <a href="#" class="ui basic item label" data-value="">
                    <p>{{$word->word}}</p>
                </a>
                <button class="ui icon button edit" data-value="{{$word->id}}">
                    <i class="edit icon"></i>
                </button>
                <button class="ui icon button negative delete" data-value="{{$word->id}}">
                    <i class="trash alternate icon"></i>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">
        <div class="edit" id="arcade_word_edit_create_zone">

        </div>
    </div>
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        const form = $("#arcade_word_edit_create_zone");
        $("#add_new_word_button").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-storage/theme-word-create/{{$id}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/arcade-storage/theme-word-create/{{$id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })
        $(".char .button.edit").on('click', function () {
            let episodeId = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-storage/theme-word-edit/{{$id}}/" + episodeId ,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/arcade-storage/theme-word-edit/{{$id}}/" + episodeId ,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })
        $('.negative.delete').on('click',  function () {
            let id = $(this).attr('data-value')
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/arcade-storage/theme-word-delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            form.empty();
                            location.reload();
                        }
                    })
                }
            }).modal('show');
        })
        $('.import-new-text').on('change', function () {
            $('#import').submit();
        })
    </script>

@endsection