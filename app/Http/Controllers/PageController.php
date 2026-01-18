<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * Update an existing page with new titles, bodies, and any other changes made. Should be a POST request.
     * 
     * @param Request The request sent to the server containing the new changes. Currently the entire page.
     * @return JsonResponse sends a 404 if page does not exist, 403 if not authorized (must be admin or author role user),
     *      400 if a new title is requested but already exists, 302 if the title change is accepted as the link will change,
     *      or 200 if all changes are accepted.
     */
    public function updatePage(Request $request) : JsonResponse {
        if (!in_array('admin', Auth::user()->roles) && !in_array('author', Auth::user()->roles)) {
            return response()->json([
                'error' => 'You do not have permission to perform this action.',
            ], 403);
        }
        $request['game'] = str_replace('_', ' ', $request['game']);
        if ($request['subtitle']) $request['subtitle'] = str_replace('_', ' ', $request['subtitle']);
        if ($request['page']) $request['page'] = str_replace('_', ' ', $request['page']);

        $page = Page::where(['game' => $request['game']]);
        $request['subtitle'] ?
            $page = $page->where(['subtitle' => $request['subtitle']]) :
            $page = $page->where(['subtitle' => ['$exists' => false]]);
        $request['page'] ?
            $page = $page->where(['page' => $request['page']]) :
            $page = $page->where(['page' => ['$exists' => false]]);
        $page = $page->first();

        if (!isset($page)) {
            return response()->json([
                'error' => 'Page not found.',
            ], 404);
        }

        $page->sections = $request['sections'];
        $page->save();
        if ($request['page'] && $request['title'] != $request['page']) {
            $game = Game::where(['game' => $request['game']])->first();
            $sections = $game->sections;
            $sectionIndex = -1;
            $pageIndex = -1;
            foreach ($sections as $i => $section) {
                if ($section['subtitle'] == $request['subtitle']) {
                    $sectionIndex = $i;
                    foreach ($sections[$i]['sections'] as $j => $subSection) {
                        if ($subSection == $request['page']) {
                            $pageIndex = $j;
                        }
                        if ($subSection == $request['title']) {
                            return response()->json([
                                'error' => 'Page name already exists. Body is updated but title is not.',
                            ], 400);
                        }
                    }
                    $sections[$sectionIndex]['sections'][$pageIndex] = $request['title'];
                    $game->sections = $sections;
                    $game->save();
                    break;
                }
            }
            $page->page = $request['title'];
            $page->save();
            return response()->json([
                'message' => 'Page updated with new name. Redirecting...',
                'redirect' => '/game/' . str_replace(' ', '_', $request['game']) . '/' . str_replace(' ', '_', $request['subtitle']) . '/' . str_replace(' ', '_', $request['title']),
            ], 302);
        } 
        else if ($request['subtitle'] && $request['title'] != $request['subtitle']) {
            $game = Game::where(['game' => $request['game']])->first();
            $sections = $game->sections;
            $sectionIndex = -1;
            foreach ($sections as $i => $section) {
                if ($section['subtitle'] == $request['subtitle']) {
                    $sectionIndex = $i;
                }
                if ($section['subtitle'] == $request['title']) {
                    return response()->json([
                        'error' => 'Page name already exists. Body is updated but title is not.',
                    ], 400);
                }
            }
            $sections[$sectionIndex]['subtitle'] = $request['title'];
            $game->sections = $sections;
            $game->save();
            $changePages = Page::where(['game' => $request['game'], 'subtitle' => $request['subtitle']])->get();
            foreach ($changePages as $changePage) {
                $changePage->subtitle = $request['title'];
                $changePage->save();
            }
            return response()->json([
                'message' => 'Page updated with new name. Redirecting...',
                'redirect' => '/game/' . str_replace(' ', '_', $request['game']) . '/' . str_replace(' ', '_', $request['title']),
            ], 302);
        }

        return response()->json([
            'message' => 'Page updated.',
        ], 200);
    }

    /**
     * Get the view for a standard page fetched from the database. Should be a GET request.
     * 
     * @param Request $request contains the uri components to specify game, subtitle, and page to be fetched.
     * @return Response either an Error page if the game or page does not exist, or the requested page.
     */
    public function showStandardPage(Request $request) : Response {
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
