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

namespace App\Http\Controllers\Web\Admin;

use App\Helpers\DBTool;
use App\Helpers\DBTool\DBEncoding;
use App\Http\Controllers\Web\Install\Traits\Install\CheckerTrait;
use App\Http\Controllers\Web\Admin\Panel\PanelController;

class SystemController extends PanelController
{
	use CheckerTrait;
	
	public function systemInfo()
	{
		// System
		$system = [];
		
		// PHP-CLI Version Info
		$phpBinaryVersion = $this->getPhpBinaryVersion();
		if (empty($phpBinaryVersion)) {
			$requiredPhpVersion = $this->getComposerRequiredPhpVersion();
			$phpBinaryVersion = "<span class='font-weight-bolder'>IMPORTANT:</span> ";
			$phpBinaryVersion .= "You have to check your server's <code>PHP-cli</code> version manually. ";
			$phpBinaryVersion .= "This need to be version <code>$requiredPhpVersion or greater</code> to allow you to run the cron job commands. ";
			$phpBinaryVersion .= "<a href='https://stackoverflow.com/a/9315749/9869030' target='_blank'>More Info</a>";
			$phpBinaryVersion = '<a href="javascript: void(0);" data-bs-toggle="popover" data-html="true" title="PHP-CLI" data-bs-content="' . $phpBinaryVersion . '">Action Required</a>';
		}
		
		$system[] = [
			'name'  => "User-Agent",
			'value' => request()->server('HTTP_USER_AGENT'),
		];
		$system[] = [
			'name'  => "Server Software",
			'value' => request()->server('SERVER_SOFTWARE'),
		];
		$system[] = [
			'name'  => "Document Root",
			'value' => relativeAppPath(request()->server('DOCUMENT_ROOT')),
		];
		$system[] = [
			'name'  => "PHP (CGI/FPM) version",
			'value' => PHP_VERSION,
		];
		$system[] = [
			'name'  => 'PHP-CLI version',
			'value' => $phpBinaryVersion,
		];
		$system[] = [
			'name'  => "Database Server",
			'value' => DBTool::isMariaDB() ? 'MariaDB' : 'MySQL',
		];
		$system[] = [
			'name'  => "Database Server version",
			'value' => DBTool::getMySqlFullVersion(),
		];
		$databaseServerEncoding = DBEncoding::getServerCharsetAndCollation();
		if (!empty($databaseServerEncoding)) {
			$charset = data_get($databaseServerEncoding, 'charset');
			$collation = data_get($databaseServerEncoding, 'collation');
			$system[] = [
				'name'  => "Database Server Encoding",
				'value' => 'Charset: <code>' . $charset . '</code> Collation: <code>' . $collation . '</code>',
			];
		}
		$databaseConnectionEncoding = DBEncoding::getConnectionCharsetAndCollation();
		if (!empty($databaseConnectionEncoding)) {
			$charset = data_get($databaseConnectionEncoding, 'charset');
			$collation = data_get($databaseConnectionEncoding, 'collation');
			$system[] = [
				'name'  => "Database Connection Encoding",
				'value' => 'Charset: <code>' . $charset . '</code> Collation: <code>' . $collation . '</code>',
			];
		}
		$selectedDatabaseEncoding = DBEncoding::getDatabaseCharsetAndCollation();
		if (!empty($selectedDatabaseEncoding)) {
			$charset = data_get($selectedDatabaseEncoding, 'charset');
			$collation = data_get($selectedDatabaseEncoding, 'collation');
			$system[] = [
				'name'  => "Database Encoding",
				'value' => 'Charset: <code>' . $charset . '</code> Collation: <code>' . $collation . '</code>',
			];
		}
		
		// Get Components & Permissions
		$components = array_merge($this->getComponents(), $this->getAdvancedComponents());
		$permissions = $this->getPermissions();
		$imageFormats = $this->getImageFormats();
		
		$data = [
			'system'       => $system,
			'components'   => $components,
			'permissions'  => $permissions,
			'imageFormats' => $imageFormats,
		];
		
		$data['title'] = trans('admin.system_info');
		
		return view('admin.system', $data);
	}
	
