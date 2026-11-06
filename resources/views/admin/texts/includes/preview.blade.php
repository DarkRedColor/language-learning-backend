<div class="edit">
    <h1>Preview</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Preview localizations</h3>
    </div>
    <div class="localization">
        <div class="local-wrap">
            @foreach($arr as $item)
                <ul>
                    <li><h3>{{ $item['lcid'] }}</h3></li>
                    @foreach($item['words'] as $word)

                        <li>{{$word['name_en']}}</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</div>
