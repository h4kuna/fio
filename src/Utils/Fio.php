<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Exceptions\InvalidState;
use h4kuna\Fio\Exceptions\ServiceUnavailable;
use Nette\Utils\DateTime;
use Psr\Http\Message\ResponseInterface;

if (Fio::is32bitOS()) {
	throw new InvalidState('This library does not support 32bit OS.');
}

/**
 * @see doc https://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
 * @support 1.8
 */
final class Fio
{
	/** @var string url Fio REST API */
	public const REST_URL = 'https://fioapi.fio.cz/v1/rest/';

	private function __construct()
	{
	}


	public static function date(int|string|\DateTimeInterface $date, string $format = 'Y-m-d'): string
	{
		return DateTime::from($date)->format($format);
	}

	/**
	 * @throws ServiceUnavailable
	 */
	public static function getContents(ResponseInterface $response): string
	{
		$body = $response->getBody();

		try {
			return $body->getContents();
		} catch (\RuntimeException $e) {
			throw new ServiceUnavailable($e->getMessage(), $e->getCode());
		}
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
