<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
			'shortTitle' => Str::limit($this->title, 25, preserveWords: true),
			'creator' => ['name' => $this->creator->name, 'username' => $this->creator->username]
		];
	}
}
