<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discussion extends Model
{
	use HasFactory;

	public function getRouteKeyName(): string
	{
		return 'slug';
	}

	protected $fillable = [
		'slug',
		'title',
		'is_locked'
	];

	public function author(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function discussable(): MorphTo
	{
		return $this->morphTo();
	}

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function reports(): MorphMany
	{
		return $this->morphMany(Report::class, 'reportable');
	}
}
