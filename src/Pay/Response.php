<?php declare(strict_types = 1);

namespace h4kuna\Fio\Pay;

interface Response
{

	public function isOk(): bool;

	public function status(): string;

	public function code(): int;

	/**
	 * @return array<int, string>
	 */
	public function errorMessages(): array;

	public function __toString(): string;

}
