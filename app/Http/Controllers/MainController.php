<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MainController extends Controller
{
    /**
     * Get the homepage view. Should be a GET request.
     * 
     * @return Response the view correlating to the home page.
     */
    public function homeView() : Response {
        $games = Game::select(['game', 'image'])->get();
        return Inertia::render('GameList', [
            'games' => $games,
        ]);
    }

    /**
     * Get the about page view. Should be a GET request.
     * 
     * @return Response the view correlating to the About page.
     */
    public function aboutView() : Response {
        return Inertia::render('About');
    }

    /**
     * Show the server that you are still active.
     * 
     * @param $request The included request containing the session information
     * @return JsonResponse the json response containing the updated lifetime.
     */
    public function updateLifetime(Request $request) : JsonResponse {
        $request->session()->put('lifetime', Carbon::now()->addMinutes(config('session.lifetime'))->toDateTimeString('millisecond'));
        return response()->json([
            'lifetime' => Carbon::now()->addMinutes(config('session.lifetime'))->toDateTimeString('millisecond'),
        ], 200);
    }
}
