<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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
			'email' => $this->email,
			'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i'),
			'avatar' => $this->avatar,
			"created_at" => $this->created_at->format('Y-m-d')
		];
	}
}
