<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Exceptions\InvalidArgument;

trait Symbols
{
	protected int $ks = 0;

	protected int $vs = 0;

	protected int $ss = 0;


	public function setConstantSymbol(int $ks): static
	{
		$this->ks = InvalidArgument::checkRange($ks, 9999);
		return $this;
	}


	public function setVariableSymbol(int $vs): static
	{
		$this->vs = InvalidArgument::checkRange($vs, 9999999999);
		return $this;
	}


	public function setSpecificSymbol(int $ss): static
	{
		$this->ss = InvalidArgument::checkRange($ss, 9999999999);
		return $this;
	}

}
