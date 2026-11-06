@foreach($seasons as $item)
    <div class="ui left labeled button season adv check">
        <a href="#" class="ui basic label">
            <span>S {{$loop->index + 1}}</span>
            <p>{{$item->description_en}}</p>
        </a>
        <button class="ui icon button">
            <i class="edit outline icon"></i>
        </button>
        <button class="un-lock ui icon button">
            <i class="unlock alternate icon"></i>
        </button>
    </div>
@endforeach