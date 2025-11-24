<?php

namespace extras\plugins\watermark\app\Models\Setting;

use App\Helpers\Files\Upload;

/*
 * settings.watermark.option
 */

class WatermarkSetting
{
	public static function passedValidation($request)
	{
		// Default system's images dimensions
		$width = (int)config('settings.upload.img_resize_width', 1000);
		$height = (int)config('settings.upload.img_resize_height', 1000);
		
		// Default watermark image dimensions
		$watermarkWidth = (int)$request->input('width', config('watermark.width', 150));
		$watermarkHeight = (int)$request->input('height', config('watermark.height', 150));
		
		// Check the watermark size related to $image size
		$watermarkWidth = ($watermarkWidth > $width) ? $width : $watermarkWidth;
		$watermarkHeight = ($watermarkHeight > $height) ? $height : $watermarkHeight;
		$request->request->set('width', $watermarkWidth);
		$request->request->set('height', $watermarkHeight);
		
		// Get size parameters
		$param = [
			'attribute' => 'watermark',
			'destPath'  => 'app/logo',
			'width'     => $watermarkWidth,
			'height'    => $watermarkHeight,
			'filename'  => 'watermark-',
		];
		$file = $request->hasFile($param['attribute'])
			? $request->file($param['attribute'])
			: $request->input($param['attribute']);
		
		$request->request->set($param['attribute'], Upload::image($param['destPath'], $file, $param));
		
		return $request;
	}
	
	public static function getValues($value, $disk)
	{
		if (empty($value)) {
			
			$value['width'] = '150';
			$value['height'] = '150';
			$value['position'] = 'bottom-right';
			$value['position_x'] = '20';
			$value['position_y'] = '20';
			$value['errors_enabled'] = '0';
			
		} else {
			
			if (!isset($value['width'])) {
				$value['width'] = '150';
			}
			if (!isset($value['height'])) {
				$value['height'] = '150';
			}
			if (!isset($value['position'])) {
				$value['position'] = 'bottom-right';
			}
			if (!isset($value['position_x'])) {
				$value['position_x'] = '20';
			}
			if (!isset($value['position_y'])) {
				$value['position_y'] = '20';
			}
			if (!isset($value['errors_enabled'])) {
				$value['errors_enabled'] = '0';
			}
			
		}
		
		return $value;
	}
	
	public static function setValues($value, $setting)
	{
		$attribute = 'watermark';
		
		// If 'watermark' value doesn't exist, don't make the upload and save data
		if (!array_key_exists($attribute, $value)) {
			return $value;
		}
		
		if (empty($value[$attribute])) {
			$value[$attribute] = null;
		}
		
		return $value;
	}
	
	public static function getFields($diskName)
	{
		// Default system's images dimensions
		$width = (int)config('settings.upload.img_resize_width', 1000);
		$height = (int)config('settings.upload.img_resize_height', 1000);
		
		$fields = [
			[
				'name'  => 'file_title',
				'type'  => 'custom_html',
				'value' => trans('watermark::messages.file_title'),
			],
			[
				'name'              => 'watermark',
				'label'             => trans('watermark::messages.watermark_label'),
				'type'              => 'image',
				'upload'            => true,
				'disk'              => 'public',
				'default'           => null,
				'hint'              => trans('watermark::messages.watermark_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-12',
				],
				'newline'           => true,
				'plugin'            => 'watermark',
			],
			
			[
				'name'  => 'file_info',
				'type'  => 'custom_html',
				'value' => trans('watermark::messages.file_info'),
			],
			[
				'name'              => 'width',
				'label'             => trans('watermark::messages.width_label'),
				'type'              => 'number',
				'attributes'        => [
					'min'  => 50,
					'max'  => $width,
					'step' => 1,
				],
				'hint'              => trans('watermark::messages.width_hint', ['width' => 300, 'max' => $width]),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'height',
				'label'             => trans('watermark::messages.height_label'),
				'type'              => 'number',
				'attributes'        => [
					'min'  => 25,
					'max'  => $height,
					'step' => 1,
				],
				'hint'              => trans('watermark::messages.height_hint', ['height' => 300, 'max' => $height]),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'percentage_reduction',
				'label'             => trans('watermark::messages.percentage_reduction_label'),
				'type'              => 'select2_from_array',
				'options'           => self::reductionPercentageList(),
				'hint'              => trans('watermark::messages.percentage_reduction_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'opacity',
				'label'             => trans('watermark::messages.opacity_label'),
				'type'              => 'select2_from_array',
				'options'           => self::opacityPercentageList(),
				'hint'              => trans('watermark::messages.opacity_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'position_title',
				'type'  => 'custom_html',
				'value' => trans('watermark::messages.position_title'),
			],
			[
				'name'  => 'position_info',
				'type'  => 'custom_html',
				'value' => trans('watermark::messages.position_info'),
			],
			[
				'name'              => 'position',
				'label'             => trans('watermark::messages.position_label'),
				'type'              => 'select_from_array',
				'options'           => [
					'top-left'     => trans('watermark::messages.op_top_left'),
					'top'          => trans('watermark::messages.op_top'),
					'top-right'    => trans('watermark::messages.op_top_right'),
					'left'         => trans('watermark::messages.op_left'),
					'center'       => trans('watermark::messages.op_center'),
					'right'        => trans('watermark::messages.op_right'),
					'bottom-left'  => trans('watermark::messages.op_bottom_left'),
					'bottom'       => trans('watermark::messages.op_bottom'),
					'bottom-right' => trans('watermark::messages.op_bottom_right'),
					'random'       => trans('watermark::messages.op_random'),
				],
				'hint'              => trans('watermark::messages.position_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'position_x',
				'label'             => trans('watermark::messages.position_x_label'),
				'type'              => 'number',
				'hint'              => trans('watermark::messages.position_x_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-3',
				],
			],
			[
				'name'              => 'position_y',
				'label'             => trans('watermark::messages.position_y_label'),
				'type'              => 'number',
				'hint'              => trans('watermark::messages.position_y_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-3',
				],
			],
			
			[
				'name'  => 'other_options_title',
				'type'  => 'custom_html',
				'value' => trans('watermark::messages.other_options_title'),
			],
			[
				'name'              => 'errors_enabled',
				'label'             => trans('watermark::messages.errors_enabled_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('watermark::messages.errors_enabled_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-12',
				],
			],
		];
		
		return $fields;
	}
	
	/**
	 * @return array
	 */
	private static function reductionPercentageList(): array
	{
		$array = self::percentageList();
		
		return array_slice($array, 1, -1);
	}
	
	/**
	 * @return array
	 */
	private static function opacityPercentageList(): array
	{
		return array_reverse(self::percentageList(), true);
	}
	
	/**
	 * @return array
	 */
	private static function percentageList(): array
	{
		$array = [];
		
		for ($i = 0; $i <= 100; $i += 5) {
			$array[$i] = $i . '%';
		}
		
		return $array;
	}
}
