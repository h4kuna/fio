<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Fixtures;

use h4kuna\Fio\Read\Column;
use stdClass;
use function str_pad;
use const STR_PAD_LEFT;

final class MyTransaction
{

	#[Column(id: 1)]
	public float $amount;

	#[Column(id: 2)]
	public string $toAccount;

	#[Column(id: 3)]
	public string $bankCode;

	public stdClass $original;


	/** custom method */
	public function setBankCode(?string $value): void
	{
		$this->bankCode = str_pad((string) $value, 4, '0', STR_PAD_LEFT);
	}

	public function setToAccount(?string $value): void
	{
		$this->toAccount = $value ?? '';
	}

}
