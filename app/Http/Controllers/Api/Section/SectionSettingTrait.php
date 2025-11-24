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

namespace App\Http\Controllers\Api\Section;

trait SectionSettingTrait
{
	/**
	 * @param array|null $value
	 * @return array|null
	 */
	protected function searchFormSettings(?array $value = []): ?array
	{
		// Load Country's Background Image
		$countryBackgroundImage = config('country.background_image_path');
		if (isset($this->disk)) {
			if (!empty($countryBackgroundImage) && $this->disk->exists($countryBackgroundImage)) {
				$value['background_image_path'] = $countryBackgroundImage;
			}
		}
		
		$appLocale = config('app.locale');
		
		// Title: Count Posts & Users
		if (!empty($value['title_' . $appLocale])) {
			$title = $value['title_' . $appLocale];
			$title = replaceGlobalPatterns($title);
			
			$value['title_' . $appLocale] = $title;
		}
		
		// SubTitle: Count Posts & Users
		if (!empty($value['sub_title_' . $appLocale])) {
			$subTitle = $value['sub_title_' . $appLocale];
			$subTitle = replaceGlobalPatterns($subTitle);
			
			$value['sub_title_' . $appLocale] = $subTitle;
		}
		
		return $value;
	}
}
