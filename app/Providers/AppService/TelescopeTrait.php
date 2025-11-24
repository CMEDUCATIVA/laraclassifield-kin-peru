<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: Mayeul Akpovi (BeDigit - https://bedigit.com)
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Providers\AppService;

use Illuminate\Foundation\Http\Kernel;
use Barryvdh\Debugbar\Facades\Debugbar;

trait TelescopeTrait
{
	/**
	 * @return void
	 */
	private function runInspection(): void
	{
		// Is Debug Bar enabled?
		$isDebugBarEnabled = (config('app.debug') && config('larapen.core.debugBar'));
		if (!$isDebugBarEnabled) {
			Debugbar::disable();
		}
		
		// Know if the server is taking too long to respond than a specific timeout
		$isRequestLifecycleCanBeChecked = (!app()->isProduction());
		if ($isRequestLifecycleCanBeChecked) {
			/**
			 * @var Kernel $kernel
			 */
			$kernel = $this->app[Kernel::class] ?? null;
			if (!is_null($kernel)) {
				if (method_exists($kernel, 'whenRequestLifecycleIsLongerThan')) {
					$httpRequestTimeout = (int)config('larapen.core.performance.httpRequestTimeout', 1);
					$kernel->whenRequestLifecycleIsLongerThan($httpRequestTimeout, function ($startedAt, $request, $response) {
						$message = 'The script detects that your server is taking too long to respond.';
						logger()->warning($message);
					});
				}
			}
		}
		
		// Configuring Eloquent Strictness
		preventLazyLoadingForModelRelations();
	}
}
