<?php

namespace h4kuna\Fio\Request\Pay;

use h4kuna\Fio\Account;

/**
 * @author Milan Matějček
 */
class PaymentFactory
{

	/** @var Account\AccountCollection */
	private $accountCollection;

	public function __construct(Account\AccountCollection $accountCollection)
	{
		$this->accountCollection = $accountCollection;
	}

	/** @return Payment\National */
	public function createNational($amount, $accountTo, $bankCode = NULL)
	{
		$account = $this->accountCollection->getActive()->getAccount();
		return (new Payment\National($account))
			->setAccountTo($accountTo, $bankCode)
			->setAmount($amount);
	}

	/** @return Payment\International */
	public function createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info)
	{
		$account = $this->accountCollection->getActive()->getAccount();
		return (new Payment\International($account))
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
		$account = $this->accountCollection->getActive()->getAccount();
		return (new Payment\Euro($account))
			->setBic($bic)
			->setName($name)
			->setCountry($country)
			->setAccountTo($accountTo)
			->setAmount($amount);
	}

}
