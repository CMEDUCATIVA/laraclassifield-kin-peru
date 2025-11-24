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

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/*
 * Running the Queue Worker
 * Doc: https://laravel.com/docs/11.x/queues#running-the-queue-worker
 * php artisan queue:work
 * php artisan queue:work -v
 */

class GenerateThumbnail implements ShouldQueue
{
	use Queueable;
	
	protected ?string $filePath;
	protected string|null|bool $filePathFallback;
	protected string $resizeOptionsName;
	protected bool $webpFormat = false;
	
	/**
	 * Create a new job instance.
	 *
	 * @param string|null $filePath
	 * @param string|bool|null $filePathFallback
	 * @param string $resizeOptionsName
	 * @param bool $webpFormat
	 */
	public function __construct(
		?string          $filePath,
		string|null|bool $filePathFallback = null,
		string           $resizeOptionsName = 'picture-lg',
		bool             $webpFormat = false
	)
	{
		$this->filePath = $filePath;
		$this->filePathFallback = $filePathFallback;
		$this->resizeOptionsName = $resizeOptionsName;
		$this->webpFormat = $webpFormat;
		
		$this->onQueue('thumbs');
	}
	
	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle(): void
	{
		thumbService($this->filePath ?? null, $this->filePathFallback)
			->resize($this->resizeOptionsName, $this->webpFormat);
	}
}

