<?php

namespace App\Http\Resources\Genre;

use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Games\GamesListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ShowResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		/** @var \App\Models\User */
		$user = $request->user();

		$games = $this->games()->orderBy('created_at', 'ASC')->paginate(15, pageName: 'games');
		$from = (($games->currentPage() - 1) * $games->perPage()) + 1;
		$to = ($games->currentPage() - 1) * $games->perPage() + $games->count();

		$discussions = $this->discussions()->with('author')->withCount('comments')->orderBy('created_at', 'ASC')->paginate(15, pageName: 'discussions');
		$from = (($discussions->currentPage() - 1) * $discussions->perPage()) + 1;
		$to = ($discussions->currentPage() - 1) * $discussions->perPage() + $discussions->count();

		return [
			...ListResource::make($this)->toArray($request),
			'shortName' => Str::limit($this->name, 25, preserveWords: true),
			'games' => [
				'data' => GamesListResource::collection($games),
				'meta' => [
					'current_page' => $games->currentPage(),
					'from' => $from,
					'last_page' => $games->lastPage(),
					'per_page' => $games->perPage(),
					'to' => $to,
					'total' => $games->total(),
				]
			],
			'discussions' => [
				'data' => ListDiscussionResource::collection($discussions),
				'meta' => [
					'current_page' => $discussions->currentPage(),
					'from' => $from,
					'last_page' => $discussions->lastPage(),
					'per_page' => $discussions->perPage(),
					'to' => $to,
					'total' => $discussions->total(),
				]
			],
			'isUsersFavorite' => $user->genres()->where('genre_id', $this->id)->exists()
		];
	}
}
