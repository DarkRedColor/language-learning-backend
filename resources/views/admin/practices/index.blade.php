@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">Practice</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-theory"><i class="plus icon"></i>add</button>
        <div id="filterList" class="ui buttons fluid ordered results">
            @foreach($practices as $item)
                <div class="ui left labeled button action adv">
                    <a href="{{route('practice-pages-index',$item->id)}}" class="ui basic label">
                        <span>P</span>
                        <p>{{$item->name}}</p>
                    </a>
                    <button class="ui icon button theory-edit" data-value="{{$item->id}}">
                        <i class="edit outline icon"></i>
                    </button>
                    <button class="negative ui icon button practice-detach topic-grammar @if(!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" data-value="{{$item->id}}">
                        <i class="trash alternate icon"></i>
                    </button>
                </div>

            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">
    </div>

    <div class="ui basic modal game-modal">
        <div class="ui icon header">
            <h1>Delete Game</h1>
        </div>
        <div class="content">
            <div class="ui form">
                <div class="field delete-modal">

                </div>
            </div>
        </div>
        <div class="actions">
            <div class="ui grey cancel inverted button">
                <i class="remove icon"></i>
                Cancel
            </div>
            <div class="ui red ok inverted button" id="content-yes">
                <i class="trash icon"></i>
                Delete
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{asset('js/main.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/search_result.js')}}"></script>
    <script type="text/javascript">
        $('.add-theory').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".basic-error").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('practice-create')}}",
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
                    url: "{{route('practice-create')}}",
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
                            url: "/admin/practice/edit/" + id,
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
                    url: "/admin/practice/edit/" + id,
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
        $('.practice-detach').not('.lena').on('click', function () {
            let id = $(this).attr('data-value');
            $.ajax({
                type: "get",
                async: false,
                url: "/admin/practice/modal/delete/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let form = $('.delete-modal');
                    form.empty();
                    form.append(res);
                    let input = $('.practice-relink');
                    let val = input.val();
                    input.on('change', function () {
                        val = $(this).val();
                    })
                    $('.game-modal').modal({
                        closable: false,
                        onApprove: function () {
                            $.ajax({
                                type: "post",
                                async: false,
                                url: "/admin/practice/delete/" + id + "/" + val,
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
                }
            });
        })


        //     let id = $(this).attr('data-value');
        //     $(".delete-modal").modal({
        //         onApprove: function () {
        //             $.ajax({
        //                 type: "post",
        //                 url: "/admin/theory/delete/" + id,
        //                 contentType: 'application/json',
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 },
        //                 success: function () {
        //                     location.reload();
        //                 },
        //             });
        //         }
        //     }).modal('show');
        // })
    </script>
@endsection