$(document).ready(function (){
let personage_id_elem = $('#personage_id')

    if (personage_id_elem.val() !== null) {
        let id = personage_id_elem.val();
        let data = JSON.stringify({
            id
        })
        $('#emotion_value').val(null);

        $.ajax({
            type: "post",
            url: "/admin/story/emotions/get",
            data: data,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let emo = $('#emotion');
                emo.empty();
                $.each(res, function (key, value) {
                    emo.prepend('<div class="item" data-value="' + key + '">' + value + '</div>')
                });
            }
        });
    }
    personage_id_elem.on('change', function () {
        let id = $(this).val();
        let data = JSON.stringify({
            id
        })
        $('#emotion_value').val(null);

        $.ajax({
            type: "post",
            url: "/admin/story/emotions/get",
            data: data,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let emo = $('#emotion');
                emo.empty();
                $.each(res, function (key, value) {
                    emo.prepend('<div class="item" data-value="' + key + '">' + value + '</div>')
                });
            }
        });
    })
    });