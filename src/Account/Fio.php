<?php

namespace h4kuna\Fio\Account;

/**
 * @author Milan Matějček
 */
class Fio
{

	/** @var string */
	private $token;

	/** @var Bank */
	private $account;

	public function __construct($token, Bank $account)
	{
		$this->token = $token;
		$this->account = $account;
	}

	/** @return string */
	public function getToken()
	{
		return $this->token;
	}

	/** @return string */
	public function getAccount()
	{
		return $this->account->getAccount();
	}

	/** @return string */
	public function getBankCode()
	{
		return $this->account->getBankCode();
	}

}
