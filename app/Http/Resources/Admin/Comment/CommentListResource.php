<?php

namespace App\Http\Resources\Admin\Comment;

use App\Http\Resources\Discussion\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CommentListResource extends JsonResource
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
			'content' => $this->content,
			'shortContent' => Str::limit($this->content, 25, preserveWords: true),
			'media' => $this->media ? sizeof($this->media) : 0,
			'user' => $this->whenLoaded(
				'user',
				fn () => new AuthorResource($this->user)
			),
			'discussion' => $this->whenLoaded(
				'discussion',
				fn () => ['title' => $this->discussion->title, 'slug' => $this->discussion->slug]
			),
			'reports_count' => $this->whenCounted('reports'),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
