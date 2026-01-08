<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Comment\UserProfileCommentResource;
use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\GameReviewResource;
use App\Http\Resources\Genre\GenreListResource;
use App\Http\Resources\PaginatedContentResource;
use App\Models\Game;
use Illuminate\Http\Request;

class UserProfileResource extends SimpleProfileResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$data = [
			...parent::toArray($request),
			'created_at' => $this->created_at->format('M jS, Y'),
			'email_verified_at' => $this->email_verified_at
		];

		$tab = $request->route('tab', 'created_games');

		$entries = (match ($tab) {
			'created_games' => Game::gamesWithScore()
				->where('games.user_id', $this->id)
				->orderBy('title', 'ASC'),
			'games' => Game::gamesWithScore()
				->withLists($request->route('user'))
				->orderBy('title', 'DESC'),
			'genres' => $this->genres()
				->withCount(['games', 'discussions'])
				->orderBy('name', 'ASC'),
			'reviews' => $this->reviews()
				->select(['reviews.created_at', 'reviews.slug', 'reviews.content', 'reviews.ratings', 'reviews.game_id'])
				->with(['game:id,title,slug'])
				->orderBy('created_at', 'DESC'),
			'discussions' => $this->discussions()
				->select(['discussions.title', 'discussions.slug', 'discussions.created_at', 'discussions.discussable_id', 'discussions.discussable_type'])
				->with(['discussable'])
				->withCount(['comments'])
				->orderBy('created_at', 'DESC'),
			'comments' => $this->comments()
				->with('discussion:id,slug')
				->orderBy('created_at', 'DESC')
		})
			->paginate(
				match ($tab) {
					'created_games', 'games' => 30,
					'genres', 'reviews' => 15,
					'discussions' => 10,
					'comments' => 20
				}
			);

		return match ($tab) {
			'created_games' => [
				...$data,
				'created_games' => PaginatedContentResource::make($entries)->additional(['data_resource' => GamesListResource::class])->toArray($request),
			],
			'games' => [
				...$data,
				'games' => PaginatedContentResource::make($entries)->additional(['data_resource' => GamesWithListTypeListResource::class])->toArray($request)
			],
			'genres' => [
				...$data,
				'genres' => PaginatedContentResource::make($entries)->additional(['data_resource' => GenreListResource::class])->toArray($request),
			],
			'reviews' => [
				...$data,
				'reviews' => PaginatedContentResource::make($entries)->additional(['data_resource' => GameReviewResource::class])->toArray($request),
			],
			'discussions' => [
				...$data,
				'discussions' => PaginatedContentResource::make($entries)->additional(['data_resource' => ListDiscussionResource::class])->toArray($request),
			],
			'comments' => [
				...$data,
				'comments' => PaginatedContentResource::make($entries)->additional(['data_resource' => UserProfileCommentResource::class])->toArray($request),
			],
		};
	}
}
