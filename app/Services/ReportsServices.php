<?php

namespace App\Services;

use App\Enum\ReportReason;
use Illuminate\Support\Facades\Route;

final class ReportsServices
{
	public static function getReportReasons(): array
	{
		return array_column(ReportReason::cases(), 'value');
	}

	public static function hasReportableContent(): bool
	{
		$currentRoute = Route::currentRouteName();

		$routesWithReportableContent = [
			'comments.show',
			'discussions.show',
			'games.show',
			'reviews.show',
			'user.profile'
		];

		return in_array($currentRoute, $routesWithReportableContent);
	}
}
