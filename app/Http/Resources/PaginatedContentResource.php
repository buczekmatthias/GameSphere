<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedContentResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$from = (($this->resource->currentPage() - 1) * $this->resource->perPage()) + 1;
		$to = ($this->resource->currentPage() - 1) * $this->resource->perPage() + $this->resource->count();

		$meta = [
			'current_page' => $this->resource->currentPage(),
			'from' => $from,
			'last_page' => $this->resource->lastPage(),
			'to' => $to,
			'total' => $this->resource->total(),
		];

		$dataResource = $this->additional['data_resource'] ?? null;

		return [
			'data' => ($dataResource ? $dataResource::collection($this->resource->items()) : $this->resource->items()),
			'meta' => $meta,
			'links' => [
				'prev' => $this->resource->previousPageUrl(),
				'next' => $this->resource->nextPageUrl()
			]
		];
	}
}
