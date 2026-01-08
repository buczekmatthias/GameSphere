<?php

namespace App\Http\Resources\Genre;

use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\PaginatedContentResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowGenreResource extends BaseGenreResource
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

		return [
			...parent::toArray($request),
			'shortName' => Str::limit($this->name, 25, preserveWords: true),
			'games' => PaginatedContentResource::make(Game::gamesWithScore()->where('games.genre_id', $this->id)->orderBy('released_at', 'DESC')->paginate(15, pageName: 'games'))->additional(['data_resource' => GamesListResource::class])->toArray($request),
			'discussions' => PaginatedContentResource::make($this->discussions()->with('author')->withCount('comments')->orderBy('created_at', 'DESC')->paginate(15, pageName: 'discussions'))->additional(['data_resource' => ListDiscussionResource::class])->toArray($request),
		];
	}
}
