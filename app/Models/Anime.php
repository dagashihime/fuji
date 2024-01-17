<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Anime extends Model
{
    use HasFactory;

    protected $table = 'anime';

    public function genres(): HasManyThrough
    {
        return $this->hasManyThrough(
            AnimeGenre::class, 
            AnimeGenreRelation::class, 
            firstKey: 'anime_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'anime_genre_id'
        );
    }
}
