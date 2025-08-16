<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discussion extends Model
{
	use HasFactory;

	public function getRouteKeyName()
	{
		return 'slug';
	}

	protected $fillable = [
		'slug',
		'title',
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

	public function scopeThisMonth(Builder $query)
	{
		return $query->where('created_at', '>=', now()->startOfMonth());
	}

	public function scopeLastMonth(Builder $query)
	{
		return $query->whereBetween('created_at', [now()->startOfMonth()->subMonth(), now()->startOfMonth()]);
	}
}
