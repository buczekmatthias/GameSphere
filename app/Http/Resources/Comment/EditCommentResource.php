<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EditCommentResource extends UserProfileCommentResource
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
			'shortSlug' => Str::limit($this->slug, 20),
			'discussion' => [
				'title' => $this->discussion->title,
				'slug' => $this->discussion->slug,
			]
		];
	}
}
