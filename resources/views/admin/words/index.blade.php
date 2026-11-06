@extends('admin.layouts.admin-intro-dashboard')

@section('content')

    <div class="four wide column tree lang-list">
        <div class="head-list">
            <h1 id="adventure-h">Words</h1>
            <div id="filter" class="field">
                <label>Filter</label>
                <div class="ui selection dropdown @if($order <> 'asc') filter-changed @endif">
                    @if($filter === "word" && $order === 'asc')
                        <input type="hidden" name="filter" class="langly-filter" value="1">
                        <div class="text">A - Z</div>
                    @elseif($filter === "word" && $order === 'desc')
                        <input type="hidden" name="filter" class="langly-filter" value="2">
                        <div class="text">Z - A</div>
                    @else
                        <input type="hidden" name="filter" class="langly-filter" value="3">
                        <div class="text">Last <br>create</div>
                    @endif
                    <div class="menu filter-menu">
                        <div class="item" data-value="1" filter="word" order="asc">A - Z</div>
                        <div class="item" data-value="2" filter="word" order="desc">Z - A</div>
                        <div class="item" data-value="3" filter="created_at" order="desc">Last <br>create</div>
                    </div>
                </div>
            </div>
            <div class="ui search">
                <div class="ui icon input">
                    <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                    <i class="search icon"></i>
                </div>
            </div>
            <button class="add ui basic button add-text"><i class="plus icon"></i>add word</button>
        </div>
        <div id="filterList" class="character-list ui buttons fluid ordered list results">
            @foreach($words as $item)
                <div class="char ui left labeled button">
                    <a href="#" class="ui basic item label" data-value="">
                        <p>{{$item->word}}</p>
                        @php
                            $iso = "meaning_ru-RU";
                        @endphp
                        <p hidden>{{$item->$iso}}</p>
                    </a>
                    <button class="ui icon button word_edit_button" id="{{$item->id}}" data-value="">
                        <i class="edit icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>
    <div class="oxford">
    </div>
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script>
        $('div.char.labeled.button').on('click', function () {
            $('div.char.labeled.button').removeClass('check');
            $(this).addClass('check');
        })
        $('button.word_edit_button').on('click', function () {
            let id = $(this).attr('id');
            if ($('.form').hasClass('contentChanged')) {
                $(".basic-error").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/word/edit/" + id,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#edit");
                                form.empty();
                                form.append(res)
                            },
                        });
                        $('.form').removeClass('contentChanged');
                        $('.button.save, .button.discard').attr('disabled', true);
                    }
                }).modal('show');
            } else {
                // let id = $(this).attr('id');
                $.ajax({
                    type: "get",
                    url: "/admin/word/edit/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#edit");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        });
        $('button.add.add-text').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".basic-error").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/word/create",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#edit");
                                form.empty();
                                form.append(res)
                            },
                        });
                        $('.form').removeClass('contentChanged');
                        $('.button.save, .button.discard').attr('disabled', true);
                    }
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/word/create",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#edit");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        });

        $(document).ready(function () {
            let checked = {{session("id") ?? "null"}};
            if (checked !== null) {
                $('#' + checked).parent().addClass('check');
            }
            $('.tree').animate({
                scrollTop: ($('.tree .ui.labeled.check').offset().top - 250)
            }, 500);
        })

        $('.langly-filter').on('change', function () {
            let value = $(this).val();
            let menu = $('.filter-menu >*')
            let filter = null;
            let order = null;

            menu.each(function () {
                if (value === $(this).attr('data-value')) {
                    filter = $(this).attr('filter');
                    order = $(this).attr('order');
                }
            })
            location.href = "/admin/word/" + filter + "/" + order;
        })

    </script>
@endsection
