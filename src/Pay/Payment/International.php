<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay\Payment;

use h4kuna\Fio\Exceptions;

class International extends Foreign
{
	public const CHARGES_OUR = 470501;
	public const CHARGES_BEN = 470502;
	public const CHARGES_SHA = 470503;

	private const TYPES_CHARGES = [self::CHARGES_BEN, self::CHARGES_OUR, self::CHARGES_SHA];

	protected string $remittanceInfo4 = '';

	/**
	 * Default value is goods export.
	 * @see Property
	 */
	protected int $paymentReason = 110;

	protected int $detailsOfCharges = self::CHARGES_BEN;


	/**
	 * Section in manual 6.3.4.
	 * @throws Exceptions\InvalidArgument
	 */
	public function setDetailsOfCharges(int $type): static
	{
		$this->detailsOfCharges = Exceptions\InvalidArgument::checkIsInList($type, self::TYPES_CHARGES);

		return $this;
	}


	public function setRemittanceInfo4(string $info): static
	{
		$this->remittanceInfo4 = Exceptions\InvalidArgument::check($info, 35);

		return $this;
	}


	/** @return array<string, bool> */
	public function getExpectedProperty(): array
	{
		return [
			'accountFrom' => true,
			'currency' => true,
			'amount' => true,
			'accountTo' => true,
			'bic' => true,
			'date' => true,
			'comment' => false,
			'benefName' => true,
			'benefStreet' => true,
			'benefCity' => true,
			'benefCountry' => true,
			'remittanceInfo1' => true,
			'remittanceInfo2' => false,
			'remittanceInfo3' => false,
			'remittanceInfo4' => false,
			'detailsOfCharges' => true,
			'paymentReason' => true,
		];
	}


	public function getStartXmlElement(): string
	{
		return 'ForeignTransaction';
	}

}
