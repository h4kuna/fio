<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Pay;

interface IResponse
{

	function isOk(): bool;


	function status(): string;


	function code(): int;


	function errorMessages(): array;


	function __toString();

}
