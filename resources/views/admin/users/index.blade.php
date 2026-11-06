@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="fourteen wide column tree ui grid">
        <h1 id="adventure-h">Workspace Members</h1>
        <div class="ui search four wide column">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Type something">
                <i class="search icon"></i>
            </div>
        </div>
        <p>EMAIL</p>
        <div id="filterList" class="character-list ui buttons fluid emails">
            @foreach($users as $user)
                @switch($user->getRoleNames()->first())
                    @case('admin')
                        <div class="char ui left labeled button action adv grid">
                            <a href="#" class="ui basic item label four wide column" data-value="">
                                <span><i class="chess queen icon large"></i></span>

                                <p>{{$user->email}}</p>
                                <button class="ui icon button negative trash-button" data-id="{{$user->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            </a>
                            <div class="ui fluid selection dropdown four wide column">
                                <input type="hidden" name="role_id"
                                       class="role_id"
                                       value="{{$roles->where('name',$user->getRoleNames()->first())->first()->id}}">
                                <i class="dropdown icon"></i>
                                <div class="default text">No role</div>
                                <div class="menu">
                                    @foreach($roles as $role)
                                        <div class="item"
                                             data-value="{{$role->id}}">{{Str::title($role->name)}}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button class="ui positive button"
                                    id="{{$user->id}}">Apply
                            </button>
                        </div>
                        @break
                    @case('user')
                    @case('qa')
                        <div class="char ui left labeled button choices adv grid">
                            <a href="#" class="ui basic item label four wide column" data-value="">
                                <span><i class="user icon large"></i></span>

                                <p>{{$user->email}}</p>
                                <button class="ui icon button negative trash-button" data-id="{{$user->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            </a>
                            <div class="ui fluid selection dropdown four wide column">
                                <input type="hidden" name="role_id"
                                       class="role_id"
                                       value="{{$roles->where('name',$user->getRoleNames()->first())->first()->id}}">
                                <i class="dropdown icon"></i>
                                <div class="default text">No role</div>
                                <div class="menu">
                                    @foreach($roles as $role)
                                        <div class="item"
                                             data-value="{{$role->id}}">{{Str::title($role->name)}}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button class="ui positive button"
                                    id="{{$user->id}}">Apply
                            </button>
                        </div>
                        @break
                    @case('project')
                        <div class="char ui left labeled button line adv grid">
                            <a href="#" class="ui basic item label four wide column"
                               data-value="">
                                <span><i class="eye icon large"></i></span>

                                <p>{{$user->email}}</p>
                                <button class="ui icon button negative trash-button" data-id="{{$user->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            </a>
                            <div class="ui fluid selection dropdown four wide column">
                                <input type="hidden" name="role_id"
                                       class="role_id"
                                       value="{{$roles->where('name',$user->getRoleNames()->first())->first()->id}}">
                                <i class="dropdown icon"></i>
                                <div class="default text">No role</div>
                                <div class="menu">
                                    @foreach($roles as $role)
                                        <div class="item"
                                             data-value="{{$role->id}}">{{Str::title($role->name)}}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button class="ui positive button"
                                    id="{{$user->id}}">Apply
                            </button>
                        </div>
                        @break
                    @case('content')
                        <div class="char ui left labeled button notification adv grid">
                            <a href="#" class="ui basic item label four wide column"
                               data-value="">
                                <span><i class="edit icon large"></i></span>

                                <p>{{$user->email}}</p>
                                <button class="ui icon button negative trash-button" data-id="{{$user->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            </a>
                            <div class="ui fluid selection dropdown four wide column">
                                <input type="hidden" name="role_id"
                                       class="role_id"
                                       value="{{$roles->where('name',$user->getRoleNames()->first())->first()->id}}">
                                <i class="dropdown icon"></i>
                                <div class="default text">No role</div>
                                <div class="menu">
                                    @foreach($roles as $role)
                                        <div class="item"
                                             data-value="{{$role->id}}">{{Str::title($role->name)}}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button class="ui positive button"
                                    id="{{$user->id}}">Apply
                            </button>
                        </div>
                        @break
                    @case('developer')
                        <div class="char ui left labeled button notification adv grid">
                            <a href="#" class="ui basic item label four wide column"
                               data-value="">
                                <span><i class="code icon large"></i></span>

                                <p>{{$user->email}}</p>
                                <button class="ui icon button negative trash-button" data-id="{{$user->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            </a>
                            <div class="ui fluid selection dropdown four wide column">
                                <input type="hidden" name="role_id"
                                       class="role_id"
                                       value="{{$roles->where('name',$user->getRoleNames()->first())->first()->id}}">
                                <i class="dropdown icon"></i>
                                <div class="default text">No role</div>
                                <div class="menu">
                                    @foreach($roles as $role)
                                        <div class="item"
                                             data-value="{{$role->id}}">{{Str::title($role->name)}}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button class="ui positive button"
                                    id="{{$user->id}}">Apply
                            </button>
                        </div>
                        @break
                    @default
                        <div class="char ui left labeled button error adv grid">
                            <a href="#"
                               class="ui basic item label four wide column"
                               data-value="">
                                <span><i class="inverted white user secret icon large"></i></span>

                                <p>{{$user->email}}</p>
                                <button class="ui icon button negative trash-button" data-id="{{$user->id}}">
                                    <i class="trash alternate icon"></i>
                                </button>
                            </a>
                            <div class="ui fluid selection dropdown four wide column">
                                <input type="hidden" name="role_id"
                                       class="role_id"
                                       value="@if($user->getRoleNames()->first() !== null){{$roles->where('name',$user->getRoleNames()->first())->first()->id}}@endif">
                                <i class="dropdown icon"></i>
                                <div class="default text">No role</div>
                                <div class="menu">
                                    @foreach($roles as $role)
                                        <div class="item"
                                             data-value="{{$role->id}}">{{Str::title($role->name)}}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button class="ui positive button"
                                    id="{{$user->id}}">Apply
                            </button>
                        </div>
                        @break
                @endswitch

            @endforeach
        </div>
    </div>
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        $('.ui.selection.dropdown').dropdown({
            clearable: true
        })

        $('.ui.selection.dropdown').on('change', function () {
            $(this).addClass('changed');
            $(this).parent().addClass('check');
            $(this).siblings('.ui.positive.button').show();
            if ($(this).parent().hasClass('check')) {
                $(this).parent().siblings().removeClass('check');
                $(this).parent().siblings().children('.ui.positive.button').hide();
            }
        })
        $('.char').on('click', function () {
            $(this).addClass('check')
            $(this).siblings().removeClass('check')
            if ($(this).children('.ui.selection.dropdown').hasClass('check')) {
                $(this).children('.ui.positive.button').show()
            }
        })
        $('.trash-button').on('click', function () {
            var id = $(this).data('id');
            $(".delete-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: 'POST',
                        url: '/admin/user/delete/' + id,
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
        $('.ui.positive.button').on('click', function () {
            var id = $(this).attr('id');
            var roleId = $(this).parent().children('.fluid').children('.role_id').attr('value');
            var bodyData = JSON.stringify({"role_id": roleId})
            $(this).hide()
            $.ajax({
                type: "post",
                url: "/admin/user/update/" + id,
                data: bodyData,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    location.reload();
                }
            })

        })
        //snow options
        // snowFall.snow(document.body, {
        //     //round : true,
        //     flakeCount: 50,
        //     minSize: 15,
        //     maxSize: 25,
        //     minSpeed: 1,
        //     maxSpeed: 3,
        //     image: '../../../public/img/coin.png'
        //     //flakeColor: '#98ccf2'
        // });

    </script>

@endsection
