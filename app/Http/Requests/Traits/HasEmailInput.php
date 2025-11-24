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

namespace App\Http\Requests\Traits;

use App\Rules\BlacklistDomainRule;
use App\Rules\BlacklistEmailRule;
use App\Rules\EmailRule;

trait HasEmailInput
{
	/**
	 * Valid Email Address Rules
	 *
	 * @param array $rules
	 * @param string $field
	 * @return array
	 */
	protected function emailRules(array $rules = [], string $field = 'email'): array
	{
		if ($this->filled($field)) {
			if (isDemoEnv()) {
				if (isDemoEmailAddress($this->input($field))) {
					return $rules;
				}
			}
			
			$rules[$field][] = new EmailRule();
			$rules[$field][] = 'max:100';
			$rules[$field][] = new BlacklistEmailRule();
			$rules[$field][] = new BlacklistDomainRule();
			
			$params = [];
			if (config('settings.security.email_validator_rfc')) {
				$params[] = 'rfc';
			}
			if (config('settings.security.email_validator_strict')) {
				$params[] = 'strict';
			}
			if (extension_loaded('intl')) {
				if (config('settings.security.email_validator_dns')) {
					$params[] = 'dns';
				}
				if (config('settings.security.email_validator_spoof')) {
					$params[] = 'spoof';
				}
			}
			if (config('settings.security.email_validator_filter')) {
				$params[] = 'filter';
			}
			
			if (!empty($params)) {
				$rules[$field][] = 'email:' . implode(',', $params);
			}
		}
		
		return $rules;
	}
}
