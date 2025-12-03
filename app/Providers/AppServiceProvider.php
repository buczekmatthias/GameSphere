<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		JsonResource::withoutWrapping();

		Validator::extend('string_or_uuid', function ($attribute, $value, $parameters, $validator) {
			return Str::isUuid($value) || is_string($value);
		});

		Inertia::share([
			'globals' => config('globals')
		]);
	}
}
