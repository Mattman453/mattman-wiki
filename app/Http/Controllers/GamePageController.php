<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GamePageController extends Controller
{
    public function showGamePage(Request $request) {
        if (!$request['game']) {
            return Inertia::render('Error', [
                'missingGame' => $request['game'],
            ]);
        }
        
        $info = Game::where(['link' => $request['game']])->get();
        if (!isset($info[0])) {
            return Inertia::render('Error', [
                'missingGame' => $request['game'],
            ]);
        }
        return Inertia::render('GameLanding', [
            'gameInfo' => $info[0],
        ]);
    }
}
