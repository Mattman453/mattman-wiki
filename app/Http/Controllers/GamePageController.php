<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GamePageController extends Controller
{
    public function showStandardPage(Request $request) {
        $request['game'] = str_replace('_', ' ', $request['game']);
        if ($request['subtitle'])$request['subtitle'] = str_replace('_', ' ', $request['subtitle']);
        if ($request['page']) $request['page'] = str_replace('_', ' ', $request['page']);

        $game = Game::select(['game', 'image', 'sections'])->where(['game' => $request['game']])->get();
        if (!isset($game[0])) {
            return Inertia::render('Error', [
                'missingGame' => $request['game'],
            ]);
        }

        $databaseRequest = Page::where(['game' => $request['game']]);
        $request['subtitle'] ?
            $databaseRequest = $databaseRequest->where(['subtitle' => $request['subtitle']]) :
            $databaseRequest = $databaseRequest->where(['subtitle' => ['$exists' => false]]);
        $request['page'] ?
            $databaseRequest = $databaseRequest->where(['page' => $request['page']]) :
            $databaseRequest = $databaseRequest->where(['page' => ['$exists' => false]]);
        $page = $databaseRequest->get();

        if (!isset($page[0])) {
            return Inertia::render('Error', [
                'missingPage' => $request['game'] . '/' . $request['subtitle'] . '/' . $request['page'],
            ]);
        }
        return Inertia::render('InfoPage', [
            'gameInfo' => $game[0],
            'page' => $page[0],
        ]);
    }
}
