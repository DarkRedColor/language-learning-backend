<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area grammar">
        <h3>Grammar</h3>
    </div>
    <div class="edit-area">
        <form action="{{route('theory-update',$theory->id)}}" class="ui form" method="post" id="theory_form">
            @csrf
            <input name="example_list" value="" hidden/>
            <input type="hidden" name="type" value="grammar" id="theory_type_select">
            <div class="two fields">
                <div class="ui field">
                    <div class="ui field accordion">
                        <div class="title inputs">
                            <div class="field main-local">
                                <label>Name Grammar’s Theory</label>
                                <textarea class="textarea" rows="2" name="description_en"
                                          id="theory_textarea">{{$theory->description_en}}</textarea>
                                <div class="ui label lang-iso">EN
                                    <i class="chevron down icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="content field inputs">
                            @foreach($languages as $item)
                                @if($item->lcid <> 'en')
                                    @php
                                        $localisationName = 'description_' . $item->lcid;
                                    @endphp
                                    <div class="field">
                                        <textarea class="textarea" rows="2" name="description_{{$item->lcid}}"
                                                  data-value="{{$theory->$localisationName}}">{{$theory->$localisationName}}</textarea>
                                        <div class="ui label lang-iso">{{$item->lcid}}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="ui field">
                    <input type="hidden" name="tags" value="" id="words_tags">
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
            <div class="buttons action-buttons">
                <button type="button" href="#" class="positive ui button save" disabled >SAVE</button>
                <button class="grey ui button discard-theory"  disabled type="button" data-value="{{$theory->id}}"> DISCARD
                </button>
                <button type="button" class="negative ui button delete topic-grammar @if (!Auth::user()->hasRole(['admin','qa','project','developer','methodologist']))lena
                @endif" id="" data-value="{{$theory->id}}"> DELETE PAGE
                </button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready(function () {
        // add class && show button
        $('.form').on('keyup', function () {
            if ($(this).find('textarea').val() !== '') {
                $('.form').addClass('contentChanged')
                $('.button.save, .discard-theory').attr('disabled', false)
            } else {
                $('.button.save, .discard-theory').attr('disabled', true)
            }
        })
    })

        $("#tags input").on({
            focusout: function () {
                var txt = this.value.replace(/[^a-z0-9\+\-\.\,\ ]/ig, ''); // allowed characters
                if (txt) $("<span/>", {text: txt.toLowerCase(), insertBefore: this});
                this.value = "";
            },
            keyup: function (ev) {
                // if: comma|enter (delimit more keyCodes with | pipe) /(188|13)/
                if (/(13)/.test(ev.which)) $(this).focusout();
                $('.form').addClass('contentChanged');
            }
        });
        $('#tags').on('click', 'span', function () {
            $(this).remove();
            $('.form').addClass('contentChanged');
            $('.button.save, .discard-theory').attr('disabled', false);
        });


    $(".save.button").on('click', function () {
        let arr = [];
        $('#tags span').each(function () {
            arr.push($(this).text());
        });
        $('#words_tags').val(JSON.stringify(arr));
        $('#theory_form').submit();
    })
    $('.button.discard-theory').on('click', function () {
        let id = $(this).attr('data-value');
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "get",
                        url: "/admin/theory/edit/" + id,
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
            }).modal('show')
        } else {
            $.ajax({
                type: "get",
                url: "/admin/theory/edit/" + id,
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
    $('.topic-grammar:not(.lena)').on('click', function () {
        let id = $(this).attr('data-value');
        $(".delete-modal").modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/theory/topic-grammar/delete/" + id,
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