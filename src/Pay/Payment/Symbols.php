<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay\Payment;

use h4kuna\Fio\Exceptions\InvalidArgument;

trait Symbols
{
	protected string $ks = '';

	protected string $vs = '';

	protected string $ss = '';


	/**
	 * @param string $ks - int is deprecated
	 */
	public function setConstantSymbol(int|string $ks): static
	{
		InvalidArgument::checkRange($ks, 9999);
		$this->ks = (string) $ks;

		return $this;
	}


	/**
	 * @param string $vs - int is deprecated
	 */
	public function setVariableSymbol(int|string $vs): static
	{
		InvalidArgument::checkRange($vs, 9999999999);
		$this->vs = (string) $vs;

		return $this;
	}


	/**
	 * @param string $ss - int is deprecated
	 */
	public function setSpecificSymbol(int|string $ss): static
	{
		InvalidArgument::checkRange($ss, 9999999999);
		$this->ss = (string) $ss;

		return $this;
	}

}
