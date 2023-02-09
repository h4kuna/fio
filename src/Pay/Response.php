<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay;

interface Response
{

	function isOk(): bool;


	function status(): string;


	function code(): int;


	/**
	 * @return array<int, string>
	 */
	function errorMessages(): array;


	function __toString();

}
