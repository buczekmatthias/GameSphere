<?php

namespace App\Models;

use App\Enum\GameCollectionType;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
	use HasFactory;

	public function getRouteKeyName(): string
	{
		return 'slug';
	}

	protected $fillable = [
		'slug',
		'title',
		'description',
		'thumbnail',
		'media',
		'released_at',
	];

	protected function casts(): array
	{
		return [
			'media' => 'array',
			'released_at' => 'date:Y-m-d',
			'list_types' => 'array',
		];
	}

	public function creator(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function reviews(): HasMany
	{
		return $this->hasMany(Review::class);
	}

	public function genre(): BelongsTo
	{
		return $this->belongsTo(Genre::class);
	}

	public function discussions(): MorphMany
	{
		return $this->morphMany(Discussion::class, 'discussable');
	}

	public function reports(): MorphMany
	{
		return $this->morphMany(Report::class, 'reportable');
	}

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'game_user')
					->withPivot('list_type');
	}

	public function favoriteUsers(): BelongsToMany
	{
		return $this->users()->wherePivot('list_type', GameCollectionType::FAVORITE->value);
	}

	public function isGameReleased(): bool
	{
		return $this->released_at <= now()->endOfDay();
	}

	public function scopeGetGameWithScore(Builder $query): Builder
	{
		return $query
			->select(['games.*'])
			->selectSub($this->getScoreSubquery(), 'score');
	}

	public function scopeGamesWithScore(Builder $query): Builder
	{
		return $query
			->select(['games.title', 'games.slug', 'games.thumbnail'])
			->selectSub($this->getScoreSubquery(), 'score');
	}

	private function getScoreSubquery(): Closure
	{
		return fn ($q) => $q->from('reviews')
			->whereColumn('reviews.game_id', 'games.id')
			->selectRaw("COALESCE(
            AVG(
                (CAST(ratings->>'gameplay' AS DECIMAL) + 
                CAST(ratings->>'graphics' AS DECIMAL) + 
                CAST(ratings->>'storyline' AS DECIMAL) + 
                CAST(ratings->>'replayability' AS DECIMAL) + 
                CAST(ratings->>'sound_and_music' AS DECIMAL) + 
                CAST(ratings->>'performance' AS DECIMAL)) / 6.0
            ),
            0
        )");
	}

	public function scopeWithLists(Builder $query, User $user): Builder
	{
		return $query
				->addSelect(DB::raw("JSON_AGG(game_user.list_type) as list_types "))
				->leftJoin('game_user', 'games.id', '=', 'game_user.game_id')
				->where('game_user.user_id', $user->id)
				->groupBy(
					'game_user.game_id',
					'game_user.user_id',
					'games.id'
				);
	}
}
