<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Games\GamesListResource;
use Illuminate\Http\Request;

class GamesWithListTypeListResource extends GamesListResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			...parent::toArray($request),
			'lists' => $this->list_types
		];
	}
}
