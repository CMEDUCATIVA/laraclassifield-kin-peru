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

namespace App\Services\UrlGen\SearchTrait;

trait FiltersCleanerBtn
{
	/**
	 * Generate button link for the category filter removal
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function getCategoryFilterClearLink($cat = null, $city = null): string
	{
		$out = '';
		
		if (self::doesCategoryIsFiltered($cat)) {
			$url = self::searchWithoutCategory($cat, $city);
			$out = getFilterClearBtn($url);
		}
		
		return $out;
	}
	
	/**
	 * Generate button link for the city filter removal
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function getCityFilterClearLink($cat = null, $city = null): string
	{
		$out = '';
		
		if (self::doesCityIsFiltered($city)) {
			$url = self::searchWithoutCity($cat, $city);
			$out = getFilterClearBtn($url);
		}
		
		return $out;
	}
	
	/**
	 * Generate button link for the date filter removal
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function getDateFilterClearLink($cat = null, $city = null): string
	{
		$out = '';
		
		if (self::doesDateIsFiltered($cat, $city)) {
			$url = self::searchWithoutDate($cat, $city);
			$out = getFilterClearBtn($url);
		}
		
		return $out;
	}
	
	/**
	 * Generate button link for the price filter removal
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function getPriceFilterClearLink($cat = null, $city = null): string
	{
		$out = '';
		
		if (self::doesPriceIsFiltered($cat, $city)) {
			$url = self::searchWithoutPrice($cat, $city);
			$out = getFilterClearBtn($url);
		}
		
		return $out;
	}
	
	/**
	 * Generate button link for the listing type filter removal
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function getTypeFilterClearLink($cat = null, $city = null): string
	{
		$out = '';
		
		if (self::doesTypeIsFiltered($cat, $city)) {
			$url = self::searchWithoutType($cat, $city);
			$out = getFilterClearBtn($url);
		}
		
		return $out;
	}
	
	/**
	 * Generate button link for the custom field filter removal
	 *
	 * @param $field
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function getCustomFieldFilterClearLink($field, $cat = null, $city = null): string
	{
		$out = '';
		
		if (self::doesCustomFieldIsFiltered($field, $cat)) {
			$url = self::searchWithoutCustomField($field, $cat, $city);
			$out = getFilterClearBtn($url);
		}
		
		return $out;
	}
}
