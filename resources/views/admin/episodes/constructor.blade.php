<link rel="stylesheet" href="{{asset('css/adventure_new.css')}}">

<div id="app">
    <aside>
        <button type="button" @click="saveData">Save data</button>
    </aside>
    <div v-if="showSuccessMessage" class="success-message">
        Saved successfully
    </div>
    <div id="map-wrap" data-value="">
        <template v-for="season in data">
            <template v-for="episode in season.episodes.slice().reverse()">
                <template v-if="episode.id === {{$id}}">
                    <h2 :id="episode.description"></h2>
                    @include('admin.episodes.includes.adventure_map')
                </template>
            </template>
        </template>
    </div>
</div>




