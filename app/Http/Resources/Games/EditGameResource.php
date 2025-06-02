<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EditGameResource extends JsonResource
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
			'title' => $this->title,
			'description' => $this->description,
			'thumbnail' => asset(Storage::url("games/{$this->slug}/{$this->thumbnail}")),
			'media' => $this->media ?
				collect($this->media)
				->map(
					fn (string $file) => [
						'filename' => $file,
						'path' => asset(Storage::url("games/{$this->slug}/{$file}")),
						'type' => str_contains('.mp4', $file) ? 'video' : 'image'
					]
				)->toArray()
				: [],
			'released_at' => $this->released_at->format('Y-m-d'),
			'genre' => ['name' => $this->genre->name, 'slug' => $this->genre->slug]
		];
	}
}
