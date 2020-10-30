<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Exceptions\InvalidArgument;
use Nette\Utils\DateTime;

final class Strings
{

	private function __construct()
	{
	}


	/**
	 * @param int|string|\DateTimeInterface $date
	 */
	public static function date($date, string $format = 'Y-m-d'): string
	{
		return DateTime::from($date)->format($format);
	}


	/**
	 * Convert string to DateTime.
	 */
	public static function createFromFormat(string $value, string $format, bool $midnight = true): \DateTimeInterface
	{
		$dt = date_create_from_format($format, $value);
		if ($dt === false) {
			throw new InvalidArgument('Create DateTime from string faild. Probably you have bad format.');
		}
		if ($midnight) {
			$dt->setTime(0, 0, 0);
		}
		return $dt;
	}


	public static function is32bitOS(): bool
	{
		return PHP_INT_SIZE === 4;
	}

}
