<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

class FioAccount
{

	/** @var Bank */
	private $account;

	/** @var string */
	private $token;


	public function __construct(string $account, string $token)
	{
		$this->account = Bank::createNational($account);
		$this->token = $token;
	}


	public function getAccount(): string
	{
		return $this->account->getAccount();
	}


	public function getBankCode(): string
	{
		return $this->account->getBankCode();
	}


	public function getToken(): string
	{
		return $this->token;
	}


	public function __toString()
	{
		return $this->getAccount();
	}

}
