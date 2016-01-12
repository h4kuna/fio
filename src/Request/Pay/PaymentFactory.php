<?php

namespace h4kuna\Fio\Request\Pay;

use h4kuna\Fio\Account;

/**
 * @author Milan Matějček
 */
class PaymentFactory
{

	/** @var Account\Accounts */
	private $accounts;

	public function __construct(Account\Accounts $account)
	{
		$this->accounts = $account;
	}

	/** @return Payment\National */
	public function createNational($amount, $accountTo, $bankCode = NULL)
	{
		static $payment = [];
		$account = $this->accounts->getActive()->getAccount();
		if (!isset($payment[$account])) {
			$payment[$account] = new Payment\National($account);
		}
		$clone = clone $payment[$account];
		$clone->setAccountTo($accountTo, $bankCode)->setAmount($amount);
		return $clone;
	}

	/** @return Payment\International */
	public function createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info)
	{
		static $payment = [];
		$account = $this->accounts->getActive()->getAccount();
		if (!isset($payment[$account])) {
			$payment[$account] = new Payment\International($account);
		}
		$clone = clone $payment[$account];
		$clone->setBic($bic)->setName($name)->setCountry($country)
			->setAccountTo($accountTo)->setStreet($street)
			->setCity($city)->setRemittanceInfo1($info)->setAmount($amount);
		return $clone;
	}

	/** @return Payment\Euro */
	public function createEuro($amount, $accountTo, $bic, $name, $country)
	{
		static $payment = [];
		$account = $this->accounts->getActive()->getAccount();
		if (!isset($payment[$account])) {
			$payment[$account] = new Payment\Euro($account);
		}
		$clone = clone $payment[$account];
		$clone->setBic($bic)->setName($name)->setCountry($country)
			->setAccountTo($accountTo)->setAmount($amount);
		return $clone;
	}

}
