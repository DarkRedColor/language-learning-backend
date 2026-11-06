@foreach($texts as $item)
    <a href="#" class="ui basic item label textsbuttons" id="{{$item->id}}">
        <p>{{$item->name_en}}</p>
    </a>
@endforeach

<script>
    $('.textsbuttons').on('click', function () {
        let id = $(this).attr('id');
        let button = $(this);
        if ($("#text_update_form").hasClass('contentChanged')) {
            $('#discard_modal').modal({
                onApprove: function () {
                    $('.textsbuttons').removeClass('check')
                    button.addClass('check')
                    $.ajax({
                        type: "get",
                        url: "/admin/text/edit/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let text = $("#text_modal_part");
                            text.empty();
                            text.append(res);
                        }

                    })
                }
            }).modal('show')
        } else {
            $('.textsbuttons').removeClass('check')
            button.addClass('check')
            $.ajax({
                type: "get",
                url: "/admin/text/edit/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let text = $("#text_modal_part");
                    text.empty();
                    text.append(res);
                }
            })
        }
    })
</script>