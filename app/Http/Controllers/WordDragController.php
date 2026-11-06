<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Language;
use App\Models\SubChapter;
use App\Models\Word;
use Illuminate\Http\Request;

class WordDragController extends Controller
{


    public function assignPost($id, Request $request)
    {
        $ids = $request->all();
        $sub = SubChapter::find($id);
        $wordsId = $sub->words()->get();

        foreach ($wordsId as $item) {
            $sub->words()->detach([
                'word_id' => $item['id'],
            ]);
        }

        for ($i = 0; $i < count($ids['ids']); $i++) {
            $sub->words()->attach([
                'word_id' => $ids['ids'][$i],
            ]);
        }

        return response('success', 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index($season_id, $episode_id, $chapter_id, $sub_chapter_id)
    {
        $servisesG = new Services\Games();
        $servisesW = new Services\Words();
        $theory = $servisesG->theoriesGet();
        $words = SubChapter::find($sub_chapter_id)->words()->orderBy('word')->get();
        $allWords = Word::orderBy('word')->get();
        $types = $servisesG->gameTypeGet();
        $languages = Language::get();
        $games = Game::where('sub_chapter_id', $sub_chapter_id)
            ->orderBy('order', 'asc')
            ->get();
        $freeGames = Game::where('sub_chapter_id', null)->get();
        $subChapter = SubChapter::where('id', $sub_chapter_id)->get();
        $gameList = $servisesG->list($games, $languages);
        $freeGameList = $servisesG->list($freeGames, $languages);
        $wordList = $servisesW->list($words, $languages);
        $freeWordList = $servisesW->list($allWords, $languages);

        return view('admin.words.drag-and-drop-tables.index')->with(compact(
            'freeGameList',
            'subChapter',
            'gameList',
            'languages',
            'season_id',
            'episode_id',
            'chapter_id',
            'sub_chapter_id',
            'types',
            'games',
            'freeGames',
            'theory',
            'words',
            'allWords',
            'wordList',
            'freeWordList'
        ));

    }
}
