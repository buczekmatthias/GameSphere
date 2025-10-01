<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Review extends Model
{
	use HasFactory;

	public function getRouteKeyName()
	{
		return 'slug';
	}

	protected $fillable = [
		'slug',
		'content',
		'ratings',
		'is_verified'
	];

	protected function casts(): array
	{
		return [
			'ratings' => 'array',
		];
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function game(): BelongsTo
	{
		return $this->belongsTo(Game::class);
	}

	public function reports(): MorphMany
	{
		return $this->morphMany(Report::class, 'reportable');
	}

	public function getAverageRatingAttribute()
	{
		if (empty($this->ratings)) {
			return 0;
		}

		return array_sum($this->ratings) / count($this->ratings);
	}
}
