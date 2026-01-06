<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function homeView(Request $request) {
        $games = Game::select(['title', 'image', 'link'])->get();
        return Inertia::render('GameList', [
            'games' => $games,
        ]);
    }

    public function aboutView(Request $request) {
        return Inertia::render('About');
    }
}
