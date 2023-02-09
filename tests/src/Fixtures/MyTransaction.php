<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use h4kuna\Fio\Read\Column;

final class MyTransaction
{

	#[Column(id: 1)]
	public float $amount;

	#[Column(id: 2)]
	public string $to_account;

	#[Column(id: 3)]
	public string $bank_code;

	public \stdClass $original;


	/** custom method */
	public function setBank_code(?string $value): void
	{
		$this->bank_code = str_pad((string) $value, 4, '0', STR_PAD_LEFT);
	}


	public function setTo_account(?string $value): void
	{
		$this->to_account = $value ?? '';
	}

}
