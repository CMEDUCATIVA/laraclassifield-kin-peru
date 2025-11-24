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

namespace App\Helpers\DBTool;

use App\Helpers\DBTool;
use App\Helpers\DotenvEditor;
use Illuminate\Support\Facades\DB;

class DBEncoding
{
	/**
	 * Get the server's charset & collation using PDO
	 *
	 * @param \PDO|null $pdo
	 * @return array
	 */
	public static function getServerCharsetAndCollation(\PDO $pdo = null): array
	{
		$encoding = [];
		
		try {
			if (empty($pdo)) {
				if (!appIsInstalled()) return [];
				$pdo = DB::connection()->getPdo();
			}
			
			// Query to get the default charset and collation
			$sql = "SELECT @@character_set_server AS charset, @@collation_server AS collation";
			$query = $pdo->query($sql);
			
			// Fetch the result as an associative array
			$encoding = $query->fetch(\PDO::FETCH_ASSOC);
			
			if (empty($encoding['charset'])) {
				$charsetSql = "SHOW VARIABLES LIKE 'character_set_server'";
				$charset = $pdo->query($charsetSql)->fetch(\PDO::FETCH_ASSOC);
				$encoding['charset'] = $charset['Value'] ?? null;
			}
			
			if (empty($encoding['collation'])) {
				$collationSql = "SHOW VARIABLES LIKE 'collation_server'";
				$collation = $pdo->query($collationSql)->fetch(\PDO::FETCH_ASSOC);
				$encoding['collation'] = $collation['Value'] ?? null;
			}
			
			if (empty($encoding['charset']) || empty($encoding['collation'])) {
				return [];
			}
		} catch (\PDOException $e) {
		}
		
		return $encoding;
	}
	
	/**
	 * Get the connection's charset & collation using PDO
	 * Note: Can be changed in the Laravel's /.env file
	 *
	 * @param \PDO|null $pdo
	 * @return array
	 */
	public static function getConnectionCharsetAndCollation(\PDO $pdo = null): array
	{
		$encoding = [];
		
		try {
			if (empty($pdo)) {
				if (!appIsInstalled()) return [];
				$pdo = DB::connection()->getPdo();
			}
			
			// Query to get the default charset and collation
			$sql = "SELECT @@character_set_connection AS charset, @@collation_connection AS collation";
			$query = $pdo->query($sql);
			
			// Fetch the result as an associative array
			$encoding = $query->fetch(\PDO::FETCH_ASSOC);
			
			if (empty($encoding['charset'])) {
				$charsetSql = "SHOW VARIABLES LIKE 'character_set_connection'";
				$charset = $pdo->query($charsetSql)->fetch(\PDO::FETCH_ASSOC);
				$encoding['charset'] = $charset['Value'] ?? null;
			}
			
			if (empty($encoding['collation'])) {
				$collationSql = "SHOW VARIABLES LIKE 'collation_connection'";
				$collation = $pdo->query($collationSql)->fetch(\PDO::FETCH_ASSOC);
				$encoding['collation'] = $collation['Value'] ?? null;
			}
			
			if (empty($encoding['charset']) || empty($encoding['collation'])) {
				return [];
			}
		} catch (\PDOException $e) {
		}
		
		return $encoding;
	}
	
	/**
	 * Get the database's charset & collation using PDO
	 *
	 * @param \PDO|null $pdo
	 * @return array
	 */
	public static function getDatabaseCharsetAndCollation(\PDO $pdo = null): array
	{
		$encoding = [];
		
		try {
			if (empty($pdo)) {
				if (!appIsInstalled()) return [];
				$pdo = DB::connection()->getPdo();
			}
			
			// Query to get the default charset and collation
			$sql = "SELECT @@character_set_database AS charset, @@collation_database AS collation";
			$query = $pdo->query($sql);
			
			// Fetch the result as an associative array
			$encoding = $query->fetch(\PDO::FETCH_ASSOC);
			
			if (empty($encoding['charset'])) {
				$charsetSql = "SHOW VARIABLES LIKE 'character_set_database'";
				$charset = $pdo->query($charsetSql)->fetch(\PDO::FETCH_ASSOC);
				$encoding['charset'] = $charset['Value'] ?? null;
			}
			
			if (empty($encoding['collation'])) {
				$collationSql = "SHOW VARIABLES LIKE 'collation_database'";
				$collation = $pdo->query($collationSql)->fetch(\PDO::FETCH_ASSOC);
				$encoding['collation'] = $collation['Value'] ?? null;
			}
			
			if (empty($encoding['charset']) || empty($encoding['collation'])) {
				return [];
			}
		} catch (\PDOException $e) {
		}
		
		return $encoding;
	}
	
