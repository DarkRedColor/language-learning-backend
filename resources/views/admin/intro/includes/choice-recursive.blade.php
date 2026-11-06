@foreach($item['frames'] as $item)

    @if($item->content_type == 3)
        <div class="{{$item["id"]}} accordion" id="choice">
            <div class="title choice">
                <i class="chevron down icon"></i>
                <button class="choice ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
                    <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
                </button>
            </div>

            <div class="content" id="content">
{{--                <div class="accordion transition visible content">--}}

                    @include("admin.intro.includes.choice-recursive", [$item, $item['id']])

                    @if(count($item['frames']) > $loop->index + 1)
                        <button class="add ui basic button add-page" id="add-page"
                                next_id="{{$item['frames'][$loop->index +1]['id']}}"
                                initial="false"
                                data-value="{{$sceneId}}"
                                name="{{$item["id"]}}"><i
                                    class="plus icon"></i>ADD
                            NEW PAGE
                        </button>
                    @else
                        <button class="add ui basic button add-page" id="add-page"
                                next_id="null"
                                initial="false"
                                data-value="{{$sceneId}}"
                                name="{{$item["id"]}}"><i
                                    class="plus icon"></i>ADD
                            NEW PAGE
                        </button>
                    @endif
                </div>
            </div>
{{--        </div>--}}
    @endif

    @if($item->content_type == 0)
        <button class="line ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
            <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
        </button>
    @endif

    @if($item->content_type == 1)
        <button class="action ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
            <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
        </button>
    @endif

    @if($item->content_type == 2)
        <button class="notification ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
            <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
        </button>

    @endif

@endforeach
