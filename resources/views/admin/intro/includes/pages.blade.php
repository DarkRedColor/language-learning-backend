<div class=" content" id="content">
    @foreach($intros as  $key =>$item)
        @if($item->content_type == 3)
            <div class="{{$item["id"]}} accordion" id="choice">
                <div class="title choice">
                    <i class="chevron down icon"></i>
                    <button class="choice ui basic button grand" id="{{$item["id"]}}" data-value="{{$sceneId}}">
                        <span id="needed_choice_id">{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>Choice {{$loop->index + 1}}
                    </button>
                </div>


                <div class="content " id="content">


                    @include("admin.intro.includes.choice-recursive", [$item,$sceneId])

                </div>
            </div>
        @elseif($item->content_type == 0)
            <button class="line ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
                <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
            </button>

        @elseif($item->content_type == 1)
            <button class="action ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
                <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
            </button>

        @elseif($item->content_type == 2)
            <button class="notification ui basic button" id="{{$item["id"]}}" data-value="{{$sceneId}}">
                <span>{{$loop->index + 1}}{{$contentTypes[$item->content_type]}}</span>{{$item->text_en}}
            </button>

        @endif
    @endforeach

    <button class="add ui basic button add-page" id="add-page"
            next_id="null"
            initial="true"
            data-value="{{$sceneId}}"
            name="{{$item["id"] ?? "null"}}"><i
                class="plus icon"></i>ADD
        NEW PAGE
    </button>

</div>