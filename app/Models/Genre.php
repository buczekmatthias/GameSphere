<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Genre extends Model
{
	protected $fillable = [
		'slug',
		'name'
	];

	public function discussions(): MorphMany
	{
		return $this->morphMany(Discussion::class, 'discussable');
	}

	public function games(): HasMany
	{
		return $this->hasMany(Game::class);
	}
}
