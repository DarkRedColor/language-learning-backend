@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">Games</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add game</button>
        <div id="filterList" class="character-list ui buttons fluid ordered list">
            @foreach($templates as $item)
                <div class="char ui left labeled button game-form-preview" data-value="{{$item->id}}">
                    <a href="#" class="ui basic item label" data-value="">
                        <p>{{$item->name}}</p>
                    </a>
                    <button class="ui icon button character_edit_button negative game-detach">
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
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script type="text/javascript">
        $('.add-text').on('click', function () {
            $.ajax({
                type: "get",
                async: false,
                url: "{{route('game-templates-create')}}",
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let form = $('#edit');
                    form.empty();
                    form.append(res);
                }
            });
        })

        $('.game-form-preview').on('click', function () {
            let id = $(this).attr('data-value');
            $.ajax({
                type: "get",
                async: false,
                url: "/admin/game/templates/preview/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let form = $('#edit');
                    form.empty();
                    form.append(res);
                }
            });
        })

        $('.game-detach').on('click', function () {
            let id = $(this).parent().attr('data-value');
            $.ajax({
                type: "get",
                async: false,
                url: "/admin/game/templates/delete/modal/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let form = $('.delete-modal');
                    form.empty();
                    form.append(res);
                    let input = $('.game-relink')
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
                                url: "/admin/game/templates/delete/" + id + "/" + val,
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
    </script>

@endsection