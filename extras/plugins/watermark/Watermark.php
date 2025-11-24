<?php

namespace extras\plugins\watermark;

use App\Exceptions\Custom\CustomException;
use App\Helpers\DBTool;
use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class Watermark
{
	/**
	 * @param \Intervention\Image\Interfaces\ImageInterface $image
	 * @return \Intervention\Image\Interfaces\ImageInterface|null
	 * @throws \App\Exceptions\Custom\CustomException
	 */
	public static function apply(ImageInterface $image): ?ImageInterface
	{
		$disk = StorageDisk::getDisk();
		
		// Get the Watermark filepath from DB
		$watermark = config('settings.watermark.watermark');
		
		// Return the original image resource if $watermark is empty or if its file does not exist
		if (empty($watermark) || !$disk->exists($watermark)) {
			return $image;
		}
		
		// 70% (by default) less than actual images
		$watermarkPercentageReduction = config('settings.watermark.percentage_reduction', 70);
		
		// Image trimming tolerance
		$trimmingTolerance = (int)config('settings.watermark.image_trimming_tolerance', 0);
		
		$watermark = Image::read($disk->get($watermark));
		
		// Apply Proportional Dimensions
		if ($watermarkPercentageReduction > 0 && $watermarkPercentageReduction <= 99) {
			// Watermark will be $watermarkPercentageReduction less than the actual dimensions of images
			$realWatermarkPercentageReduction = (100 - $watermarkPercentageReduction);
			
			// Get the dimensions to which the watermark will be resized
			$watermarkResizeWidth = ceil($image->width() * ($realWatermarkPercentageReduction / 100));
			$watermarkResizeHeight = ceil($image->height() * ($realWatermarkPercentageReduction / 100));
			
			// Try something magical (before you throw in the towel)
			if ($watermarkResizeWidth >= $watermark->width() || $watermarkResizeHeight >= $watermark->height()) {
				$newImgWidth = ceil($image->width() / 3);
				$newImgHeight = ceil($image->height() / 3);
				
				$watermarkResizeWidth = ceil($newImgWidth * ($realWatermarkPercentageReduction / 100));
				$watermarkResizeHeight = ceil($newImgHeight * ($realWatermarkPercentageReduction / 100));
			}
			
			// If the watermark original dimensions (Width & Height) are greater than the resize dimensions,
			// Resize the watermark
			if ($watermark->width() > $watermarkResizeWidth && $watermark->height() > $watermarkResizeHeight) {
				$watermark = $watermark->scaleDown($watermarkResizeWidth, $watermarkResizeHeight);
			}
		}
		
		// Get the Watermark position
		$position = config('settings.watermark.position', config('watermark.position'));
		$positionX = (int)config('settings.watermark.position_x', config('watermark.position_x'));
		$positionY = (int)config('settings.watermark.position_y', config('watermark.position_y'));
		
		if ($position == 'random') {
			$positions = ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'];
			shuffle($positions);
			if (isset($positions[0])) {
				$position = $positions[0];
			}
		}
		if ($position == 'top') {
			$positionX = 0;
		}
		if ($position == 'left') {
			$positionY = 0;
		}
		if ($position == 'center') {
			$positionX = 0;
			$positionY = 0;
		}
		if ($position == 'right') {
			$positionY = 0;
		}
		if ($position == 'bottom') {
			$positionX = 0;
		}
		
		// Insert watermark!
		$errorMessage = null;
		try {
			$opacity = config('settings.watermark.opacity', 100);
			
			// Insert watermark at $position corner with 'position_x' & 'position_y' offset
			$updatedImage = $image->place($watermark, $position, $positionX, $positionY, $opacity);
		} catch (\Throwable $e) {
			$updatedImage = null;
			$errorMessage = 'Watermark Error: ' . $e->getMessage();
		}
		
		if (config('settings.watermark.errors_enabled') == '1') {
			if (!empty($errorMessage)) {
				throw new CustomException($errorMessage);
			}
		}
		
		return !is_null($updatedImage) ? $updatedImage : $image;
	}
	
	/**
	 * @return array
	 */
	public static function getOptions(): array
	{
		$options = [];
		$setting = Setting::active()->where('key', 'watermark')->first();
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
	public static function installed(): bool
	{
		$cacheExpiration = 86400; // Cache for 1 day (60 * 60 * 24)
		
		return cache()->remember('plugins.watermark.installed', $cacheExpiration, function () {
			$setting = Setting::active()->where('key', 'watermark')->first();
			if (!empty($setting)) {
				return File::exists(plugin_path('watermark', 'installed'));
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
		self::uninstall();
		
		try {
			// Check if the plugin folder is writable
			if (!self::pluginFolderIsWritable()) {
				return self::filesPermissionError();
			}
			
			// Get the setting's position
			$lft = getNextSettingPosition();
			$rgt = $lft + 1;
			
			// Create plugin setting
			DB::statement('ALTER TABLE ' . DBTool::table((new Setting())->getTable()) . ' AUTO_INCREMENT = 1;');
			$pluginSetting = [
				'key'         => 'watermark',
				'name'        => 'Watermark',
				//'value'     => null,
				'description' => 'Watermark for Ads Pictures',
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
			
			// Create plugin Installed file
			File::put(plugin_path('watermark', 'installed'), '');
			
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
			cache()->forget('plugins.watermark.installed');
		} catch (\Throwable $e) {
		}
		
		try {
			// Check if the plugin folder is writable
			if (!self::pluginFolderIsWritable()) {
				return self::filesPermissionError();
			}
			
			// Remove the plugin setting
			$setting = Setting::where('key', 'watermark')->first();
			if (!empty($setting)) {
				$setting->delete();
			}
			
			// Remove plugin Installed file
			File::delete(plugin_path('watermark', 'installed'));
			
			return true;
		} catch (\Throwable $e) {
			notification($e->getMessage(), 'error');
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	private static function pluginFolderIsWritable(): bool
	{
		$pluginPath = plugin_path('watermark');
		
		return (
			file_exists($pluginPath)
			&& is_dir($pluginPath)
			&& is_writable($pluginPath)
			&& getPerms($pluginPath) >= 755
		);
	}
	
	/**
	 * @return false
	 */
	private static function filesPermissionError(): bool
	{
		$systemUrl = admin_url('system');
		$errorMessage = 'To do this action, you have to make sure that the watermark plugin folder has writable permissions.';
		$errorMessage .= ' Click <a href="'.$systemUrl.'" target="_blank">here</a> to check all the system\'s files permissions.';
		
		notification($errorMessage, 'error');
		
		return false;
	}
}
