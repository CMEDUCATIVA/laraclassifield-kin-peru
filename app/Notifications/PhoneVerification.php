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

namespace App\Notifications;

use Illuminate\Notifications\Messages\VonageMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class PhoneVerification extends BaseNotification
{
	protected ?object $entity;
	protected ?array $entityRef;
	
	public function __construct(?object $entity, ?array $entityRef)
	{
		$this->entity = $entity;
		$this->entityRef = $entityRef;
	}
	
	protected function shouldSendNotificationWhen($notifiable): bool
	{
		if (empty($this->entity) || empty($this->entityRef)) {
			return false;
		}
		
		if (!isset($this->entityRef['name'])) {
			return false;
		}
		
		return (empty($this->entity->phone_verified_at) && !empty($this->entity->phone_token));
	}
	
	protected function determineViaChannels($notifiable): array
	{
		if (config('settings.sms.driver') == 'twilio') {
			return [TwilioChannel::class];
		}
		
		return ['vonage'];
	}
	
	public function toVonage($notifiable): VonageMessage
	{
		return (new VonageMessage())->content($this->getSmsMessage())->unicode();
	}
	
	public function toTwilio($notifiable): TwilioSmsMessage|\NotificationChannels\Twilio\TwilioMessage
	{
		return (new TwilioSmsMessage())->content($this->getSmsMessage());
	}
	
	// PRIVATE
	
	private function getSmsMessage(): string
	{
		$token = $this->entity->phone_token;
		
		$path = $this->entityRef['slug'] . '/verify/phone/' . $token;
		$tokenUrl = (config('plugins.domainmapping.installed'))
			? dmUrl($this->entity->country_code, $path)
			: url($path);
		
		$msg = trans('sms.phone_verification_content', [
			'appName'  => config('app.name'),
			'token'    => $token,
			'tokenUrl' => $tokenUrl,
		]);
		
		return getAsString($msg);
	}
}
