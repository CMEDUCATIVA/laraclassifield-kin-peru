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

namespace App\Models\Thread;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait MessageTrait
{
	/**
	 * Recipients of this message.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function recipients(): HasMany
	{
		return $this->participants()->where('user_id', '!=', $this->user_id);
	}
}
