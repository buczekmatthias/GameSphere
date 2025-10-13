<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
			'name' => $this->name,
			'username' => $this->username,
			'role' => $this->role,
			'avatar' => $this->avatar ? asset(Storage::url("users/{$this->avatar}")) : '',
			'is_verified' => !is_null($this->email_verified_at),
			'created_at' => $this->created_at->format('M jS, Y'),
		];
	}
}
