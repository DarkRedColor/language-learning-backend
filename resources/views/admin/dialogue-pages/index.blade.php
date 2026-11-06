@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <input type="hidden" value="{{$chapterId}}" id="dialogue_sub_chapter_id">
    <input type="hidden" value="{{$parent->id ?? null}}" id="dialogue_index_parent_id">
    <div class="four wide column tree">
        <h1>Dialogues</h1>
        <div class="import-export">
            <button type="button" class="ui icon button download-file">
                <i class="file excel icon large"></i>
            </button>
            <form method="post" action="/admin/new/dialogue/import/{{$subChapter->id}}" id="import_new"
                  enctype="multipart/form-data">
                @csrf
                <label class="ui icon button upload-eng">
                    <input type="file" class="import-new-text" name="import-new-file"
                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <i class="cloud upload icon large"></i>
                    eng</label>
            </form>
            <form method="post" action="{{route('dialogue-import')}}" id="import" enctype="multipart/form-data">
                @csrf
                <label class="ui icon button upload-file">
                    <input type="file" class="import-text" name="import-file"
                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <i class="cloud upload icon large"></i></label>
            </form>
            {{--            <button type="button" class="ui icon button upload-eng">--}}
            {{--                <i class="cloud upload icon large"></i>--}}
            {{--                eng--}}
            {{--            </button>--}}
            {{--            <button type="button" class="ui icon button upload-file">--}}
            {{--                <i class="cloud upload icon large"></i>--}}
            {{--            </button>--}}
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="ui small breadcrumb">
            @if($parentId)

                <a href="{{route('dialogue-page-index',[$chapterId,$parent->parent_id])}}"><i
                            class="arrow left icon"></i></a>
                <a href="{{route('dialogue-index')}}"
                   class="section"><i
                            class="right chevron icon"></i>{{$subChapter->name_en ?? "Dialogue"}}</a>
            @else
                <a href="{{route('dialogue-index')}}"><i class="arrow left icon"></i></a>
                <a href="{{route('dialogue-index')}}"
                   class="section"><i
                            class="right chevron icon"></i>{{$subChapter->name_en}}</a>
            @endif

            @if(isset($arr))
                @foreach($arr as $key=>$item)
                    @if(isset($namesOfElements[$key]))
                        <a href="{{route('dialogue-page-index',[$chapterId,$item])}}"
                           class="section"><i
                                    class="right chevron icon"></i>{{$namesOfElements[$key]}}</a>
                    @endif
                @endforeach
            @endif
        </div>
        <div id="filterList" class="ui buttons fluid stories">
            @foreach($pages as $item)
                @if($item->content_type == 0)
                    <div class="{{$item->id}} ui left labeled button line adv" id="{{$loop->index + 1}}"
                         data-value="{{$item->id}}">
                        <a href="#" class="ui basic label">
                            <span>{{$loop->index + 1}}L</span>
                            <p>{{$item->text_en}}</p>
                            @php
                                $ru = "text_ru-RU"
                            @endphp
                            <p class="hid">{{$item->$ru}}</p>
                        </a>
                        <button class="ui icon button dialogue-button">
                            <i class="edit outline icon"></i>
                        </button>
                    </div>
                @endif
                @if($item->content_type == 1)
                    <div class="{{$item->id}} ui left labeled button action adv" id="{{$loop->index + 1}}"
                         data-value="{{$item->id}}">
                        <a href="#" class="ui basic label">
                            <span>{{$loop->index + 1 }}A</span>
                            <p>{{$item->text_en}}</p>
                            @php
                                $ru = "text_ru-RU"
                            @endphp
                            <p class="hid">{{$item->$ru}}</p>
                        </a>
                        <button class="ui icon button dialogue-button">
                            <i class="edit outline icon"></i>
                        </button>
                    </div>
                @endif

                @if($item->content_type == 2)
                    <div class="{{$item->id}} ui left labeled button notification adv" id="{{$loop->index + 1}}"
                         data-value="{{$item->id}}">
                        <a href="#" class="ui basic label">
                            <span>{{$loop->index + 1 }}N</span>
                            <p>{{$item->text_en}}</p>
                            @php
                                $ru = "text_ru-RU"
                            @endphp
                            <p class="hid">{{$item->$ru}}</p>
                        </a>
                        <button class="ui icon button dialogue-button">
                            <i class="edit outline icon"></i>
                        </button>
                    </div>
                @endif

                @if($item->content_type == 3)
                    <div class="{{$item->id}} ui left labeled button @if($item->text_en <> null)in-choice @endif choices adv"
                         id="{{$loop->index + 1}}" data-value="{{$item->id}}">
                        <a href="{{route('dialogue-page-index',[$chapterId,$item->id])}}"
                           class="ui basic label">
                            <span>{{$loop->index + 1 }}C</span>
                            <p>{{$item->text_en ?? "Choice " . ($loop->index + 1)}}</p>
                            @php
                                $ru = "text_ru-RU"
                            @endphp
                            <p class="hid">{{$item->$ru}}</p>
                        </a>
                        <button class="ui icon button dialogue-button">
                            <i class="edit outline icon"></i>
                        </button>
                    </div>
                @endif
            @endforeach

            <button class="add ui basic button"><i class="plus icon"></i>ADD NEW PAGE</button>
        </div>

    </div>
    <div id="edit" class="ten wide column">
    </div>

    <script src="{{asset('js/main.js')}}"></script>
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script>
        $(document).ready(function () {
            let checked = {{session("id") ?? "null"}};
            if (checked != null) {
                $('.' + checked).addClass('check')
            }
            $('.tree').animate({
                scrollTop: ($('.ui.left.labeled.button.check').offset().top - 150)
            }, 500);
        })
        $('div .button').on('click', function () {
            $('div .button').removeClass('check');
            $(this).addClass('check');
            let path = $('div.breadcrumb')
            if (path.hasClass("something-is-selected")) {
                path.children().last().remove();
                path.append('<a href="#" class="section active"><i class="right chevron icon"></i>' + $($(this).children('a')).children('span').text() + '</a>')
            } else {
                path.addClass("something-is-selected")
                path.append('<a href="#" class="section active"><i class="right chevron icon"></i>' + $($(this).children('a')).children('span').text() + '</a>')
            }
        })


        $('button.add.button').on('click', function () {
            let editAria = $('#edit')
            let content_type = "line";
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/dialogue/page/create-and-edit/" + content_type,
                            contentType: 'application/json',
                            async: true,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                editAria.empty();

                                editAria.append(res);
                            }
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/dialogue/page/create-and-edit/" + content_type,
                    contentType: 'application/json',
                    async: true,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        editAria.empty();

                        editAria.append(res);
                    }
                });
            }

        })

        $('button.dialogue-button').on('click', function () {
            let parent = $(this).parent();
            let id = parent.attr('data-value');

            let content_type = "choice"
            let editAria = $('#edit')
            if (parent.hasClass('line')) {
                content_type = "line"
            }
            if (parent.hasClass('action')) {
                content_type = "action"
            }
            if (parent.hasClass('notification')) {
                content_type = "notification"
            }
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/dialogue/page/create-and-edit/" + content_type + '/' + id,
                            contentType: 'application/json',
                            async: true,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                editAria.empty();
                                editAria.append(res);
                            }
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/dialogue/page/create-and-edit/" + content_type + '/' + id,
                    contentType: 'application/json',
                    async: true,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        editAria.empty();
                        editAria.append(res);
                    }
                });
            }
        })
        $(document).ready(function () {
            if ($('.button.choices').hasClass('in-choice')) {
                $('.add.button').hide();
            }
        })

        window.onload = () => {
            if ($('.stories').children().not('.add.button').length == 0) {
                $('.upload-file').hide();
            } else {
                $('.upload-eng').hide();
            }
        }
        $('.download-file').on('click', function () {
            location.href = "/admin/dialogue/export/{{$subChapter->id}}"
        })

        $('.import-text').on('change', function () {
            $('#import').submit();
        })

        $('.import-new-text').on('change', function () {
            $('#import_new').submit();
        })

    </script>
@endsection