	/**
	 * @param \PDO|null $pdo
	 * @return array|null
	 */
	public static function getFirstValidRecommendedCharsetAndCollation(\PDO $pdo = null): ?array
	{
		$recommendedEncodings = (array)config('larapen.core.database.encoding.recommended');
		if (empty($recommendedEncodings)) return null;
		
		try {
			if (empty($pdo)) {
				$pdo = DB::connection()->getPdo();
			}
			
			foreach ($recommendedEncodings as $charset => $collations) {
				// Check if the charset is valid
				if (self::isValidCharset($charset, $pdo)) {
					// Check for the first valid collation in this charset
					foreach ($collations as $collation) {
						if (self::isValidCollation($collation, $charset, $pdo)) {
							// Return the first valid charset and collation
							return [
								'charset'   => $charset,
								'collation' => $collation,
							];
						}
					}
				}
			}
		} catch (\Throwable $e) {
		}
		
		return null;
	}
	
	/**
	 * @param array $databaseServerEncoding
	 * @param \PDO|null $pdo
	 * @return array
	 */
	public static function getRecommendedCharsetAndCollation(array $databaseServerEncoding = [], \PDO $pdo = null): array
	{
		$databaseServerCharset = $databaseServerEncoding['charset'] ?? '';
		$databaseServerCollation = $databaseServerEncoding['collation'] ?? '';
		
		$defaultCharset = config('larapen.core.database.encoding.default.charset', 'utf8mb4');
		$defaultCollation = config('larapen.core.database.encoding.default.collation', 'utf8mb4_unicode_ci');
		
		if (!str_starts_with(strtolower($databaseServerCharset), 'utf8')) {
			$defaultCharset = config('larapen.core.database.encoding.fallback.charset', 'utf8');
			$defaultCollation = config('larapen.core.database.encoding.fallback.collation', 'utf8_unicode_ci');
		}
		
		$validRecommendedEncoding = self::getFirstValidRecommendedCharsetAndCollation($pdo);
		if (!empty($validRecommendedEncoding)) {
			$defaultCharset = $validRecommendedEncoding['charset'] ?? $defaultCharset;
			$defaultCollation = $validRecommendedEncoding['collation'] ?? $defaultCollation;
		}
		
		$recommendedEncodings = (array)config('larapen.core.database.encoding.recommended');
		if (array_key_exists($databaseServerCharset, $recommendedEncodings)) {
			$recommendedCollations = (array)$recommendedEncodings[$databaseServerCharset];
			if (!empty($recommendedCollations)) {
				if (!in_array($databaseServerCollation, $recommendedCollations)) {
					$databaseServerCollation = reset($recommendedCollations);
				}
			} else {
				$databaseServerCharset = $defaultCharset;
				$databaseServerCollation = $defaultCollation;
			}
		} else {
			$databaseServerCharset = $defaultCharset;
			$databaseServerCollation = $defaultCollation;
		}
		
		if (!str_starts_with($databaseServerCollation, $databaseServerCharset)) {
			$databaseServerCharset = $defaultCharset;
			$databaseServerCollation = $defaultCollation;
		}
		
		return [
			'charset'   => $databaseServerCharset,
			'collation' => $databaseServerCollation,
		];
	}
	
