@extends('admin.layouts.admin-intro-dashboard')
@section('embed')
    <script src="https://unpkg.com/vue@3.3.4"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
@endsection

@section('content')
    <div class="four wide column tree" id="admin_adventure_lists">
        <h1 id="adventure-h">Adventure</h1>
        <div id="filter" class="field">
            <label>Filter</label>
            <div class="ui selection dropdown">
                <input type="hidden" name="filter" value="{{$filter}}" id="adventure_filter">
                <div class="text">All</div>
                <div class="menu">
                    <div class="item default" data-value="all">All</div>
                    <div class="item" data-value="unlock">Unlock</div>
                </div>
            </div>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search" id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="ui small breadcrumb" id="adventures_path">
            <a href="{{route('season-index',$filter)}}" id="adventures_back_button"><i class="arrow left icon"></i></a>
            <a href="{{route('season-index',$filter)}}" class="section">Adventure</a>
            <a href="{{route('season-index',$filter)}}" class="section"><i
                        class="right chevron icon divider"></i>Season {{$season->order + 1}}</a>
        </div>
        <div class="ui buttons fluid" id="admin_seasons_list" id="filterList">
            @foreach($episodes as $item)
                <div class="{{$item->id}} ui left labeled button season adv episode_div_button"
                     data-value="{{$item->id}}"
                     id="{{$loop->index + 1}}">
                    @if($item->type == 0)
                    <a href="{{route('chapter-index',[$season->id,$item->id])}}"
                       class="ui basic label adventures_episode_entity_button">
                        <span style="background-color: var(--episode)">E {{$loop->index + 1}}</span>
                        <p>{{$item->name_en}}</p>
                    </a>
                    @else
                        <a href="{{route('sub-chapter-index',[$season->id,$item->id, $item->chapters[0]->id])}}"
                           class="ui basic label adventures_episode_entity_button">
                            <span style="background-color: var(--episode)">F {{$loop->index + 1}}</span>
                            <p>{{$item->name_en}}</p>
                        </a>
                    @endif
                    <button class="ui icon button episode_design" data-value="{{$item->id}}">
                        <i class="paint brush white icon"></i>
                    </button>

                    <button class="ui icon button episode_edit_button" @if($item->type == 0)data-value="0"@else data-value="1"@endif >
                        <i class="edit outline icon"></i>
                    </button>

                    @if($item->is_displayed_to_user == 1)
                        <button class="un-lock ui icon button episode_unlock" data-value="{{$item->id}}">
                            <i class="unlock alternate icon"></i>
                        </button>
                    @else
                        <button class="lock ui icon button episode_lock" data-value="{{$item->id}}">
                            <i class="lock alternate icon"></i>
                        </button>
                    @endif
                </div>
            @endforeach
            <button class="add ui basic button" id="add_new_episode_button" data-value="{{$season_id}}"><i
                        class="plus icon"></i>add new episode
            </button>
        </div>
    </div>

    <div id="edit" class="ten wide column">
        <div class="edit" id="season_edit_create_zone">

        </div>
    </div>
    </button>

    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        seasonId = {{$season_id}};
        $(document).ready(function () {
            fetch('{{ request()->root() }}/api/v2/seasons?list=' + seasonId)
                .then(response => response.json())
                .then(data => {
                    const episodes = data[0].episodes;

                    episodes.forEach(episode => {
                        const {id, transform} = episode;

                        if (transform && transform.episode_path) {
                            $('.episode_design').each(function () {
                                if (id === parseInt($(this).attr('data-value')))
                                    $(this).addClass('check')
                            })
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        })
        let appWrapper;
        $(".episode_design").on('click', function () {
            let id = $($(this).parent()[0]).attr('data-value');
            $.ajax({
                type: "get",
                url: "/admin/episode/constructor/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let form = $("#season_edit_create_zone");
                    form.empty();
                    $('script[src*="adventure_logic"]').remove();
                    if (appWrapper) {
                        appWrapper.innerHTML = '';
                    }
                    form.append(res);
                    appWrapper = document.createElement('div');
                    appWrapper.id = 'app';
                    form.append(appWrapper);
                    const appInstance = Vue.createApp({
                        data() {
                            return {
                                data: [],
                                showSuccessMessage: false,
                            };
                        },
                        methods: {
                            async getData() {
                                try {
                                    const response = await axios.get('{{ request()->root() }}/api/v2/seasons?icons_scale=S&list=' + seasonId);
                                    // const response = await axios.get('https://stage.langly.ai/api/v2/seasons?icons_scale=S&list=' + seasonId);
                                    this.data = response.data;
                                } catch (error) {
                                    console.error(error);
                                }
                            },
                            getEpisodeImg(image) {
                                if (image !== undefined && image !== null && image.trim() !== '') {
                                    return `background-image: url('${image}');`;
                                    console.log("have background")
                                } else {
                                    console.log("don't have background")
                                    return '';
                                }
                            },
                            getImgStyle(thumbnail, transform) {
                                if(thumbnail && transform){
                                    return `--img-background: url(${thumbnail});
                                          left: ${transform.chapter_image.position.x}%;
                                          top: ${transform.chapter_image.position.y}%;
                                          transform: rotate(${transform.chapter_image.rotation}deg);`;
                                }else{
                                    return  '';
                                }
                            },
                            getSceneStyle(transform) {
                                if(transform){
                                    return `left: ${transform.position.x}%;
                                          top: ${transform.position.y}%;`;
                                }else{
                                    return '';
                                }
                            },
                            getQuizStyle(transform) {
                                if(transform){
                                    return `left: ${transform.position.x}%;
                                          top: ${transform.position.y}%;`;
                                }else{
                                    return '';
                                }
                            },
                            hasIntroScenes(episode) {
                                return episode.chapters.some(chapter => chapter.intro_scenes.length > 0);
                            },
                            saveData() {
                                this.$nextTick(() => {
                                    const getTransformValue = (element) => {
                                        const transform = element.style.transform;
                                        if (transform && transform !== 'none') {
                                            const matches = transform.match(/rotate\(([-\d.]+)deg\)/);
                                            if (matches && matches[1]) {
                                                const rotation = parseFloat(matches[1]);
                                                return rotation;
                                            }
                                        }
                                        return 0;
                                    };

                                    const chapterElements = document.querySelectorAll('.img');
                                    const sceneElements = document.querySelectorAll('.chapter');
                                    const episodePath = $('#svgContainer path').attr('d');
                                    const episodeId = $('.adventure').attr('id');

                                    // const episodeId = episode.id;
                                    const episodePathUpdate = this.data.flatMap((season) => season.episodes).find((episode) => episode.id.toString() === episodeId);

                                    if (episodePathUpdate) {
                                        episodePathUpdate.transform = {
                                            episode_path: episodePath,
                                        };
                                    }

                                    chapterElements.forEach((chapterElement) => {
                                        const chapterId = chapterElement.id;
                                        const style = window.getComputedStyle(chapterElement);

                                        const parentElement = chapterElement.parentElement;
                                        const parentWidth = parentElement.offsetWidth;
                                        const parentHeight = parentElement.offsetHeight;

                                        const left = parseFloat((parseFloat(style.left) * 100 / parentWidth || 0).toFixed(2));
                                        const top = parseFloat((parseFloat(style.top) * 100 / parentHeight || 0).toFixed(2));

                                        const rotation = getTransformValue(chapterElement);

                                        const chapterToUpdate = this.data
                                            .flatMap((season) => season.episodes)
                                            .flatMap((episode) => episode.chapters)
                                            .find((chapter) => chapter.id.toString() === chapterId);

                                        if (chapterToUpdate) {
                                            chapterToUpdate.transform = {
                                                chapter_image: {
                                                    position: {
                                                        x: left,
                                                        y: top,
                                                    },
                                                    rotation: rotation,
                                                },
                                            };
                                        }
                                    });

                                    sceneElements.forEach((sceneElement) => {
                                        const sceneId = sceneElement.id;
                                        const style = window.getComputedStyle(sceneElement);
                                        const parentElement = sceneElement.parentElement;
                                        const parentWidth = parentElement.offsetWidth;
                                        const parentHeight = parentElement.offsetHeight;
                                        const left = parseFloat((parseFloat(style.left) * 100 / parentWidth || 0).toFixed(2));
                                        const top = parseFloat((parseFloat(style.top) * 100 / parentHeight || 0).toFixed(2));

                                        const sceneToUpdate = this.data
                                            .flatMap((season) => season.episodes)
                                            .flatMap((episode) => episode.chapters)
                                            .flatMap((chapter) => chapter.intro_scenes || chapter.default_scenes || chapter.quiz_scenes || [])
                                            .find((scene) => scene.id.toString() === sceneId);

                                        if (sceneToUpdate) {
                                            sceneToUpdate.transform = {
                                                position: {
                                                    x: left,
                                                    y: top,
                                                },
                                            };
                                        }
                                    });
                                    const payload = this.data;
                                    axios.post('{{ request()->root() }}/api/adventures', payload)
                                    // axios.post('https://stage.langly.ai/api/adventures', payload)
                                        .then((response) => {
                                            this.showSuccessMessage = true;
                                            setTimeout(() => {
                                                this.showSuccessMessage = false;
                                            }, 5000);
                                        })
                                        .catch((error) => {
                                            console.error('Error saving data:', error);
                                        });
                                });
                            },
                        },
                        mounted() {
                            this.getData()
                                .then(() => {
                                    var adenture = document.createElement('script');
                                    adenture.src = '{{asset("js/adventure_logic.js")}}';
                                    document.body.appendChild(adenture);
                                    return false;
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        },
                    }).mount('#app');

                },
            });
        })
        $("#add_new_episode_button").on('click', function () {
            let id = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/episode/create/" + id,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                $(".adv").removeClass('check')
                                let form = $("#season_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/episode/create/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $(".adv").removeClass('check')
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })


        $(".episode_edit_button").on('click', function () {
            let type = $(this).attr('data-value');
            let url = "/admin/episode/edit/";
            if (type == 1){
                url = "/admin/episode/final-quiz/edit/"
            }
            if ($('#episode_create_form').hasClass('contentChanged') || $('#episode_edit_form').hasClass('contentChanged')) {
                let id = $($(this).parent()[0]).attr('data-value');
                let position = $($(this).parent()[0]).attr('id');
                let path = $("#adventures_path");
                let seasonId = $('#add_new_episode_button').attr('data-value')
                let clickedButton = $($(this).parent()[0]);
                $(".basic-error").modal({
                    onApprove: function () {
                        if (path.hasClass('selected')) {
                            path.children().last().remove()
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Episode ' + position + '</a>')
                        } else {
                            path.addClass('selected');
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Episode ' + position + '</a>')
                        }

                        $(".episode_div_button").removeClass('check')
                        clickedButton.addClass('check')
                        $.ajax({
                            type: "get",
                            url: url + + id + '/' + seasonId,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#season_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show');
            } else {
                let id = $($(this).parent()[0]).attr('data-value');
                let position = $($(this).parent()[0]).attr('id');
                let path = $("#adventures_path");
                let clickedButton = $($(this).parent()[0]);
                let seasonId = $('#add_new_episode_button').attr('data-value');
                if (path.hasClass('selected')) {
                    path.children().last().remove()
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Episode ' + position + '</a>')
                } else {
                    path.addClass('selected');
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Episode ' + position + '</a>')
                }


                $(".episode_div_button").removeClass('check')
                clickedButton.addClass('check')
                $.ajax({
                    type: "get",
                    url: url + id + '/' + seasonId,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }

        })
        //
        // function entityPass(id) {
        //     let filter = $("#adventure_filter").val()
        //     let seasonId = $("#add_new_episode_button").attr('data-value')
        //     if ($('#episode_create_form').hasClass('contentChanged') || $('#episode_edit_form').hasClass('contentChanged')) {
        //         $('#entity_pass_modal').modal({
        //             closable: false,
        //             onApprove: function () {
        //                 location.href = '/admin/episode/chapters/' + seasonId + "/" + id + "/" + filter ;
        //             }
        //         }).modal('show')
        //     } else {
        //         location.href = '/admin/episode/chapters/' + seasonId + "/" + id + "/" + filter;
        //
        //     }
        // }
        // $('#adventure_filter').on('change',function (){
        //     let filter = $("#adventure_filter").val()
        //     let seasonId = $("#add_new_episode_button").attr('data-value')
        //     location.href = '/admin/season/episodes/' + seasonId + "/" + filter ;
        // })

        $(".episode_unlock").on('click', function () {
            let id = $(this).attr('data-value');
            let elem = $(this);
            let closed = 0;

            if (elem.hasClass("lock")) {
                closed = 1;
            }
            $(".state-hidden").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/episode/lock-unlock/" + id + "/" + closed,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            if (closed === 0) {
                                elem.removeClass("un-lock");
                                elem.addClass("lock")
                                let child = elem.children()[0];
                                $(child).removeClass('unlock');
                                $(child).addClass('lock');
                            } else {
                                elem.removeClass("un-lock");
                                elem.addClass("lock")
                                let child = elem.children()[0];
                                $(child).removeClass('lock');
                                $(child).addClass('unlock');
                            }
                        },
                    });
                }
            }).modal('show');

        })
        $(".episode_lock").on('click', function () {
            let id = $(this).attr('data-value');
            let elem = $(this);
            let closed = 0;

            if (elem.hasClass("lock")) {
                closed = 1;
            }
            $(".state-visible").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/episode/lock-unlock/" + id + "/" + closed,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            if (closed === 0) {
                                elem.removeClass("un-lock");
                                elem.addClass("lock")
                                let child = elem.children()[0];
                                $(child).removeClass('unlock');
                                $(child).addClass('lock');
                            } else {
                                elem.removeClass("lock");
                                elem.addClass("un-lock")
                                let child = elem.children()[0];
                                $(child).removeClass('lock');
                                $(child).addClass('unlock');
                            }
                        },
                    });
                }
            }).modal('show');

        })
    </script>
@endsection