<?php

namespace h4kuna\Fio\Request\Pay;

/**
 * @author Milan Matějček
 */
class PaymentFactory
{

	/** @var string */
	private $account;

	public function __construct($account)
	{
		$this->account = $account;
	}

	/** @return Payment\National */
	public function createNational($amount, $accountTo, $bankCode = NULL)
	{
		static $payment = [];
		if (!isset($payment[$this->account])) {
			$payment[$this->account] = new Payment\National($this->account);
		}
		$clone = clone $payment[$this->account];
		$clone->setAccountTo($accountTo, $bankCode)->setAmount($amount);
		return $clone;
	}

	/** @return Payment\International */
	public function createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info)
	{
		static $payment = [];
		if (!isset($payment[$this->account])) {
			$payment[$this->account] = new Payment\International($this->account);
		}
		$clone = clone $payment[$this->account];
		$clone->setBic($bic)->setName($name)->setCountry($country)
			->setAccountTo($accountTo)->setStreet($street)
			->setCity($city)->setRemittanceInfo1($info)->setAmount($amount);
		return $clone;
	}

	/** @return Payment\Euro */
	public function createEuro($amount, $accountTo, $bic, $name, $country)
	{
		static $payment = [];
		if (!isset($payment[$this->account])) {
			$payment[$this->account] = new Payment\Euro($this->account);
		}
		$clone = clone $payment[$this->account];
		$clone->setBic($bic)->setName($name)->setCountry($country)
			->setAccountTo($accountTo)->setAmount($amount);
		return $clone;
	}

}
