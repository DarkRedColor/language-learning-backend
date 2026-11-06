<div class="_append" style="display:flex;flex-direction: column; overflow: scroll;height:1000px ">
    <ul>
        @if(count($datas) == 0)
            <li style="font-size: 20px">not found</li>
        @else
            <li>Transcription:</li>
            @foreach($datas['transcription'] as $item)
                <li>{{$item}}</li>
            @endforeach
            @foreach($datas as $key => $item)
                @if($key == "transcription")
                    @continue
                @else
                    <li style="margin-top: 25px">Lang: {{$key}}</li>
                    <li>Meaning:</li>
                    @foreach($item['meaning'] as $aux)
                        <ol>{{$loop->index}}:{{$aux}}</ol>
                    @endforeach
                @endif
            @endforeach
        @endif
    </ul>
</div>