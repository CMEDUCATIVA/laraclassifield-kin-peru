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

use App\Helpers\Arr;
use App\Services\UrlGen;

trait FiltersCleaner
{
	use FiltersCleanerBtn;
	
	/**
	 * Remove category from filters
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function searchWithoutCategory($cat = null, $city = null): string
	{
		$url = request()->fullUrl();
		
		$cat = is_array($cat) ? Arr::toObject($cat) : $cat;
		$city = is_array($city) ? Arr::toObject($city) : $city;
		
		if (self::doesCategoryIsFiltered($cat)) {
			$paramsToRemove = ['page', 'cf', 'minPrice', 'maxPrice'];
			if (!empty($cat)) {
				$paramsToRemove[] = 'sc';
				if (empty($cat->parent)) {
					$paramsToRemove[] = 'c';
				}
			}
			
			$url = urlQuery(UrlGen::search())->removeParameters($paramsToRemove)->toString();
			
			if (!empty($city)) {
				if (empty($cat)) {
					$url = UrlGen::city($city);
				}
			} else {
				if (!empty($cat->parent)) {
					$url = UrlGen::category($cat->parent, null, $city);
				}
			}
		}
		
		return $url;
	}
	
	/**
	 * Remove city from filters
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function searchWithoutCity($cat = null, $city = null): string
	{
		$url = request()->fullUrl();
		
		$cat = is_array($cat) ? Arr::toObject($cat) : $cat;
		$city = is_array($city) ? Arr::toObject($city) : $city;
		
		if (self::doesCityIsFiltered($city)) {
			$paramsToRemove = ['l', 'r', 'location', 'distance', 'page'];
			$url = urlQuery(UrlGen::search())->removeParameters($paramsToRemove)->toString();
			
			if (!empty($cat)) {
				if (empty($city)) {
					$url = UrlGen::category($cat);
				}
			}
		}
		
		return $url;
	}
	
	/**
	 * Remove date from filters
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function searchWithoutDate($cat = null, $city = null): string
	{
		$url = request()->fullUrl();
		
		$cat = is_array($cat) ? Arr::toObject($cat) : $cat;
		$city = is_array($city) ? Arr::toObject($city) : $city;
		
		if (self::doesDateIsFiltered($cat, $city)) {
			$params = [];
			if (!empty($cat) && !empty($cat->id)) {
				$params['c'] = $cat->id;
				if (!empty($cat->parent)) {
					$params['c'] = $cat->parent->id;
					$params['sc'] = $cat->id;
				}
			}
			if (!empty($city) && !empty($city->id)) {
				$params['l'] = $city->id;
			}
			
			$paramsToRemove = ['page', 'postedDate'];
			$url = urlQuery(UrlGen::search())->removeParameters($paramsToRemove)->setParameters($params)->toString();
		}
		
		return $url;
	}
	
	/**
	 * Remove price from filters
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function searchWithoutPrice($cat = null, $city = null): string
	{
		$url = request()->fullUrl();
		
		$cat = is_array($cat) ? Arr::toObject($cat) : $cat;
		$city = is_array($city) ? Arr::toObject($city) : $city;
		
		if (self::doesPriceIsFiltered($cat, $city)) {
			$params = [];
			if (!empty($cat) && !empty($cat->id)) {
				$params['c'] = $cat->id;
				if (!empty($cat->parent)) {
					$params['c'] = $cat->parent->id;
					$params['sc'] = $cat->id;
				}
			}
			if (!empty($city) && !empty($city->id)) {
				$params['l'] = $city->id;
			}
			
			$paramsToRemove = ['page', 'minPrice', 'maxPrice'];
			$url = urlQuery(UrlGen::search())->removeParameters($paramsToRemove)->setParameters($params)->toString();
		}
		
		return $url;
	}
	
	/**
	 * Remove listing type from filters
	 *
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function searchWithoutType($cat = null, $city = null): string
	{
		$url = request()->fullUrl();
		
		$cat = is_array($cat) ? Arr::toObject($cat) : $cat;
		$city = is_array($city) ? Arr::toObject($city) : $city;
		
		if (self::doesTypeIsFiltered($cat, $city)) {
			$params = [];
			if (!empty($cat) && !empty($cat->id)) {
				$params['c'] = $cat->id;
				if (!empty($cat->parent)) {
					$params['c'] = $cat->parent->id;
					$params['sc'] = $cat->id;
				}
			}
			if (!empty($city) && !empty($city->id)) {
				$params['l'] = $city->id;
			}
			
			$paramsToRemove = ['page', 'type'];
			$url = urlQuery(UrlGen::search())->removeParameters($paramsToRemove)->setParameters($params)->toString();
		}
		
		return $url;
	}
	
	/**
	 * Remove a specific custom field from filters
	 *
	 * @param $field
	 * @param null $cat
	 * @param null $city
	 * @return string
	 */
	public static function searchWithoutCustomField($field, $cat = null, $city = null): string
	{
		$url = request()->fullUrl();
		
		$cat = is_array($cat) ? Arr::toObject($cat) : $cat;
		$city = is_array($city) ? Arr::toObject($city) : $city;
		
		if (self::doesCustomFieldIsFiltered($field, $cat)) {
			$params = [];
			if (!empty($cat) && !empty($cat->id)) {
				$params['c'] = $cat->id;
				if (!empty($cat->parent)) {
					$params['c'] = $cat->parent->id;
					$params['sc'] = $cat->id;
				}
			}
			if (!empty($city) && !empty($city->id)) {
				$params['l'] = $city->id;
			}
			
			$paramsToRemove = ['page', $field];
			$url = urlQuery(UrlGen::search())->removeParameters($paramsToRemove)->setParameters($params)->toString();
		}
		
		return $url;
	}
}
