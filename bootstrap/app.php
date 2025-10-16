<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CanBecomeGameCreatorMiddleware;
use App\Http\Middleware\IsGameCreatorMiddleware;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetIntendedUrl;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__.'/../routes/web.php',
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware) {
		$middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

		$middleware->redirectGuestsTo('security/login');

		$middleware->web(append: [
			HandleAppearance::class,
			HandleInertiaRequests::class,
			AddLinkHeadersForPreloadedAssets::class,
		]);

		$middleware->alias([
			'admin' => AdminMiddleware::class,
			'set.intended' => SetIntendedUrl::class,
			'game.creator' => IsGameCreatorMiddleware::class,
			'non.game.creator' => CanBecomeGameCreatorMiddleware::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions) {
		//
	})->create();
