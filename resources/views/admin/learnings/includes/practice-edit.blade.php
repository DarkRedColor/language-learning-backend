<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Select game description</h3>
    </div>
    <div class="ui form">
        <div class="edit-area grid">
            <form action="{{route('learning-practice-update',[$templates->id,$learningId,$practiceId])}}"
                  class="ui form learning add-words"
                  method="post">
                @csrf
                <input type="hidden" name="last_des" value="{{$description->game_description_id ?? null}}">
                <div class="field eight wide">
                    <label>Game description</label>
                    <div class="ui selection search dropdown">
                        <input type="hidden" name="description" value="{{$description->game_description_id ?? null}}">
                        @php
                            $des= 'select something';
                            $elem = null;
                            if(isset($description)){
                                $elem = $descriptions->find($description->game_description_id);
                            }
                            if($elem != null){
                                $des = $elem->name;
                            }
                        @endphp

                        <div class="default text">{{$description}}</div>
                        <div class="menu">
                            @foreach($descriptions as $item)
                                <div class="item" data-value="{{$item->id}}">{{$item->description_en}}</div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div class="buttons action-buttons">
                    <button type="submit" class="positive ui button save" disabled>SAVE</button>
                    <button type="button" class="grey ui button dis" disabled>DISCARD</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}}></script>
<script>
    $('.form').on('change', function () {
        $(this).addClass('contentChanged');
        $('.button.dis, .button.save').attr('disabled', false);
    });
    $('.button.dis').on('click', function () {
        $('.discard-modal').modal({
            onApprove() {
                $.ajax({
                    type: "get",
                    url: "/admin/learning/practice/edit/{{$templates->id}}/{{$learningId}}/{{$practiceId}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#edit");
                        text.empty();
                        text.append(res);
                    }
                })
            }
        }).modal('show')
    })
</script>