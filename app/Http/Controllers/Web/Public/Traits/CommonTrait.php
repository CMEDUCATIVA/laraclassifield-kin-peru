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

namespace App\Http\Controllers\Web\Public\Traits;

use App\Helpers\DotenvEditor;
use App\Helpers\Files\Storage\StorageDisk;

trait CommonTrait
{
	public $disk;
	
	/**
	 * Set the storage disk
	 */
	private function setStorageDisk(): void
	{
		// Get the storage disk
		$this->disk = StorageDisk::getDisk();
		view()->share('disk', $this->disk);
	}
	
	/**
	 * Check & update the App Key (If needed, for security reasons)
	 *
	 * @return void
	 */
	private function checkAndGenerateAppKey(): void
	{
		$isUnsecureAppKey = (DotenvEditor::getValue('APP_KEY') == 'SomeRandomStringWith32Characters');
		
		// Generate a new App Key
		if ($isUnsecureAppKey) {
			updateAppKeyWithArtisan();
		}
	}
}
