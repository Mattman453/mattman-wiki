<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function homeView(Request $request) {
        $games = Game::all();
        return Inertia::render('Home', [
            'games' => $games,
        ]);
    }
}
