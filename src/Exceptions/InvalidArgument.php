<?php declare(strict_types=1);

namespace h4kuna\Fio\Exceptions;

final class InvalidArgument extends \InvalidArgumentException
{

	public static function check(string $text, int $size): string
	{
		if (mb_strlen($text) > $size) {
			throw new self(sprintf('Value "%s" is longer then allowed limit (%s).', $text, $size));
		}

		return $text;
	}


	public static function checkRange(int|string $number, int $limit): void
	{
		$check = intval($number);
		if ($check < 0 || $check > $limit) {
			throw new self(sprintf('Value is out of range "%s" must contain 1-%s positive digits.', $check, $limit));
		}
	}


	/**
	 * @template T of string|int
	 * @param T $value
	 * @param array<T> $list
	 * @return T
	 */
	public static function checkIsInList(string|int $value, array $list): string|int
	{
		if (!in_array($value, $list, true)) {
			throw new InvalidArgument(sprintf('Value "%s" is not contained in list: [%s].', $value, implode(', ', $list)));
		}

		return $value;
	}


	public static function checkLength(string $text, int $limit): string
	{
		if (strlen($text) !== $limit) {
			throw new self(sprintf('Value has not exact length "%s" this is "%s" with length "%s".', $limit, $text, strlen($text)));
		}

		return $text;
	}

}
