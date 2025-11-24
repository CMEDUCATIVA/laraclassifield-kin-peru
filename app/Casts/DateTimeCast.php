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

namespace App\Casts;

use App\Helpers\Date;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DateTimeCast implements CastsAttributes
{
	/**
	 * Cast the given value.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @param string $key
	 * @param mixed $value
	 * @param array<string, mixed> $attributes
	 * @return \Illuminate\Support\Carbon|null
	 */
	public function get(Model $model, string $key, mixed $value, array $attributes): ?Carbon
	{
		return Date::toCarbon($value);
	}
	
	/**
	 * Prepare the given value for storage.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @param string $key
	 * @param mixed $value
	 * @param array<string, mixed> $attributes
	 * @return mixed|null
	 */
	public function set(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		// Return the value without modification to use Laravel's default datetime cast
		return $value;
	}
}
