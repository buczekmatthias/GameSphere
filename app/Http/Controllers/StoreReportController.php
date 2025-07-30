<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Str;

class StoreReportController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(StoreReportRequest $request)
	{
		$data = $request->validated();

		$entryClass = match ($data['type']) {
			'comment' => new Comment,
			'discussion' => new Discussion,
			'game' => new Game,
			'user' => new User,
			'review' => new Review
		};

		$entry = $entryClass::where($data['type'] === 'user' ? 'username' : 'slug', $data['id'])->first();

		$report = $entry->reports()->make([
			'slug' => Str::uuid(),
			'reason' => $data['reason']
		]);

		$report->user()->associate($request->user());

		$report->save();

		return back();
	}
}
