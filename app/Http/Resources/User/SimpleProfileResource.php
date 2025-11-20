<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SimpleProfileResource extends JsonResource
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
		];
	}
}
