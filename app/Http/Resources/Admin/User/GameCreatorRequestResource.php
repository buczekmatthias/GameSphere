<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameCreatorRequestResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'slug' => $this->slug,
			'user' => [
				'name' => $this->user->name,
				'username' => $this->user->username,
			],
			"created_at" => $this->created_at->format('Y-m-d H:i')
		];
	}
}
