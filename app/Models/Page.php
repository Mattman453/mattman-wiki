<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Page extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function game() {
        return $this->belongsTo(Game::class, 'game', 'game');
    }
}
