<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio,
	h4kuna\Fio\Utils;

/**
 * @author Milan Matějček
 */
class International extends Euro
{

	const
		CHARGES_OUR = 470501,
		CHARGES_BEN = 470502,
		CHARGES_SHA = 470503;

	/** @var string */
	protected $bic = true;

	/** @var string */
	protected $benefStreet = true;

	/** @var string */
	protected $benefCity = true;

	/** @var string */
	protected $benefCountry = true;

	/** @var string */
	protected $remittanceInfo1 = true;

	/** @var string */
	protected $remittanceInfo4;

	/**
	 * Default value is goods export.
	 * @see Property
	 */
	protected $paymentReason = 110;

	/** @var int */
	protected $detailsOfCharges = self::CHARGES_BEN;

	/**
	 * @param int $type
	 * @return self
	 * @throws Fio\InvalidArgumentException
	 */
	public function setDetailsOfCharges($type)
	{
		static $types = [self::CHARGES_BEN, self::CHARGES_OUR, self::CHARGES_SHA];
		if (!in_array($type, $types)) {
			throw new Fio\InvalidArgumentException('Select one type from constatns. Section in manual 6.3.4.');
		}
		$this->detailsOfCharges = $type;
		return $this;
	}

	/**
	 * @param string $str
	 * @return self
	 */
	public function setRemittanceInfo4($str)
	{
		$this->remittanceInfo4 = Utils\Strings::substr($str, 35);
		return $this;
	}

	protected function getExpectedProperty()
	{
		return ['accountFrom', 'currency', 'amount', 'accountTo', 'bic', 'date',
			'comment', 'benefName', 'benefStreet', 'benefCity', 'benefCountry',
			'remittanceInfo1', 'remittanceInfo2', 'remittanceInfo3', 'remittanceInfo4',
			'detailsOfCharges', 'paymentReason'];
	}

	public function getStartXmlElement()
	{
		return 'ForeignTransaction';
	}

	/** @internal */
	public function setConstantSymbol($ks)
	{
		throw new Fio\InvalidArgumentException('Not available.');
	}

	/** @internal */
	public function setSpecificSymbol($ss)
	{
		throw new Fio\InvalidArgumentException('Not available.');
	}

	/** @internal */
	public function setVariableSymbol($vs)
	{
		throw new Fio\InvalidArgumentException('Not available.');
	}

	/** @internal */
	public function setPaymentType($type)
	{
		throw new Fio\InvalidArgumentException('Not available.');
	}

}
