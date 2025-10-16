<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameCreatorRequest extends Model
{
	public function getRouteKeyName()
	{
		return 'slug';
	}

	protected $fillable = [
		'slug'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
