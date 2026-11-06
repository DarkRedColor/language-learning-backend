@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">{{$theories->name}}</h1>
        <div id="filter" class="field">
            <label>Filter</label>
            <div class="ui selection dropdown @if($order <> false)filter-changed @endif">
                @if($filter === "created_at" && $order === true)
                    <input type="hidden" name="filter" class="langly-filter" value="3">
                    <div class="text">Last <br>create</div>

                @elseif($filter === "word" && $order === true)
                    <input type="hidden" name="filter" class="langly-filter" value="2">
                    <div class="text">Z - A</div>
                @else
                    <input type="hidden" name="filter" class="langly-filter" value="1">
                    <div class="text">A - Z</div>
                @endif
                <div class="menu filter-menu">
                    <div class="item default" data-value="1" filter="word" order="false">A - Z</div>
                    <div class="item" data-value="2" filter="word" order="true">Z - A</div>
                    <div class="item" data-value="3" filter="created_at" order="true">Last <br>create</div>
                </div>
            </div>
        </div>
        <div class="ui search">
            <div class="ui icon input back-btn">
                <a class="ui button" href="{{route('theory-index')}}"><i class="chevron left icon"></i></a>
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button  add-theory add-words-theory"><i
                    class="plus icon"></i>add
        </button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results">
            @foreach($theories->words->sortBy($filter,0,$order ?? false) as $item)
                <div class="char ui left labeled button">
                    <a href="#" class="ui basic item label" data-value="">
                        <p>{{$item->word}}</p>
                        @foreach($languages as $language)
                            @if($language->lcid <> "en")
                                @php
                                    $iso = "meaning_" . $language->lcid;
                                @endphp
                                <p hidden>{{$item->$iso}}</p>
                            @endif
                        @endforeach
                    </a>
                    <button class="ui icon button character_edit_button words-edit" data-value="" id="{{$item->id}}">
                        <i class="edit icon"></i>
                    </button>
                    <button type="button" class="ui icon button character_edit_button negative words-detach @if (!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))
                    lena
                @endif" data-value=""
                            id="{{$item->id}}">
                        <i class="cancel icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>
    <div class="oxford">

    </div>

    <script type="application/javascript" src={{asset("js/main.js")}} ></script>
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script>


        $(".add-words-theory").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".discard-modal").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/theory/words/create/{{$theories->id}}",
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
                    url: "/admin/theory/words/create/{{$theories->id}}",
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
        })

        $('.words-edit').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".discard-modal").modal({
                    onApprove: function () {
                        let id = $(this).attr('id');
                        $.ajax({
                            type: "get",
                            url: "/admin/theory/words/edit/" + id + "/{{$theories->id}}",
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
                let id = $(this).attr('id');
                $.ajax({
                    type: "get",
                    url: "/admin/theory/words/edit/" + id + "/{{$theories->id}}",
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
        })
        $(document).ready(function () {
            $('.char.labeled.button').on('click', function () {
                $('.char.labeled.button').removeClass('check');
                $(this).addClass('check');
            })
            let checked = {{session("id") ?? "null"}};
            if (checked !== null) {
                $('#' + checked).parent().addClass('check');
            }
            $('.tree').animate({
                scrollTop: ($('.tree .ui.labeled.check').offset().top - 150)
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
            location.href = "/admin/theory/words/{{$theories->id}}/" + filter + "/" + order;
        })

        $('.words-detach:not(.lena)').on('click', function () {
            let id = $(this).attr('id');
            $(".delete-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/theory/words/delete/" + id + "/{{$theories->id}}",
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload()
                        },
                    });
                }
            }).modal('show');
        })
    </script>
@endsection