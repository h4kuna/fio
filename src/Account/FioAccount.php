<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use Stringable;

class FioAccount implements Stringable
{
	private Bank $account;


	public function __construct(string $account, private string $token)
	{
		$this->account = Bank::createNational($account);
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


	public function __toString(): string
	{
		return $this->getAccount();
	}

}
