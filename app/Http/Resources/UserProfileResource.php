<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\Comment\CommentListResource;
use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\ReviewResource as ReviewListResource;
use App\Http\Resources\Genre\ListResource as GenreListResource;
use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$data = [
			...SimpleProfileResource::make($this)->toArray($request),
			'created_at' => $this->created_at->format('M jS, Y'),
		];

		$tab = $request->route('tab', 'created_games');

		$length = match ($tab) {
			'created_games', 'games' => 30,
			'genres', 'reviews' => 15,
			'discussions' => 10,
			'comments' => 20
		};

		$entries = $this->{$tab === 'created_games' ? 'createdGames' : $tab}()->orderBy('created_at', 'ASC');

		if ($tab === 'reviews') {
			$entries = $entries->with(['game']);
		}
		if ($tab === 'games') {
			$entries = $entries->withPivot(['list_type']);
		}
		if ($tab === 'discussions') {
			$entries = $entries->with(['discussable'])->withCount(['comments']);
		}
		if ($tab === 'genres') {
			$entries = $entries->withCount(['games', 'discussions']);
		}

		$entries = $entries->paginate($length);
		$from = (($entries->currentPage() - 1) * $entries->perPage()) + 1;
		$to = ($entries->currentPage() - 1) * $entries->perPage() + $entries->count();

		$meta = [
			'current_page' => $entries->currentPage(),
			'from' => $from,
			'last_page' => $entries->lastPage(),
			'per_page' => $entries->perPage(),
			'to' => $to,
			'total' => $entries->total(),
		];

		return match ($tab) {
			'created_games' => [
				...$data,
				'created_games' => [
					'data' => GamesListResource::collection($entries),
					'meta' => $meta
				]
			],
			'games' => [
				...$data,
				'games' => [
					'data' => $entries->map(function ($game) use ($request) {
						return [
							...GamesListResource::make($game)->toArray($request),
							'list' => $game->pivot->list_type
						];
					}),
					'meta' => $meta
				]
			],
			'genres' => [
				...$data,
				'genres' => [
					'data' => GenreListResource::collection($entries),
					'meta' => $meta
				]
			],
			'reviews' => [
				...$data,
				'reviews' => [
					'data' => ReviewListResource::collection($entries),
					'meta' => $meta
				]
			],
			'discussions' => [
				...$data,
				'discussions' => [
					'data' => ListDiscussionResource::collection($entries),
					'meta' => $meta
				]
			],
			'comments' => [
				...$data,
				'comments' => [
					'data' => CommentListResource::collection($entries),
					'meta' => $meta
				]
			],
		};
	}
}
