<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'game',
        'image',
        'link',
    ];

    /**
     * Get the pages that are associated with this game.
     * 
     * @return HasMany the relationship allowing the fetching of all pages related to the Game.
     */
    public function pages() : HasMany {
        return $this->hasMany(Page::class);
    }
}
