<?php

namespace App\Services;

use Illuminate\Support\Number;

final class ShorterNumbers
{
	public static function convertIntToHumanReadable(int $number, int $threshold = 9999): string | int
	{
		if ($number <= $threshold) {
			return $number;
		}

		return Number::forHumans($number, 1, 2, abbreviate: true);
	}
}
