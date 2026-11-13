<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Game;
use App\Http\Requests\Review\StoreRequest;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\GameReviewResource;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function show(Review $review): Response
    {
        $review->load([
            'user:id,name,username,role,avatar,email_verified_at',
            'game:id,slug,title,description,thumbnail'
        ]);

        return Inertia::render('app/review/Show', [
            'review' => GameReviewResource::make($review)
        ]);
    }

    public function create(Game $game): Response
    {
        return Inertia::render('app/review/Create', [
            'game' => GamesListResource::make($game)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $review = Game::where('slug', $data['game_slug'])->first()->reviews()->make([
            'slug' => Str::uuid(),
            'content' => $data['content'],
            'ratings' => $data['ratings']
        ]);

        $review->user_id = $request->user()->id;
        $review->save();

        return to_route('games.show', ['game' => $data['game_slug']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        DB::transaction(function () use ($review) {
            $review->reports()->delete();
            $review->delete();
        });

        if (request()->has('to_route')) {
            return to_route(request()->get('to_route'));
        }

        return back();
    }
}
