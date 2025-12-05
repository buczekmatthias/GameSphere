<?php

namespace App\Http\Resources\User;

use App\Http\Resources\User\SimpleProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			...SimpleProfileResource::make($this)->toArray($request),
			'is_verified' => !is_null($this->email_verified_at),
			'created_at' => $this->created_at->format('M jS, Y'),
		];
	}
}
