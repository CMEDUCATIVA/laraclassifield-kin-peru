<?php

namespace extras\plugins\reviews\app\Models;

use App\Helpers\Date;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\BaseModel;
use App\Models\Post;
use App\Models\Scopes\LocalizedScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[ScopedBy([LocalizedScope::class])]
class Review extends BaseModel
{
	use Crud;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'reviews';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	public $incrementing = false;
	protected $appends = ['created_at_formatted'];
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'post_id',
		'user_id',
		'rating',
		'comment',
		'approved',
		'spam',
	];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'created_at' => 'datetime',
			'updated_at' => 'datetime',
		];
	}
	
	/**
	 * @return string
	 */
	public function getPostTitleHtml(): string
	{
		$post = $this->post ?? null;
		
		return getPostUrl($post);
	}
	
	/**
	 * @return \Illuminate\Contracts\Translation\Translator|string
	 */
	public function getUserHtml()
	{
		if (!empty($this->user)) {
			return $this->user->name;
		}
		
		return trans('reviews::messages.Anonymous');
	}
	
	/**
	 * @return string
	 */
	public function getApprovedHtml(): string
	{
		$toggleIcon = ($this->approved == 1)
			? 'fa-solid fa-toggle-on'
			: 'fa-solid fa-toggle-off';
		
		return '<i class="admin-single-icon ' . $toggleIcon . '" aria-hidden="true"></i>';
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function post(): BelongsTo
	{
		return $this->belongsTo(Post::class, 'post_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeApproved(Builder $query): Builder
	{
		return $query->where('approved', 1);
	}
	
	public function scopeSpam(Builder $query): Builder
	{
		return $query->where('spam', 1);
	}
	
	public function scopeNotSpam(Builder $query): Builder
	{
		return $query->where('spam', 0);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS
	|--------------------------------------------------------------------------
	*/
	protected function createdAt(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = new Carbon($value);
				$value->timezone(Date::getAppTimeZone());
				
				return $value;
			},
		);
	}
	
	protected function createdAtFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$createdAt = $this->created_at ?? ($this->attributes['created_at'] ?? null);
				if (empty($createdAt)) {
					return null;
				}
				
				if (!$createdAt instanceof Carbon) {
					$value = new Carbon($createdAt);
					$value->timezone(Date::getAppTimeZone());
				}
				
				return Date::customFromNow($value);
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| MUTATORS
	|--------------------------------------------------------------------------
	*/
}