	/**
	 * @param \PDO|null $pdo
	 * @return void
	 */
	public static function tryToFixConnectionCharsetAndCollation(\PDO $pdo = null): void
	{
		$isEncodingNeedToBeUpdated = false;
		
		// Default Charset & Collation
		$charset = config('larapen.core.database.encoding.default.charset', 'utf8mb4');
		$collation = config('larapen.core.database.encoding.default.collation', 'utf8mb4_unicode_ci');
		
		try {
			if (empty($pdo)) {
				$pdo = DB::connection()->getPdo();
			}
			
			$databaseServerEncoding = self::getServerCharsetAndCollation($pdo);
			$serverCharset = $databaseServerEncoding['charset'] ?? $charset;
			$serverCollation = $databaseServerEncoding['collation'] ?? $collation;
			
			$connectionCharset = DBTool::getDatabaseConnectionInfo('charset');
			$connectionCollation = DBTool::getDatabaseConnectionInfo('collation');
			
			$isEncodingNeedToBeUpdated = (
				$serverCharset != $connectionCharset
				|| $serverCollation != $connectionCollation
			);
			
			$recommendedEncoding = self::getRecommendedCharsetAndCollation($databaseServerEncoding, $pdo);
			$charset = $recommendedEncoding['charset'] ?? $charset;
			$collation = $recommendedEncoding['collation'] ?? $collation;
		} catch (\Throwable $e) {
		}
		
		if (!$isEncodingNeedToBeUpdated) return;
		
		// Update the database connection encoding in the /.env file
		try {
			$needToBeSaved = false;
			if (DotenvEditor::keyExists('DB_CHARSET')) {
				DotenvEditor::setKey('DB_CHARSET', $charset);
				$needToBeSaved = true;
			}
			if (DotenvEditor::keyExists('DB_COLLATION')) {
				DotenvEditor::setKey('DB_COLLATION', $collation);
				$needToBeSaved = true;
			}
			if ($needToBeSaved) {
				DotenvEditor::save();
			}
		} catch (\Throwable $e) {
		}
		
		// Update the database encoding
		try {
			// Run a query to get the current database name
			$databaseName = DBTool::getRawDatabaseName($pdo);
			if (!empty($databaseName)) {
				// SQL query to update the database's charset & collation
				$sql = "ALTER DATABASE `$databaseName` CHARACTER SET $charset COLLATE $collation";
				
				// Perform the Query
				$pdo->exec($sql);
			}
		} catch (\PDOException $e) {
		}
	}
	
	/**
	 * Check if the charset is valid
	 *
	 * @param string $charset
	 * @param \PDO|null $pdo
	 * @return bool
	 */
	public static function isValidCharset(string $charset, \PDO $pdo = null): bool
	{
		try {
			if (empty($pdo)) {
				$pdo = DB::connection()->getPdo();
			}
			
			$sql = 'SELECT CHARACTER_SET_NAME FROM information_schema.CHARACTER_SETS WHERE CHARACTER_SET_NAME = :charset';
			$query = $pdo->prepare($sql);
			$query->execute(['charset' => $charset]);
			
			return !empty($query->fetchColumn());
		} catch (\Throwable $e) {
		}
		
		return false;
	}
	
	/**
	 * Check if the collation is valid, or
	 * Check for the valid collation in a charset
	 *
	 * @param string $collation
	 * @param string|null $charset
	 * @param \PDO|null $pdo
	 * @return bool
	 */
	public static function isValidCollation(string $collation, ?string $charset = null, \PDO $pdo = null): bool
	{
		try {
			if (empty($pdo)) {
				$pdo = DB::connection()->getPdo();
			}
			
			$sql = 'SELECT COLLATION_NAME FROM information_schema.COLLATIONS WHERE COLLATION_NAME = :collation';
			$sql .= !empty($charset) ? ' AND CHARACTER_SET_NAME = :charset' : '';
			$query = $pdo->prepare($sql);
			$query->execute(['collation' => $collation, 'charset' => $charset]);
			$isValidCollation = $query->fetchColumn();
			
			return !empty($isValidCollation);
		} catch (\Throwable $e) {
		}
		
		return false;
	}
	
