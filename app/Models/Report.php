<?php

namespace App\Models;

use App\Enum\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
	public function getRouteKeyName()
	{
		return 'slug';
	}

	protected $fillable = [
		'slug',
		'reason',
		'status'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function reportable(): MorphTo
	{
		return $this->morphTo();
	}

	public function scopeThisMonth(Builder $query)
	{
		return $query->where('created_at', '>=', now()->startOfMonth());
	}

	public function scopeLastMonth(Builder $query)
	{
		return $query->whereBetween('created_at', [now()->startOfMonth()->subMonth(), now()->startOfMonth()]);
	}

	public function scopeActiveReports(Builder $query)
	{
		return $query->where('status', ReportStatus::OPEN->value);
	}
}
