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

namespace App\Http\Requests\Front;

use App\Http\Requests\Request;
use App\Http\Requests\Traits\HasCaptchaInput;
use App\Http\Requests\Traits\HasPhoneInput;

class AuthRequest extends Request
{
	use HasPhoneInput, HasCaptchaInput;
	
	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation(): void
	{
		$input = $this->all();
		
		// auth_field
		$input['auth_field'] = getAuthField();
		
		// phone
		$input = $this->preparePhoneForValidation($this, $input, 'phone', true);
		
		request()->merge($input); // Required!
		$this->merge($input);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		$rules = [];
		
		$phoneIsEnabledAsAuthField = (config('settings.sms.enable_phone_as_auth_field') == '1');
		
		if ($phoneIsEnabledAsAuthField) {
			$authField = $this->input('auth_field');
			if (!empty($authField)) {
				$rules[$authField] = ['required'];
				
				if ($authField == 'phone') {
					$rules['phone_country'] = ['required_with:phone'];
				}
			} else {
				$rules['email'] = ['required'];
			}
		} else {
			$rules['email'] = ['required'];
		}
		
		return $this->captchaRules($rules);
	}
}
