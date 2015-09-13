<?php

namespace h4kuna\Fio\Security;

/**
 * @author Milan Matějček
 */
class Account
{

	/** @var string */
	private $token;

	/** @var AccountBank */
	private $account;

	public function __construct($token, AccountBank $account)
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
