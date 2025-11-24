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
use App\Http\Requests\Traits\HasPasswordInput;

class UserSettingsRequest extends Request
{
	use HasPasswordInput;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		$guard = getAuthGuard();
		
		return auth($guard)->check();
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		$rules = [];
		
		// password
		$rules = $this->passwordRules($rules);
		if ($this->filled('password')) {
			$rules['password'][] = 'confirmed';
		}
		
		return $rules;
	}
}
