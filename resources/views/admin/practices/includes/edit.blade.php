<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <form action="{{route('practice-update', $practice->id)}}" class="ui form" method="post" id="practice_form">
        @csrf
        <div class="option-area theory-option">
            <div class="ui selection dropdown" style="background: var(--action)">
                <label>TYPE: </label>
                <div class="text">Practice</div>
            </div>
        </div>
        <div class="edit-area">
            <div class="two fields">
                <div class="ui field">
                    <label>Name Practice</label>
                    <input placeholder="Word name" type="text" name="name" value="{{$practice->name}}">
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" disabled>SAVE</button>
                <button type="button" class="grey ui button" id="practice_discard" disabled="">DISCARD</button>
                <button type="button" class="negative ui button @if (!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" id="practice_delete" data-value="{{$practice->id}}">DELETE</button>
            </div>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    // add class && show button
    $('.form input[name]').not('.hidden').on('keyup change', function () {
        if ($(this).val() !== '') {
            $('.form').addClass('contentChanged')
            $('.button.save').attr('disabled', false)
            $('#practice_discard').attr('disabled', false)
        } else {
            $('.button.save').attr('disabled', true)
        }
    })

    $('#practice_discard').on('click', function () {
        if ($('#practice_form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    $.ajax({
                        type: "get",
                        url: "/admin/practice/edit/{{$practice->id}}",
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
                }
            }).modal('show');
        } else {
            $.ajax({
                type: "get",
                url: "/admin/practice/edit/{{$practice->id}}",
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
        }
    })
    $('#practice_delete').not('.lena').on('click', function () {
        let id = $(this).attr('data-value');
        $.ajax({
            type: "get",
            async: false,
            url: "/admin/practice/modal/delete/" + id,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let form = $('.delete-modal');
                form.empty();
                form.append(res);
                let input = $('.practice-relink');
                let val = input.val();
                input.on('change', function () {
                    val = $(this).val();
                })
                $('.game-modal').modal({
                    closable: false,
                    onApprove: function () {
                        $.ajax({
                            type: "post",
                            async: false,
                            url: "/admin/practice/delete/" + id + "/" + val,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function () {
                                location.reload();
                            }
                        })
                    }
                }).modal('show');
            }
        });
    })
</script>
