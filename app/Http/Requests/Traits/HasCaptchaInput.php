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

trait HasCaptchaInput
{
	/**
	 * CAPTCHA Rules
	 *
	 * @param array $rules
	 * @return array
	 */
	protected function captchaRules(array $rules = []): array
	{
		if (empty(config('settings.security.captcha'))) {
			return $rules;
		}
		
		if (config('settings.security.captcha') == 'recaptcha') {
			// reCAPTCHA
			if (config('recaptcha.site_key') && config('recaptcha.secret_key')) {
				if (!isFromApi()) {
					$rules['g-recaptcha-response'] = ['recaptcha'];
				}
			}
		} else {
			// CAPTCHA
			if (config('captcha.option') && !empty(config('captcha.option'))) {
				if (isFromApi()) {
					if (!doesRequestIsFromWebClient()) {
						if ($this->filled('captcha_key')) {
							$rules['captcha'] = [
								'required',
								'captcha_api:' . $this->get('captcha_key') . ',' . config('settings.security.captcha'),
							];
						}
					}
				} else {
					$rules['captcha'] = ['required', 'captcha'];
				}
			}
		}
		
		return $rules;
	}
}
