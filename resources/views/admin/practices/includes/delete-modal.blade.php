<label>Link with another practice</label>
<div class="ui selection search dropdown">
    <input type="hidden" class="practice-relink" name="practice_name" value="">
    <div class="default text">practice</div>
    <div class="menu">
    @foreach($practices as $item)
            <div class="item" data-value="{{$item->id}}">{{$item->name}}</div>
        @endforeach
    </div>
    <i class="chevron down icon"></i>
</div>
<script type="text/javascript" src="{{asset('js/main.js')}}"></script>
