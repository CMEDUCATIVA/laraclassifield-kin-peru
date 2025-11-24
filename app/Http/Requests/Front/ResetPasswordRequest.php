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

use App\Http\Requests\Traits\HasEmailInput;
use App\Http\Requests\Traits\HasPasswordInput;
use App\Http\Requests\Traits\HasPhoneInput;

class ResetPasswordRequest extends AuthRequest
{
	use HasEmailInput, HasPhoneInput, HasPasswordInput;
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		$rules = parent::rules();
		
		// token
		$rules['token'] = ['required'];
		
		// email
		$rules = $this->emailRules($rules);
		
		// phone
		$rules = $this->phoneRules($rules);
		$rules['phone_country'] = ['required_with:phone'];
		
		// password
		$rules = $this->passwordRules($rules);
		$rules['password'] = ['required', 'confirmed'];
		
		return $rules;
	}
}
