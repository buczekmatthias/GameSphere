<?php

namespace App\Services;

final class MorphTypeToLowerString
{
	public static function getTransformedString(string $type): string
	{
		$parts = explode("\\", $type);
		$lastPart = array_pop($parts);

		return strtolower($lastPart);
	}
}