	/**
	 * @param \PDO|null $pdo
	 * @return bool
	 */
	public static function isValidCharsetAndCollation(\PDO $pdo = null): bool
	{
		try {
			if (empty($pdo)) {
				$pdo = DB::connection()->getPdo();
			}
			
			$defaultCharset = config('larapen.core.database.encoding.default.charset', 'utf8mb4');
			$defaultCollation = config('larapen.core.database.encoding.default.collation', 'utf8mb4_unicode_ci');
			
			$validRecommendedEncoding = self::getFirstValidRecommendedCharsetAndCollation($pdo);
			if (!empty($validRecommendedEncoding)) {
				$defaultCharset = $validRecommendedEncoding['charset'] ?? $defaultCharset;
				$defaultCollation = $validRecommendedEncoding['collation'] ?? $defaultCollation;
			}
			
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
			
			// Get the selected database name
			// $databaseName = DB::connection()->getDatabaseName();
			$databaseName = DBTool::getRawDatabaseName($pdo);
			
			// Get the database charset & collation
			$sql = "SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
					FROM INFORMATION_SCHEMA.SCHEMATA
					WHERE SCHEMA_NAME = :databaseName";
			$query = $pdo->prepare($sql);
			$query->execute(['databaseName' => $databaseName]);
			$defaultCharacterSetAndCollation = $query->fetch(\PDO::FETCH_ASSOC);
			
			$sql = "SHOW VARIABLES LIKE 'character_set%'";
			$query = $pdo->query($sql);
			$characterSetVars = $query->fetchAll(\PDO::FETCH_ASSOC);
			if (!empty($characterSetVars)) {
				$characterSetVars = collect($characterSetVars)
					->mapWithKeys(fn ($item) => [$item['Variable_name'] => $item['Value']])
					->toArray();
			}
			
			$sql = "SHOW VARIABLES LIKE 'collation%'";
			$query = $pdo->query($sql);
			$collationVars = $query->fetchAll(\PDO::FETCH_ASSOC);
			if (!empty($collationVars)) {
				$collationVars = collect($collationVars)
					->mapWithKeys(fn ($item) => [$item['Variable_name'] => $item['Value']])
					->toArray();
			}
			
			if (
				isset(
					$defaultCharacterSetAndCollation['DEFAULT_CHARACTER_SET_NAME'],
					$characterSetVars['character_set_server'],
					$characterSetVars['character_set_connection'],
					$characterSetVars['character_set_database'],
					$defaultCharacterSetAndCollation['DEFAULT_COLLATION_NAME'],
					$collationVars['collation_connection'],
					$collationVars['collation_database']
				)
			) {
				$isValidCharacterSet = (
					$defaultCharacterSetAndCollation['DEFAULT_CHARACTER_SET_NAME'] == $characterSetVars['character_set_server']
					&& $characterSetVars['character_set_server'] == $characterSetVars['character_set_connection']
					&& $characterSetVars['character_set_connection'] == $characterSetVars['character_set_database']
					&& $characterSetVars['character_set_database'] == $databaseCharset
				);
				
				if ($isValidCharacterSet) {
					$isValidCollation = (
						str_starts_with($defaultCharacterSetAndCollation['DEFAULT_COLLATION_NAME'], $databaseCharset)
						&& str_starts_with($collationVars['collation_server'], $databaseCharset)
						&& str_starts_with($collationVars['collation_connection'], $databaseCharset)
						&& str_starts_with($collationVars['collation_database'], $databaseCharset)
					);
				} else {
					$isValidCollation = (
						$defaultCharacterSetAndCollation['DEFAULT_COLLATION_NAME'] == $collationVars['collation_server']
						&& $collationVars['collation_server'] == $collationVars['collation_connection']
						&& $collationVars['collation_connection'] == $collationVars['collation_database']
						&& $collationVars['collation_database'] == $databaseCollation
					);
				}
				
				return $isValidCharacterSet && $isValidCollation;
			}
		} catch (\Throwable $e) {
			return false;
		}
		
		return false;
	}
}