	/**
	 * @return array
	 */
	protected function getAdvancedComponents(): array
	{
		$components = [];
		
		// Database version
		$databaseCurrentVersion = DBTool::getMySqlVersion();
		if (!DBTool::isMariaDB()) {
			$databaseMinVersion = '5.6';
			$databaseRecommendedVersion = '5.7';
			$databaseIsMySqlDeprecatedVersion = (
				(version_compare($databaseCurrentVersion, $databaseMinVersion) >= 0)
				&& (version_compare($databaseCurrentVersion, $databaseMinVersion . '.9') <= 0)
			);
			$databaseIsMySqlRightVersion = DBTool::isMySqlMinVersion($databaseRecommendedVersion);
			$components[] = [
				'type'     => 'component',
				'name'     => 'Database Server Version',
				'required' => true,
				'isOk'     => ($databaseIsMySqlDeprecatedVersion || $databaseIsMySqlRightVersion),
				'warning'  => 'The minimum MySQL version required is: <code>' . $databaseMinVersion . '</code>, '
					. 'version <code>' . $databaseRecommendedVersion . '</code> or greater is recommended.',
				'success'  => $databaseIsMySqlDeprecatedVersion
					? 'MySQL version <code>' . $databaseCurrentVersion . '</code> is not recommended. '
					. 'Upgrade your database to version <code>' . $databaseRecommendedVersion . '</code> or greater.'
					: 'MySQL version <code>' . $databaseCurrentVersion . '</code> is valid.',
			];
		} else {
			$databaseMinVersion = '10.2.3';
			$databaseIsMariaDbRightVersion = (DBTool::isMySqlMinVersion($databaseMinVersion));
			$components[] = [
				'type'     => 'component',
				'name'     => 'Database Server Version',
				'required' => true,
				'isOk'     => ($databaseIsMariaDbRightVersion),
				'warning'  => 'MariaDB version <code>' . $databaseMinVersion . '</code> or greater is required.',
				'success'  => 'MariaDB version <code>' . $databaseCurrentVersion . '</code> is valid.',
			];
		}
		
		// Server (Apache or Nginx) encoding
		$validCharset = 'UTF-8';
		$currentCharset = ini_get('default_charset');
		$components[] = [
			'type'     => 'component',
			'name'     => 'Server default_charset',
			'required' => false,
			'isOk'     => (strtolower(ini_get('default_charset')) == 'utf-8'),
			'warning'  => "The server <code>default_charset</code> is: <code>$currentCharset</code>. <code>$validCharset</code> is required.",
			'success'  => "The server <code>default_charset</code> (<code>$validCharset</code>) is valid.",
		];
		
		// Database charset & collation
		// Get the default charset & collation
		$defaultCharset = config('larapen.core.database.encoding.default.charset', 'utf8mb4');
		$defaultCollation = config('larapen.core.database.encoding.default.collation', 'utf8mb4_unicode_ci');
		
		// Get valid recommended charset & collation
		$validRecommendedEncoding = DBEncoding::getFirstValidRecommendedCharsetAndCollation();
		if (!empty($validRecommendedEncoding)) {
			$defaultCharset = $validRecommendedEncoding['charset'] ?? $defaultCharset;
			$defaultCollation = $validRecommendedEncoding['collation'] ?? $defaultCollation;
		}
		
		// Get server charset & collation
		$databaseServerEncoding = DBEncoding::getServerCharsetAndCollation();
		$serverCharset = $databaseServerEncoding['charset'] ?? $defaultCharset;
		$serverCollation = $databaseServerEncoding['collation'] ?? $defaultCollation;
		
		// Get the connection charset & collation (from the /.env file)
		$databaseCharset = DBTool::getDatabaseConnectionInfo('charset');
		$databaseCollation = DBTool::getDatabaseConnectionInfo('collation');
		
		$recommendedEncodings = (array)config('larapen.core.database.encoding.recommended');
		if (array_key_exists($databaseCharset, $recommendedEncodings)) {
			$recommendedCollations = (array)$recommendedEncodings[$databaseCharset];
			if (!empty($recommendedCollations)) {
				if (!in_array($databaseCollation, $recommendedCollations)) {
					$databaseCollation = reset($recommendedCollations);
				}
			} else {
				$databaseCharset = $defaultCharset;
				$databaseCollation = $defaultCollation;
			}
		} else {
			$databaseCharset = $defaultCharset;
			$databaseCollation = $defaultCollation;
		}
		
		$charsetVars = [
			'DEFAULT_CHARACTER_SET_NAME',
			'character_set_server',
			'character_set_connection',
			'character_set_database',
		];
		$collationVars = [
			'DEFAULT_COLLATION_NAME',
			'collation_server',
			'collation_connection',
			'collation_database',
		];
		$envFileVars = [
			'<span class="fw-bold">DB_CHARSET=</span><code>' . $serverCharset . '</code>',
			'<span class="fw-bold">DB_COLLATION=</span><code>' . $serverCollation . '</code>',
		];
		
		$and = t('_and_');
		
		$charsetVarsStr = collect($charsetVars)
			->map(fn ($item) => ('<span class="fw-bold">' . $item . '</span>'))
			->join(', ', $and);
		
		$collationVarsStr = collect($collationVars)
			->map(fn ($item) => ('<span class="fw-bold">' . $item . '</span>'))
			->join(', ', $and);
		
		$envFileVarsStr = collect($envFileVars)->join('<br>');
		
		$warning = '';
		if ($serverCharset != $databaseCharset || $serverCollation != $databaseCollation) {
			$btnUrl = admin_url('actions/update_database_charset_collation');
			$warning .= 'The <code>/.env</code> file\'s charset & collation variables need to be updated like this: ';
			$warning .= '<br>' . $envFileVarsStr;
			$warning .= '<br><br>';
			$warning .= '<a href="' . $btnUrl . '" class="btn btn-primary btn-sm confirm-simple-action">Update the /.env file</a>';
			$warning .= '<br><br>';
			$warning .= 'If the error persists, the database server variables: ' . $charsetVarsStr;
		} else {
			$warning .= 'The database server variables: ' . $charsetVarsStr;
		}
		$warning .= ' must be set to <code>' . $databaseCharset . '</code> in the server configuration.';
		$warning .= ' Additionally, the variables: ' . $collationVarsStr;
		$warning .= ' must be set to <code>' . $databaseCollation . '</code> in the server configuration.';
		$warning .= '<br>Note: Cache needs to be cleared after configuration changes.';
		
		$components[] = [
			'type'     => 'component',
			'name'     => 'Database Server Character Set & Collation',
			'required' => false,
			'isOk'     => DBEncoding::isValidCharsetAndCollation(),
			'warning'  => $warning,
			'success'  => "The database server character set (<code>$databaseCharset</code>) and collation (<code>$databaseCollation</code>) are valid.",
		];
		
		return $components;
	}
}
