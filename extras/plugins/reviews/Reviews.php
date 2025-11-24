<?php

namespace extras\plugins\reviews;

use App\Helpers\DBTool;
use App\Models\Setting;
use extras\plugins\reviews\database\MigrationsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Reviews
{
	use MigrationsTrait;
	
	/**
	 * @return string
	 */
	public static function getAdminMenu(): string
	{
		$out = '<li class="sidebar-item">';
		$out .= '<a href="' . admin_url('reviews') . '" class="sidebar-link">';
		$out .= '<i data-feather="message-square" class="feather-icon"></i> ';
		$out .= '<span class="hide-menu">' . trans('reviews::messages.Reviews') . '</span>';
		$out .= '</a>';
		$out .= '</li>';
		
		return $out;
	}
	
	/**
	 * @return array
	 */
	public static function getOptions(): array
	{
		$options = [];
		$options[] = (object)[
			'name'     => trans('reviews::messages.Reviews'),
			'url'      => admin_url('reviews'),
			'btnClass' => 'btn-primary',
			'iClass'   => 'fa-regular fa-comment-dots',
		];
		$setting = Setting::active()->where('key', 'reviews')->first();
		if (!empty($setting)) {
			$options[] = (object)[
				'name'     => mb_ucfirst(trans('admin.settings')),
				'url'      => admin_url('settings/' . $setting->id . '/edit'),
				'btnClass' => 'btn-info',
			];
		}
		
		return $options;
	}
	
	/**
	 * @return bool
	 */
	public static function isPreInstalled(): bool
	{
		if (
			Schema::hasTable('reviews')
			&& Schema::hasColumn('posts', 'rating_cache')
			&& Schema::hasColumn('posts', 'rating_count')
		) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function installed(): bool
	{
		$cacheExpiration = 86400; // Cache for 1 day (60 * 60 * 24)
		
		return cache()->remember('plugins.reviews.installed', $cacheExpiration, function () {
			$setting = Setting::active()->where('key', 'reviews')->first();
			if (!empty($setting)) {
				if (
					Schema::hasTable('reviews')
					&& Schema::hasColumn('posts', 'rating_cache')
					&& Schema::hasColumn('posts', 'rating_count')
				) {
					return true;
				}
			}
			
			return false;
		});
	}
	
	/**
	 * @return bool
	 */
	public static function install(): bool
	{
		// Remove the plugin entry
		if (!self::isPreInstalled()) {
			self::uninstall();
		}
		
		try {
			// Run the plugin's install migration
			if (!self::isPreInstalled()) {
				self::migrationsInstall();
			}
			
			// Get the setting's position
			$lft = getNextSettingPosition();
			$rgt = $lft + 1;
			
			// Create plugin setting
			DB::statement('ALTER TABLE ' . DBTool::table((new Setting())->getTable()) . ' AUTO_INCREMENT = 1;');
			$pluginSetting = [
				'key'         => 'reviews',
				'name'        => 'Reviews',
				//'value'     => null,
				'description' => 'Reviews System',
				'field'       => null,
				'parent_id'   => 0,
				'lft'         => $lft,
				'rgt'         => $rgt,
				'depth'       => 1,
				'active'      => 1,
			];
			$setting = Setting::create($pluginSetting);
			if (empty($setting)) {
				return false;
			}
			
			return true;
		} catch (\Throwable $e) {
			notification($e->getMessage(), 'error');
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function uninstall(): bool
	{
		try {
			cache()->forget('plugins.reviews.installed');
		} catch (\Throwable $e) {
		}
		
		try {
			// Run the plugin's uninstall migration
			self::migrationsUninstall();
			
			// Remove the plugin setting
			$setting = Setting::where('key', 'reviews')->first();
			if (!empty($setting)) {
				$setting->delete();
			}
			
			return true;
		} catch (\Throwable $e) {
			notification($e->getMessage(), 'error');
		}
		
		return false;
	}
	
	/**
	 * @param string|null $sql
	 * @return void
	 */
	private static function execSql(?string $sql)
	{
		if (empty($sql)) {
			return;
		}
		
		$sql = str_replace('<<prefix>>', DB::getTablePrefix(), $sql);
		$sql = str_replace('__PREFIX__', DB::getTablePrefix(), $sql);
		DB::unprepared($sql);
	}
}
