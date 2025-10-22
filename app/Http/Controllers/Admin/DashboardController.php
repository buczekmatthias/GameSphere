<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\User\GameCreatorRequestResource;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\GameCreatorRequest;
use App\Models\Report;
use App\Models\User;
use App\Services\ShorterNumbers;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(): Response
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
			'chart_data' => $this->getChartData(),
			'active_reports' => UserReportsTableResource::collection(Report::activeReports()->with(['reportable', 'user'])->orderBy('created_at', 'DESC')->paginate(15)),
			'pending_requests' => GameCreatorRequestResource::collection(GameCreatorRequest::with(['user'])->orderBy('created_at', 'DESC')->paginate(15))
		]);
	}

	private function getTrend(int $current, int $last): string | int
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

	private function getChartData(): array
	{
		$now = Carbon::now();

		$date = $now->copy()->startOfMonth()->subMonths(5);

		$games = $this->getTableDataForChart('games', $date);
		$users = $this->getTableDataForChart('users', $date);
		$discussions = $this->getTableDataForChart('discussions', $date);
		$reports = $this->getTableDataForChart('reports', $date);

		$months = [];

		for ($i = 5; $i >= 0; $i--) {
			$months[] = $now->copy()->subMonths($i)->format('M');
		}

		$data = array_map(
			fn ($month) => [
				'month' => $month,
				'games' => 0,
				'users' => 0,
				'discussions' => 0,
				'reports' => 0,
			],
			$months
		);

		$this->updateData($games, 'games', $data);
		$this->updateData($users, 'users', $data);
		$this->updateData($discussions, 'discussions', $data);
		$this->updateData($reports, 'reports', $data);

		return $data;
	}

	private function getTableDataForChart(string $tableName, Carbon $date): Collection
	{
		return DB::table($tableName)
			->select(
				DB::raw('EXTRACT(MONTH FROM created_at) as month'),
				DB::raw('COUNT(*) as count')
			)
			->where('created_at', '>=', $date)
			->groupBy('month')
			->get();
	}

	private function updateData(Collection $collection, string $key, array &$data): void
	{
		foreach ($collection as $item) {
			$monthName = Carbon::create()->month((int)$item->month)->format('M');
			foreach ($data as &$entry) {
				if ($entry['month'] === $monthName) {
					$entry[$key] = $item->count;

					break;
				}
			}
		}
	}
}
