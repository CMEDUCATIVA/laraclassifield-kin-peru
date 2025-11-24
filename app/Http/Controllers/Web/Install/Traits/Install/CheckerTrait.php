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

namespace App\Http\Controllers\Web\Install\Traits\Install;

use App\Http\Controllers\Web\Install\Traits\Install\Checker\ComponentsTrait;
use App\Http\Controllers\Web\Install\Traits\Install\Checker\PermissionsTrait;

trait CheckerTrait
{
	use ComponentsTrait, PermissionsTrait;
	
	/**
	 * Is Manual Checking Allowed
	 *
	 * @return bool
	 */
	protected function isManualCheckingAllowed(): bool
	{
		return (request()->has('mode') && request()->input('mode') == 'manual');
	}
	
	/**
	 * @return bool
	 */
	protected function checkComponents(): bool
	{
		$components = $this->getComponents();
		
		$success = true;
		foreach ($components as $component) {
			if ($component['required'] && !$component['isOk']) {
				$success = false;
			}
		}
		
		return $success;
	}
	
	/**
	 * @return bool
	 */
	protected function checkPermissions(): bool
	{
		$permissions = $this->getPermissions();
		
		$success = true;
		foreach ($permissions as $permission) {
			if ($permission['required'] && !$permission['isOk']) {
				$success = false;
			}
		}
		
		return $success;
	}
}
