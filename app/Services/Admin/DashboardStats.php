<?php

namespace App\Services\Admin;

use App\Models\Discussion;
use App\Models\Game;
use App\Models\Report;
use App\Models\User;
use App\Services\ShorterNumbers;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

final class DashboardStats
{
    private const FRESH = 60 * 30;
    private const STALE = 60 * 45;
    private const MONTHS = 6;

    private const MODELS = [
        'games' => Game::class,
        'users' => User::class,
        'reports' => Report::class,
        'discussions' => Discussion::class,
    ];

    public function get(): array
    {
        return Cache::flexible('admin_dashboard_stats', [self::FRESH, self::STALE], function () {
            $start = now()->toImmutable()->startOfMonth()->subMonths(self::MONTHS - 1);
            $months = $this->months($start);
            $keys = array_keys($months);

            $series = collect(self::MODELS)->map(fn (string $model) => $this->monthlyCounts($model, $start));

            return [
                'new_entries' => $series->map(fn (array $counts) => $this->entry(
                    $counts[$keys[self::MONTHS - 1]] ?? 0,
                    $counts[$keys[self::MONTHS - 2]] ?? 0,
                ))->all(),
                'chart_data' => collect($months)
                    ->map(fn (string $label, string $key) => [
                        'month' => $label,
                        ...$series->map(fn (array $counts) => $counts[$key] ?? 0)->all()
                    ])
                    ->values()
                    ->all()
            ];
        });
    }

    private function months(CarbonImmutable $start): array
    {
        $months = [];

        for ($i = 0; $i < self::MONTHS; $i++) {
            $month = $start->addMonths($i);
            $months[$month->format('Y-m')] = $month->format('M');
        }

        return $months;
    }

    private function monthlyCounts(string $model, CarbonImmutable $since): array
    {
        return $model::query()
            ->where('created_at', '>=', $since)
            ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as total")
            ->groupBy("month")
            ->pluck('total', 'month')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    private function entry(int $current, int $last): array
    {
        return [
            'this_month' => ShorterNumbers::convertIntToHumanReadable($current),
            'last_month' => ShorterNumbers::convertIntToHumanReadable($last),
            'trend' => $this->trend($current, $last)
        ];
    }

    private function trend(int $current, int $last): float
    {
        if ($last === 0) {
            return $current === 0 ? 0.0 : 100.0;
        }

        return round(($current - $last) / $last * 100, 2);
    }
}
