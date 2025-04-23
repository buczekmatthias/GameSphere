<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Review extends Model
{
	protected $fillable = [
		'slug',
		'content',
		'ratings',
		'is_verified'
	];

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
}
