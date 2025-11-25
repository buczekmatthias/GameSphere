<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
	use HasFactory;

	public function getRouteKeyName(): string
	{
		return 'slug';
	}

	protected $fillable = [
		'slug',
		'content',
		'media'
	];

	protected function casts(): array
	{
		return [
			'media' => 'array',
		];
	}

	public function discussion(): BelongsTo
	{
		return $this->belongsTo(Discussion::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function reports(): MorphMany
	{
		return $this->morphMany(Report::class, 'reportable');
	}
}
