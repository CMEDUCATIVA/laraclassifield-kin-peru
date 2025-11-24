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

namespace App\Services;

use App\Helpers\Arr;
use App\Services\UrlGen\SearchTrait;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class UrlGen
{
	use SearchTrait;
	
	/**
	 * @param $entry
	 * @param bool $encoded
	 * @return string
	 */
	public static function postPath($entry, bool $encoded = false): string
	{
		$entry = is_array($entry) ? Arr::toObject($entry) : $entry;
		
		if (isset($entry->id) && isset($entry->title)) {
			$preview = !isVerifiedPost($entry) ? '?preview=1' : '';
			
			$slug = ($encoded) ? rawurlencode($entry->slug) : $entry->slug;
			
			$path = str_replace(['{slug}', '{hashableId}', '{id}'], [$slug, hashId($entry->id), $entry->id], config('routes.post'));
			$path = $path . $preview;
		} else {
			$path = '/';
		}
		
		return getAsString($path);
	}
	
	/**
	 * @param $id
	 * @param string $slug
	 * @return string
	 */
	public static function postPathBasic($id, string $slug = 'listing-slug'): string
	{
		$path = str_replace(['{slug}', '{hashableId}', '{id}'], [$slug, $id, $id], config('routes.post'));
		
		return getAsString($path);
	}
	
	/**
	 * @param $entry
	 * @param bool $encoded
	 * @return string
	 */
	public static function postUri($entry, bool $encoded = false): string
	{
		return self::postPath($entry, $encoded);
	}
	
	/**
	 * @param $entry
	 * @return string
	 */
	public static function post($entry): string
	{
		$entry = is_array($entry) ? Arr::toObject($entry) : $entry;
		
		if (config('plugins.domainmapping.installed')) {
			$url = dmUrl($entry->country_code, self::postUri($entry));
		} else {
			$url = url(self::postPath($entry));
		}
		
		return urlQuery($url)->toString();
	}
	
	/**
	 * @param $entry
	 * @return string
	 */
	public static function reportPost($entry): string
	{
		$entry = is_array($entry) ? Arr::toObject($entry) : $entry;
		
		$url = url('posts/' . hashId($entry->id) . '/report');
		
		return urlQuery($url)->toString();
	}
	
	/**
	 * @param bool $httpError
	 * @return string
	 */
	public static function addPost(bool $httpError = false): string
	{
		$url = (config('settings.listing_form.publication_form_type') == '2')
			? url('create')
			: url('posts/create');
		
		return urlQuery($url)->toString();
	}
	
	/**
	 * @param $entry
	 * @return string
	 */
	public static function editPost($entry): string
	{
		$entry = is_array($entry) ? Arr::toObject($entry) : $entry;
		
		if (isset($entry->id)) {
			$url = (config('settings.listing_form.publication_form_type') == '2')
				? url('edit/' . $entry->id)
				: url('posts/' . $entry->id . '/edit');
		} else {
			$url = '/';
		}
		
		return urlQuery($url)->toString();
	}
	
	/**
	 * @param string|null $countryCode
	 * @return string
	 */
	public static function companies(string $countryCode = null): string
	{
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		
		$countryCodePath = '';
		if (config('settings.seo.multi_country_urls')) {
			if (!empty($countryCode)) {
				$countryCodePath = strtolower($countryCode) . '/';
			}
		}
		
		$url = url($countryCodePath . config('routes.companies'));
		
		return urlQuery($url)->toString();
	}
	
	/**
	 * @param $entry
	 * @return string
	 */
	public static function page($entry): string
	{
		$entry = is_array($entry) ? Arr::toObject($entry) : $entry;
		
		if (isset($entry->slug)) {
			$path = str_replace(['{slug}'], [$entry->slug], config('routes.pageBySlug'));
			$url = url($path);
		} else {
			$url = '/';
		}
		
		return urlQuery($url)->toString();
	}
	
	/**
	 * @param string|null $countryCode
	 * @return string
	 */
	public static function sitemap(string $countryCode = null): string
	{
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		
		$countryCodePath = '';
		if (config('settings.seo.multi_country_urls')) {
			if (!empty($countryCode)) {
				$countryCodePath = strtolower($countryCode) . '/';
			}
		}
		
		$path = str_replace(['{countryCode}/'], [''], config('routes.sitemap'));
		$url = url($countryCodePath . $path);
		
		return urlQuery($url)->toString();
	}
	
	public static function countries(): string
	{
		$url = url(config('routes.countries'));
		
		if (doesCountriesPageCanBeLinkedToTheHomepage()) {
			$url = str(config('app.url'))->finish('/')->toString();
			
			$crawler = new CrawlerDetect();
			if (!$crawler->isCrawler()) {
				$url = $url . 'locale/' . config('app.locale');
			}
		}
		
		return urlQuery($url)->toString();
	}
	
	public static function contact(): string
	{
		return urlQuery(config('routes.contact'))->toString();
	}
	
	public static function pricing(): string
	{
		return urlQuery(config('routes.pricing'))->toString();
	}
	
	public static function loginPath(): string
	{
		return getAsString(config('routes.login'));
	}
	
	public static function logoutPath(): string
	{
		return getAsString(config('routes.logout'));
	}
	
	public static function registerPath(): string
	{
		return getAsString(config('routes.register'));
	}
	
	public static function login(): string
	{
		return urlQuery(self::loginPath())->toString();
	}
	
	public static function logout(): string
	{
		return urlQuery(self::logoutPath())->toString();
	}
	
	public static function register(): string
	{
		return urlQuery(self::registerPath())->toString();
	}
}
