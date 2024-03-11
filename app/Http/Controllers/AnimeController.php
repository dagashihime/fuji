<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowAnimeRequest;
use App\Models\AnimeListItem;
use Illuminate\Contracts\View\View;

class AnimeController extends Controller
{
    public function index(): View
    {
        $animeListItems = AnimeListItem::select('*')
            ->where('user_id', auth()->user()->id)
            ->with('anime', fn($q)=> $q->with('genres'))
            ->get();
    
        return view('anime.index', compact('animeListItems'));
    }

    public function show(ShowAnimeRequest $request): View
    {
        $animeListItem = AnimeListItem::select('*')
            ->where('user_id', auth()->user()->id)
            ->where('anime_id', $request->id)
            ->with('anime', fn($q)=> $q->with('genres'))
            ->first();

        return view('anime.show', $animeListItem);
    }
}
