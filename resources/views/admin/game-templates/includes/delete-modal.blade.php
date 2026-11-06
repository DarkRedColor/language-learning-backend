<label>Link with another game</label>
<div class="ui selection search dropdown">
    <input type="hidden" class="game-relink" name="practice_name" value="">
    <div class="default text">practice</div>
    <div class="menu">
    @foreach($templates as $template)
            <div class="item" data-value="{{$template->id}}">{{$template->name}}</div>
        @endforeach
    </div>
    <i class="chevron down icon"></i>
</div>