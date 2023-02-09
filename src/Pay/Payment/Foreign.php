<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay\Payment;

use h4kuna\Fio\Account;
use h4kuna\Fio\Exceptions\InvalidArgument;

abstract class Foreign extends Property
{
	public const PAYMENT_STANDARD = 431008;
	public const PAYMENT_PRIORITY = 431009;

	private const TYPES_PAYMENT = [self::PAYMENT_STANDARD, self::PAYMENT_PRIORITY];

	protected string $bic = '';

	protected string $benefName = '';

	protected string $benefStreet = '';

	protected string $benefCity = '';

	protected string $benefCountry = '';

	protected string $remittanceInfo1 = '';

	protected string $remittanceInfo2 = '';

	protected string $remittanceInfo3 = '';

	protected int $paymentType = 0;


	public function __construct(Account\FioAccount $account)
	{
		parent::__construct($account);
		$this->setCurrency('EUR');
	}


	/**
	 * @param string $accountTo ISO 13616
	 */
	public function setAccountTo(string $accountTo): static
	{
		$this->accountTo = InvalidArgument::check($accountTo, 34);

		return $this;
	}


	/**
	 * @param string $bic ISO 9362
	 */
	public function setBic(string $bic): static
	{
		$this->bic = InvalidArgument::checkLength($bic, 11);

		return $this;
	}


	public function setStreet(string $street): static
	{
		$this->benefStreet = InvalidArgument::check($street, 35);

		return $this;
	}


	public function setCity(string $city): static
	{
		$this->benefCity = InvalidArgument::check($city, 35);

		return $this;
	}


	public function setCountry(string $benefCountry): static
	{
		$country = strtoupper($benefCountry);
		if (strlen($country) !== 2 && $country !== 'TCH') {
			throw new InvalidArgument('Look at to manual for country code section 6.3.3.');
		}
		$this->benefCountry = $country;

		return $this;
	}


	public function setName(string $name): static
	{
		$this->benefName = InvalidArgument::check($name, 35);

		return $this;
	}


	public function setRemittanceInfo1(string $info): static
	{
		$this->remittanceInfo1 = InvalidArgument::check($info, 35);

		return $this;
	}


	public function setRemittanceInfo2(string $info): static
	{
		$this->remittanceInfo2 = InvalidArgument::check($info, 35);

		return $this;
	}


	public function setRemittanceInfo3(string $str): static
	{
		$this->remittanceInfo3 = InvalidArgument::check($str, 35);

		return $this;
	}


	public function setPaymentType(int $type): static
	{
		$this->paymentType = InvalidArgument::checkIsInList($type, self::TYPES_PAYMENT);

		return $this;
	}

}
