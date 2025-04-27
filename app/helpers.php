<?php

use App\Services\Routes;

if (! function_exists('ziggyRoutes')) {
	function ziggyRoutes(): array
	{
		return Routes::getRoutes();
	}
}
