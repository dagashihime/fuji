<?php

use App\Hooks\MAL;
use App\Models\Anime;
use App\Models\AnimeGenre;
use App\Models\AnimeGenreRelation;
use App\Models\AnimeListItem;
use App\Models\PkceConnection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('/user')->group(function () {
        Route::prefix('/connections')->group(function() {
            Route::get('/', function () {
                $connections = auth()->user()->pkce_connections;
    
                return view('connections.index', compact('connections'));
            })->name('connections.index');

            Route::prefix('/mal')->group(function() {
                Route::post('/connect', function() {
                    $verifierBytes = random_bytes(64);
                    $codeVerifier = session()->get('codeVerifier') ?? rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
            
                    session()->put('codeVerifier', $codeVerifier);
            
                    // MAL only supports 'plain' PKCE    
                    // $challengeBytes = hash("sha256", $codeVerifier, true);
                    // $codeChallenge = rtrim(strtr(base64_encode($challengeBytes), "+/", "-_"), "=");
            
                    $params = collect([
                        "response_type" => "code",
                        "client_id" => env('MAL_CLIENT_ID'),
                        "redirect_uri" => route('api.mal.auth'),
                        "code_challenge" => $codeVerifier,
                        "code_challenge_method" => "plain"
                    ])->map(fn($value,$key)=> "$key=$value")->implode('&');

                    return "https://myanimelist.net/v1/oauth2/authorize?$params";
                });
                Route::post('/disconnect', function() {
                    $connection = auth()->user()->pkce_connections->where('domain', 'https://api.myanimelist.net')->first();
                    $connection->delete();

                    return null;
                });
            });
        });
    });

    Route::get('/anime', function() {
        $animeListItems = AnimeListItem::select('*')
            ->where('user_id', auth()->user()->id)
            ->with('anime', fn($q)=> $q->with('genres'))
            ->get();
    
        return view('anime', compact('animeListItems'));
    });

    Route::prefix('/api/v1')->group(function () {
        Route::get('/mal-auth', function(Request $request) {
            $response = MAL::authorize($request->get('code'));
            $userId = auth()->user()->id;

            try {
                $pkce = new PkceConnection;
                $pkce->user_id = $userId;
                $pkce->type = 'plain';
                $pkce->domain = 'https://api.myanimelist.net';
                $pkce->access_token = $response['access_token'];
                $pkce->refresh_token = $response['refresh_token'];
                $pkce->expires_at = Carbon::parse( now()->getPreciseTimestamp(0) + $response['expires_in'] );
                $pkce->save();
            } catch(Exception $e) {
                // @ToDo: add log
                dd($e);
            }

            [$list] = MAL::getUserList();

            $data = collect($list['data']);

            $fetchedGenres = $data->map(fn($v)=> $v['node']['genres'])
                ->collapse()
                ->keyBy('id');

            $genres = AnimeGenre::whereIn('title', $fetchedGenres->pluck('name'))->get();
            
            if($genres->count() < $fetchedGenres->count()) {
                $genres = $fetchedGenres->map(function($genre) use($genres) {
                    $animegenre = $genres->where('title', $genre['name'])->first();
                    if(!$animegenre) {
                        $animegenre = new AnimeGenre;
                        $animegenre->title = $genre['name'];
                        $animegenre->mal_id = $genre['id'];
                        $animegenre->save();
                    }
                    return $animegenre;
                });
            }

            $animeTitles = $data->map(fn($v)=> $v['node']['title']);
            $anime = Anime::whereIn('title', $animeTitles)->get();

            $saveAnime = $anime->count() < $animeTitles->count();

            $animeListItems = AnimeListItem::whereIn('anime_id', $anime->pluck('id'))->get();

            if($animeListItems->count() < $anime->count()) {
                collect($list['data'])->map(function($item) use($anime, $animeListItems, $genres, $saveAnime) {
                    if($saveAnime && !($ani = $anime->where('title', $item['node']['title'])->first())) {
                        $ani = new Anime;
                        $ani->title = $item['node']['title'];
                        $ani->num_episodes = $item['node']['num_episodes'];
                        // @ToDo: make pictures sepperate table
                        $ani->main_picture = $item['node']['main_picture']['large'];
                        $ani->start_date = $item['node']['start_date'];
                        $ani->end_date = $item['node']['end_date'];
                        $ani->mal_id = $item['node']['id'];
                        $ani->save();
                        
                        collect($item['node']['genres'])->map(function($genre) use($ani, $genres) {
                            $genre = $genres->where('title', $genre['name'])->first();
    
                            $relation = new AnimeGenreRelation;
                            $relation->anime_id = $ani['id'];
                            $relation->anime_genre_id = $genre['id'];
                            $relation->save();
                        });
                    };

                    // @ToDo: check for differences
                    $animeListItem = $animeListItems->where('title', $item['node']['title'])->first();
                    if(!$animeListItem) {
                        $animeListItem = new AnimeListItem;
                        $animeListItem->user_id = auth()->user()->id;
                        $animeListItem->anime_id = ($ani ?? $anime->where('title', $item['node']['title'])->first())['id'];
                        $animeListItem->status = $item['list_status']['status'];
                        $animeListItem->score = $item['list_status']['score'];
                        $animeListItem->num_episodes_watched = $item['list_status']['num_episodes_watched'];
                        $animeListItem->is_rewatching = $item['list_status']['is_rewatching'];
                        $animeListItem->num_times_rewatched = $item['list_status']['num_times_rewatched'];
                        $animeListItem->save();
                    }
                });
            }

            return redirect()->route('connections.index');
        })->name('api.mal.auth');
    });
});
