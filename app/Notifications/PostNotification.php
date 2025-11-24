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

use App\Helpers\Date;
use App\Services\UrlGen;
use Illuminate\Notifications\Messages\MailMessage;

class PostNotification extends BaseNotification
{
	protected ?object $post;
	protected string $todayDateFormatted;
	protected string $todayTimeFormatted;
	
	public function __construct(?object $post)
	{
		$this->post = $post;
		
		// Get timezone
		$tz = Date::getAppTimeZone();
		
		// Get today date & time
		$this->todayDateFormatted = Date::format(now($tz));
		$this->todayTimeFormatted = now($tz)->format('H:i');
	}
	
	protected function shouldSendNotificationWhen($notifiable): bool
	{
		return !empty($this->post);
	}
	
	protected function determineViaChannels($notifiable): array
	{
		return ['mail'];
	}
	
	public function toMail($notifiable): MailMessage
	{
		$postUrl = UrlGen::post($this->post);
		
		return (new MailMessage)
			->subject(trans('mail.post_notification_title'))
			->greeting(trans('mail.post_notification_content_1'))
			->line(trans('mail.post_notification_content_2', ['advertiserName' => $this->post->contact_name]))
			->line(trans('mail.post_notification_content_3', [
				'postUrl' => $postUrl,
				'title'   => $this->post->title,
				'now'     => $this->todayDateFormatted,
				'time'    => $this->todayTimeFormatted,
			]))
			->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
	}
}
