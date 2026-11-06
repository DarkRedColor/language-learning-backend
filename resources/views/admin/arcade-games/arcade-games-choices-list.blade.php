@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree" id="arcade_store_lists">
        <h1 id="adventure-h">Arcade Games</h1>
        <div class="ui small breadcrumb" id="arcade_path">
            <a href="{{route('arcade-games-list')}}"><i class="arrow left icon"></i></a>
            <a href="{{route('arcade-games')}}" class="section">Arcade Games</a>
            <i class="right chevron icon divider"></i>
            <a href="#" class="section">Type Fast</a>
            <i class="right chevron icon divider"></i>
            <a href="{{route('arcade-games-list')}}" class="section">Choices</a>
            <i class="right chevron icon divider"></i>
            <a href="#" class="section">Coice</a>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search ..." id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="arcade-list ui buttons fluid results" id="filterList">
            <div class="ui left labeled button  choices adv" id="" data-value="">
                <a href="#" class="ui basic label">
                    <span>C_1</span>
                    <p>Choice</p>
                </a>
                <button class="ui icon button sub_choice_edit">
                    <i class="edit outline icon"></i>
                </button>
                <button class="negative ui icon button arcade_storage_delete_button">
                    <i class="trash alternate icon"></i>
                </button>
            </div>
            <div class="ui left labeled button  choices adv" id="" data-value="">
                <a href="#" class="ui basic label">
                    <span>C_2</span>
                    <p>Choice</p>
                </a>
                <button class="ui icon button sub_choice_edit">
                    <i class="edit outline icon"></i>
                </button>
                <button class="negative ui icon button arcade_storage_delete_button">
                    <i class="trash alternate icon"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="edit" class="ten wide column">
        <div class="edit" id="arcade_storage_edit_create_zone">

        </div>
    </div>
    </button>
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        $(".sub_choice_edit").on('click', function () {
            let episodeId = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-games/choices-list/sub-choice-edit",
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
                    url: "/admin/arcade-games/choices-list/sub-choice-edit",
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

        $('.arcade_storage_delete_button').on('click', () => {
            $('.delete-modal').modal({
                onApprove: function(){

                }
            }).modal('show')
        })
    </script>

@endsection