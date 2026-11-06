@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">Theory</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-theory"><i class="plus icon"></i>add</button>
        <div id="filterList" class="ui buttons fluid ordered results">
            @foreach($theories as $item)

                @if($item->type == 0)
                    <div class="ui left labeled button word adv">
                        <a href="{{route('theory-words-index',$item->id)}}" class="ui basic label">
                            <span>W</span>
                            <p>{{$item->name}}</p>
                        </a>
                        <button class="ui icon button theory-edit" data-value="{{$item->id}}">
                            <i class="edit outline icon"></i>
                        </button>
                        <button class="negative ui icon button  @if(!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" data-value="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>
                    </div>
                @elseif($item->type == 1)
                    <div class="ui left labeled button grammar adv">
                        <a href="{{route('theory-pages-index',$item->id)}}" class="ui basic label">
                            <span>G</span>
                            <p>{{$item->description_en}}</p>
                        </a>
                        <button class="ui icon button theory-edit" data-value="{{$item->id}}">
                            <i class="edit outline icon"></i>
                        </button>
                        <button class="negative ui icon button topic-grammar @if(!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" data-value="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>
                    </div>
                @else
                    <div class="ui left labeled button topics adv">
                        <a href="{{route('theory-pages-index',$item->id)}}" class="ui basic label">
                            <span>T</span>
                            <p>{{$item->description_en}}</p>
                        </a>
                        <button class="ui icon button theory-edit" data-value="{{$item->id}}">
                            <i class="edit outline icon"></i>
                        </button>
                        <button class="negative ui icon button topic-grammar @if(!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" data-value="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">
    </div>
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script>

        $('.add-theory').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".basic-error").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('theory-create')}}",
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
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: "{{route('theory-create')}}",
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

        $('button.theory-edit').on('click', function () {
            let id = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $(".basic-error").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/theory/edit/" + id,
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
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/theory/edit/" + id,
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

        $('.negative.button:not(.lena)').on('click', function () {
            let id = $(this).attr('data-value');
            $(".delete-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/theory/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        },
                    });
                }
            }).modal('show');
        })
        $('.topic-grammar:not(.lena)').on('click', function () {
            let id = $(this).attr('data-value');
            $(".delete-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/theory/topic-grammar/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        },
                    });
                }
            }).modal('show');
        })
    </script>
@endsection
