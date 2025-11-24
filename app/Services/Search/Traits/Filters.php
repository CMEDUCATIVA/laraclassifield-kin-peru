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

namespace App\Services\Search\Traits;

use App\Services\Search\Traits\Filters\AuthorFilter;
use App\Services\Search\Traits\Filters\CategoryFilter;
use App\Services\Search\Traits\Filters\CustomFieldsFilter;
use App\Services\Search\Traits\Filters\DateFilter;
use App\Services\Search\Traits\Filters\DynamicFieldsFilter;
use App\Services\Search\Traits\Filters\KeywordFilter;
use App\Services\Search\Traits\Filters\LocationFilter;
use App\Services\Search\Traits\Filters\PostTypeFilter;
use App\Services\Search\Traits\Filters\PriceFilter;
use App\Services\Search\Traits\Filters\TagFilter;

trait Filters
{
	use AuthorFilter, CategoryFilter, KeywordFilter, LocationFilter, TagFilter,
		DateFilter, PostTypeFilter, PriceFilter, DynamicFieldsFilter, CustomFieldsFilter;
	
	protected function applyFilters(): void
	{
		if (!(isset($this->posts))) {
			return;
		}
		
		// Default Filters
		$this->posts->inCountry()->verified()->unarchived();
		if (config('settings.listing_form.listings_review_activation')) {
			$this->posts->reviewed();
		}
		
		// Author
		$this->applyAuthorFilter();
		
		// Category
		$this->applyCategoryFilter();
		
		// Keyword
		$this->applyKeywordFilter();
		
		// Location
		$this->applyLocationFilter();
		
		// Tag
		$this->applyTagFilter();
		
		// Date
		$this->applyDateFilter();
		
		// Listing Type
		$this->applyPostTypeFilter();
		
		// Price
		$this->applyPriceFilter();
		
		// Dynamic Fields
		$this->applyDynamicFieldsFilters();
		
		// Custom Fields
		$this->applyCustomFieldsFilter();
	}
}
