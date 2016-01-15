<?php

namespace h4kuna\Fio\Request\Pay;

use h4kuna\Fio\Account;

/**
 * @author Milan Matějček
 */
class PaymentFactory
{

	/** @var Account\FioAccount */
	private $account;

	public function __construct(Account\FioAccount $account)
	{
		$this->account = $account;
	}

	/** @return Payment\National */
	public function createNational($amount, $accountTo, $bankCode = NULL)
	{
		return (new Payment\National($this->account))
				->setAccountTo($accountTo, $bankCode)
				->setAmount($amount);
	}

	/** @return Payment\International */
	public function createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info)
	{
		return (new Payment\International($this->account))
				->setBic($bic)
				->setName($name)
				->setCountry($country)
				->setAccountTo($accountTo)
				->setStreet($street)
				->setCity($city)
				->setRemittanceInfo1($info)
				->setAmount($amount);
	}

	/** @return Payment\Euro */
	public function createEuro($amount, $accountTo, $bic, $name, $country)
	{
		return (new Payment\Euro($this->account))
				->setBic($bic)
				->setName($name)
				->setCountry($country)
				->setAccountTo($accountTo)
				->setAmount($amount);
	}

}
