<?php declare(strict_types=1);

namespace h4kuna\Fio\Exceptions;

final class InvalidArgument extends \InvalidArgumentException
{

	public static function check(string $text, int $size): string
	{
		if (mb_strlen($text) > $size) {
			throw new static(sprintf('Value "%s" is longer then allowed limit (%s).', $text, $size));
		}
		return $text;
	}


	public static function checkRange(int $number, int $limit): int
	{
		if ($number < 0 || $number > $limit) {
			throw new static(sprintf('Value is out of range "%s" must contain 1-%s positive digits.', $number, strlen((string) $limit)));
		}
		return $number;
	}


	public static function checkIsInList($value, array $list)
	{
		if (!in_array($value, $list, true)) {
			throw new InvalidArgument(sprintf('Value "%s" is not contained in list: [%s].', $value, implode(', ', $list)));
		}
		return $value;
	}


	public static function checkLength(string $text, int $limit): string
	{
		if (strlen($text) !== $limit) {
			throw new static(sprintf('Value has not exact length "%s" this is "%s" with length "%s".', $limit, $text, strlen((string) $text)));
		}
		return $text;
	}

}
