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

namespace App\Enums;

enum PostType: int
{
	use EnumToArray {
		all as traitAll;
	}
	
	case INDIVIDUAL = 1;
	case PROFESSIONAL = 2;
	
	public function label(): string
	{
		return match ($this) {
			self::INDIVIDUAL => trans('enum.individual'),
			self::PROFESSIONAL => trans('enum.professional'),
		};
	}
	
	/**
	 * @return array
	 */
	public static function all(): array
	{
		if (!app()->runningInConsole()) {
			if (!config('settings.listing_form.show_listing_type')) {
				return [];
			}
		}
		
		return self::traitAll();
	}
}
