<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AnimeListItem extends Model
{
    use HasFactory;

    public function anime(): HasOne
    {
        return $this->hasOne(Anime::class, 'id', 'anime_id');
    }
}
