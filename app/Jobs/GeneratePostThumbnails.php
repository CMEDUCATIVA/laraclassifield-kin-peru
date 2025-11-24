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

use App\Models\Post;
use App\Services\Thumbnail\PostThumbnail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/*
 * Running the Queue Worker
 * Doc: https://laravel.com/docs/11.x/queues#running-the-queue-worker
 * php artisan queue:work
 * php artisan queue:work -v
 */

class GeneratePostThumbnails implements ShouldQueue
{
	use Queueable;
	
	protected Post $post;
	
	/**
	 * Create a new job instance.
	 *
	 * @param \App\Models\Post $post
	 */
	public function __construct(Post $post)
	{
		$this->post = $post;
		
		$this->onQueue('thumbs');
	}
	
	/**
	 * Execute the job.
	 *
	 * @param \App\Services\Thumbnail\PostThumbnail $thumbnailService
	 * @return void
	 */
	public function handle(PostThumbnail $thumbnailService): void
	{
		$thumbnailService->generateFor($this->post);
	}
}

