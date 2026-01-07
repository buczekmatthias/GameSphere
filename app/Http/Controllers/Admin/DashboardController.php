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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
	private const FRESH = 60 * 30;

	private const STALE = 60 * 45;

	/**
	 * Handle the incoming request.
	 */
	public function __invoke(): Response
	{
		return Inertia::render('admin/Dashboard', [
			'new_entries' => [
				'games' => [
					...$games = $this->getStatsDataForClass(Game::class),
					'trend' => $this->getTrend($games['this_month'], $games['last_month'])
				],
				'users' => [
					...$users = $this->getStatsDataForClass(User::class),
					'trend' => $this->getTrend($users['this_month'], $users['last_month'])
				],
				'reports' => [
					...$reports = $this->getStatsDataForClass(Report::class),
					'trend' => $this->getTrend($reports['this_month'], $reports['last_month'])
				],
				'discussions' => [
					...$discussions = $this->getStatsDataForClass(Discussion::class),
					'trend' => $this->getTrend($discussions['this_month'], $discussions['last_month'])
				],
			],
			'chart_data' => $this->getChartData(),
			'active_reports' => UserReportsTableResource::collection(Report::activeReports()->with(['reportable', 'user'])->orderBy('created_at', 'DESC')->limit(5)->get()),
			'pending_requests' => GameCreatorRequestResource::collection(GameCreatorRequest::with(['user'])->orderBy('created_at', 'DESC')->limit(5)->get())
		]);
	}

	/**
	 * @param class-string<Game|User|Report|Discussion> $class
	 */
	private function getStatsDataForClass(string $class): array
	{
		$results = Cache::flexible("{$class}_dashboard_stats", [self::FRESH, self::STALE], function () use ($class) {
			$now = now();
			$startOfMonth = $now->startOfMonth();

			$model = app($class);
			$results = collect(
				$model
					->query()
					->selectRaw(
						'SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_month,
						SUM(CASE WHEN created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as last_month',
						[$startOfMonth, $now->subMonth()->startOfMonth(), $startOfMonth]
					)->first()
			);

			$results->map(fn ($r) => ShorterNumbers::convertIntToHumanReadable($r));

			return $results->toArray();
		});

		return $results;
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
		return Cache::flexible("dashboard_chart_data", [self::FRESH, self::STALE], function () {
			$now = now();

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
		});
	}

	private function getTableDataForChart(string $tableName, Carbon $date): Collection
	{
		return DB::table($tableName)
			->selectRaw(
				"EXTRACT(MONTH FROM created_at) as month,
				COUNT(*) as count"
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
