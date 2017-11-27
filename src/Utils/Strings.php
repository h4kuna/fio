<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Account,
	Nette\Utils\DateTime;

/**
 * @author Milan Matějček
 */
final class Strings
{

	private function __construct() {}

	/**
	 * @param string $str
	 * @param int $limit
	 * @return string|NULL
	 */
	public static function substr($str, $limit)
	{
		return $str ? mb_substr($str, 0, $limit) : null; // max length from API
	}

	/**
	 * @param string $account
	 * @param string|NULL $bankCode
	 * @return Account\Bank
	 */
	public static function createAccount($account, $bankCode = null)
	{
		if ($bankCode) {
			$account .= '/';
		}
		return new Account\Bank($account . $bankCode);
	}

	/**
	 * @param mixed $date
	 * @param string $format
	 * @return string
	 */
	public static function date($date, $format = 'Y-m-d')
	{
		return DateTime::from($date)->format($format);
	}

	/**
	 * Convert string to DateTime.
	 * @param string $value
	 * @param bool $midnight
	 * @return DateTime
	 */
	public static function createFromFormat($value, $format, $midnight = true)
	{
		$dt = date_create_from_format($format, $value);
		if ($midnight) {
			$dt->setTime(0, 0, 0);
		}
		return $dt;
	}

}
