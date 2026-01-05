<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable;

	public function getRouteKeyName(): string
	{
		return 'username';
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'name',
		'username',
		'email',
		'password',
		'avatar',
		'role',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var list<string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	public function reviews(): HasMany
	{
		return $this->hasMany(Review::class);
	}

	public function discussions(): HasMany
	{
		return $this->hasMany(Discussion::class);
	}

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function genres(): BelongsToMany
	{
		return $this->belongsToMany(Genre::class, 'genre_user');
	}

	public function createdReports(): HasMany
	{
		return $this->hasMany(Report::class);
	}

	public function reports(): MorphMany
	{
		return $this->morphMany(Report::class, 'reportable');
	}

	public function createdGames(): HasMany
	{
		return $this->hasMany(Game::class);
	}

	public function games(): BelongsToMany
	{
		return $this->belongsToMany(Game::class);
	}

	public function isGameCreator(): bool
	{
		return $this->role === UserRole::GAME_CREATOR->value;
	}

	public function isStaff(): bool
	{
		return in_array(
			$this->role,
			[
				UserRole::MODERATOR->value,
				UserRole::ADMIN->value,
				UserRole::DEVELOPER->value
			]
		);
	}

	public function canAddGame(): bool
	{
		return $this->role !== UserRole::USER->value;
	}

	public function scopePermittedToOwnGame(Builder $query): Builder
	{
		return $query->select(['name', 'username', 'avatar'])->whereNot('role', UserRole::USER->value)->orderBy('name', 'ASC');
	}

	public function scopeOrderInRoleHierarchy(Builder $query, string $order = 'asc'): Builder
	{
		return $query->orderByRaw("array_position("."'{" . implode(',', array_reverse(array_column(UserRole::cases(), 'value'))) . "}'"."::text[],role) {$order}");
	}
}
