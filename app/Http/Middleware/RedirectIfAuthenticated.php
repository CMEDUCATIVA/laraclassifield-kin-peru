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

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;

class RedirectIfAuthenticated extends Middleware
{
	/**
	 * Get the path the user should be redirected to when they are authenticated.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return string|null
	 */
	protected function redirectTo(Request $request): ?string
	{
		if (isFromApi()) return null;
		if ($request->expectsJson()) return null;
		
		$url = isFromAdminPanel() ? admin_url() : url('/');
		
		return urlQuery($url)
			->setParameters(['login' => 'success'])
			->toString();
	}
}
