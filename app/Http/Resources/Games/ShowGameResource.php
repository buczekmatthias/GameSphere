<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowGameResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			...EditGameResource::make($this)->toArray($request),
			'creator' => ['name' => $this->creator->name, 'username' => $this->creator->username]
		];
	}
}
