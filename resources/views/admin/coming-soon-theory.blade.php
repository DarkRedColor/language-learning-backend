<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <form class="ui form">
        @csrf
        <div class="option-area theory-option">
            <div class="ui selection dropdown" style="background: var(--{{$type}})">
                <label>TYPE: </label>
                <input type="hidden" name="type" value="{{$type}}" id="theory_type_select">
                <div class="text" >Word</div>
                <div class="menu">
                    <div class="item" data-value="word">Word</div>
                    <div class="item" data-value="topics">Topics</div>
                    <div class="item" data-value="grammar">Grammar</div>
                </div>
                <i class="chevron down icon"></i>
            </div>
        </div>
        <div class="edit-area  adventure soon">
            <h1>Coming soon</h1>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>

<script>
    $('#theory_type_select').on('change', function () {
        let type = $(this).val();
        $.ajax({
            type: "get",
            url: "/admin/theory/create/" + type,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let form = $("#edit");
                form.empty();
                form.append(res)
            },
        });
    })
</script>