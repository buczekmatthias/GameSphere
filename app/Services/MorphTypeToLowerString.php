<?php

namespace App\Services;

final class MorphTypeToLowerString
{
	public static function getTransformedString(string $type)
	{
		$parts = explode("\\", $type);
		$lastPart = array_pop($parts);

		return strtolower($lastPart);
	}
}
