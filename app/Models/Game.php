<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Game extends Model
{
	use HasFactory;

	protected $fillable = [
		'slug',
		'title',
		'description',
		'thumbnail',
		'media',
		'released_at',
	];

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
}
