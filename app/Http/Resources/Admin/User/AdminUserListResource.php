<?php

namespace App\Http\Resources\Admin\User;

use App\Http\Resources\User\SimpleProfileResource;
use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserListResource extends JsonResource
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
			'email' => $this->email,
			'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i'),
			"created_at" => $this->created_at->format('Y-m-d'),
			'permissions' => [
				'destroy' => UserPermissions::checkPermissions('delete', $this->resource),
			],
		];
	}
}
