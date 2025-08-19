<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Report;
use App\Models\User;
use App\Services\ShorterNumbers;
use Inertia\Inertia;

class DashboardController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke()
	{
		return Inertia::render('admin/Dashboard', [
			'new_entries' => [
				'games' => [
					'this_month' => ShorterNumbers::convertIntToHumanReadable($game_this = Game::thisMonth()->count()),
					'last_month' => ShorterNumbers::convertIntToHumanReadable($game_last = Game::lastMonth()->count()),
					'trend' => $this->getTrend($game_this, $game_last)
				],
				'users' => [
					'this_month' => ShorterNumbers::convertIntToHumanReadable($user_this = User::thisMonth()->count()),
					'last_month' => ShorterNumbers::convertIntToHumanReadable($user_last = User::lastMonth()->count()),
					'trend' => $this->getTrend($user_this, $user_last)
				],
				'reports' => [
					'this_month' => ShorterNumbers::convertIntToHumanReadable($report_this = Report::thisMonth()->count()),
					'last_month' => ShorterNumbers::convertIntToHumanReadable($report_last = Report::lastMonth()->count()),
					'trend' => $this->getTrend($report_this, $report_last)
				],
				'discussions' => [
					'this_month' => ShorterNumbers::convertIntToHumanReadable($discussion_this = Discussion::thisMonth()->count()),
					'last_month' => ShorterNumbers::convertIntToHumanReadable($discussion_last = Discussion::lastMonth()->count()),
					'trend' => $this->getTrend($discussion_this, $discussion_last)
				],
			],
			'active_reports' => UserReportsTableResource::collection(Report::activeReports()->with(['reportable', 'user'])->orderBy('created_at', 'DESC')->paginate(15))
		]);
	}

	private function getTrend(int $current, int $last)
	{
		if ($last === 0) {
			if ($current === 0) {
				return 0;
			}

			return 100;
		}

		$diff = $current - $last;
		$trend = ($diff / $last) * 100;

		return number_format($trend, 2, ".", "");
	}
}
