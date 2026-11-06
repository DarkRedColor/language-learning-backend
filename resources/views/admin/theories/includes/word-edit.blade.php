<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <form action="{{route('theory-update',$theory->id)}}" class="ui form" method="post" id="theory_form">
        @csrf
        <div class="option-area theory-option">
            <div class="ui selection dropdown" style="background: var(--word)">
                <label>TYPE: </label>
                <input type="hidden" name="type" value="word" id="theory_type_select">
                <div class="text">Word</div>
                {{--                <div class="menu">--}}
                {{--                    <div class="item" data-value="word">Word</div>--}}
                {{--                    <div class="item" data-value="topics">Topics</div>--}}
                {{--                    <div class="item" data-value="grammar">Grammar</div>--}}
                {{--                </div>--}}
                {{--                <i class="chevron down icon"></i>--}}
            </div>
        </div>
        <div class="edit-area">
            <input type="hidden" name="tags" value="" id="words_tags">
            <div class="two fields">
                <div class="ui field">
                    <label>Name Words’ Theory</label>
                    <input placeholder="Word name" type="text" class="not-null-input" name="name"
                           value="{{$theory->name}}">
                </div>
                <div class="ui field">
                    <div class="field">
                        <label>Tags theory</label>
                        <div id="tags">
                            @if(isset($theory->tags))
                                @foreach($theory->tags as $item)
                                    <span>{{$item}}</span>
                                @endforeach
                            @endif
                            <input type="text" placeholder="Add a tag"/>
                            <input class="hidden"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="button" href="#" class="positive ui button save" disabled="disabled">SAVE</button>
                <button class="grey ui button discard" type="button" disabled="disabled"> DISCARD
                </button>
                <button type="button" class="negative ui button delete @if(!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" id=""> DELETE PAGE
                </button>
            </div>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready(function () {
        $('.button.save, .button.discard').attr('disabled', true)
        $('.button.discard').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $(".discard-modal").modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/theory/edit/{{$theory->id}}",
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
                        $('.form').removeClass('contentChanged');
                        $('.button.save, .button.discard').attr('disabled', true);
                    }
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/theory/edit/{{$theory->id}}",
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
    });
    // add class && show button
    $('.form input[name]').not('.hidden').on('keyup change', function () {
        if ($(this).val() !== '') {
            $('.form').addClass('contentChanged')
            $('.button.save, .button.discard').attr('disabled', false)
        } else {

            $('.button.save, .button.discard').attr('disabled', true)
        }
    })
    // $("body").on('DOMSubtreeModified', "#tags", function () {
    //     if ($(this).val() !== '') {
    //         $('.form').addClass('contentChanged')
    //         $('.button.save, .button.discard').attr('disabled', false)
    //     } else {
    //
    //         $('.button.save, .button.discard').attr('disabled', true)
    //
    //     }
    // });

    $(function () {
        $("#tags input").on({
            focusout: function () {
                var txt = this.value.replace(/[^a-z0-9\+\-\.\,\ ]/ig, ''); // allowed characters
                if (txt) $("<span/>", {text: txt.toLowerCase(), insertBefore: this});
                this.value = "";
            },
            keyup: function (ev) {
                // if: comma|enter (delimit more keyCodes with | pipe) /(188|13)/
                if (/(13)/.test(ev.which)) $(this).focusout();
                $('.button.save, .button.discard').attr('disabled', false);
                $('.form').addClass('contentChanged');
            }
        });
        $('#tags').on('click', 'span', function () {
            // if(confirm("Remove "+ $(this).text() +"?"))
            $(this).remove();
            $('.button.save, .button.discard').attr('disabled', false);
            $('.form').addClass('contentChanged');
        });
    })

    $(".save.button").on('click', function () {
        let arr = [];
        $('#tags span').each(function () {
            arr.push($(this).text());
        });
        $('#words_tags').val(JSON.stringify(arr));
        $('#theory_form').submit();
    })

    $('.button.delete:not(.lena)').on('click', function () {
        $(".delete-modal").modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/theory/delete/{{$theory->id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    },
                });
            }
        }).modal('show');

    })

</script>
