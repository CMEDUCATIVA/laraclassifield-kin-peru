<?php

namespace extras\plugins\watermark;

use Illuminate\Support\ServiceProvider;

class WatermarkServiceProvider extends ServiceProvider
{
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot(): void
	{
		// Merge plugin config
		$this->mergeConfigFrom(realpath(__DIR__ . '/config.php'), 'watermark');
		
		// Load plugin languages files
		$this->loadTranslationsFrom(realpath(__DIR__ . '/lang'), 'watermark');
	}
	
	/**
	 * Register any package services.
	 *
	 * @return void
	 */
	public function register(): void
	{
		$this->app->bind('watermark', fn () => new Watermark());
	}
}
