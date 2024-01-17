<?php

namespace App\Http\Controllers;

use App\Models\AnimeListItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    public function show(Request $request): View
    {
        dd($request->slug);

        return view('anime.show');
    }
}
