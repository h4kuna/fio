<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Exceptions\InvalidState;
use Nette\Utils\DateTime;

if (Fio::is32bitOS()) {
	throw new InvalidState('This library does not support 32bit OS.');
}

/**
 * @see doc https://www.fio.sk/docs/cz/API_Bankovnictvi.pdf
 * @support 1.7.4
 */
final class Fio
{
	/** @var string url Fio REST API */
	public const REST_URL = 'https://www.fio.cz/ib_api/rest/';

	private function __construct()
	{
	}


	public static function date(int|string|\DateTimeInterface $date, string $format = 'Y-m-d'): string
	{
		return DateTime::from($date)->format($format);
	}


	public static function toDate(string $value): \DateTimeImmutable
	{
		$date = \DateTimeImmutable::createFromFormat('!Y-m-dO', $value);
		assert($date instanceof \DateTimeImmutable);

		return $date;
	}


	public static function is32bitOS(): bool
	{
		return PHP_INT_SIZE === 4;
	}

}
