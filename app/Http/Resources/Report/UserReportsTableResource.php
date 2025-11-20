<?php

namespace App\Http\Resources\Report;

use App\Enum\ReportableType;
use App\Http\Resources\User\SimpleProfileResource;
use App\Services\MorphTypeToLowerString;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReportsTableResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		if ($this->whenLoaded('reportable')) {
			$s = MorphTypeToLowerString::getTransformedString($this->reportable_type);
		}

		return [
			'slug' => $this->slug,
			'reason' => $this->reason,
			'status' => $this->status,
			'reportable' => $this->whenLoaded(
				'reportable',
				fn () => match ($s) {
					ReportableType::DISCUSSION->value => route('discussions.show', ['discussion' => $this->reportable->slug]),
					ReportableType::COMMENT->value => route('comments.show', ['comment' => $this->reportable->slug]),
					ReportableType::GAME->value => route('games.show', ['game' => $this->reportable->slug]),
					ReportableType::REVIEW->value => route('reviews.show', ['review' => $this->reportable->slug]),
					ReportableType::USER->value => route('user.profile', ['user' => $this->reportable->username])
				}
			),
			'reportable_type' => $this->whenLoaded(
				'reportable',
				fn () => $s
			),
			'user' => $this->whenLoaded(
				'user',
				fn () => SimpleProfileResource::make($this->user)->toArray($request),
			),
			'created_at' => $this->created_at->format('Y-m-d')
		];
	}
}
