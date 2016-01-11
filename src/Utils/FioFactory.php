<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio;

class FioFactory
{

	public function createFioRead(array $accounts)
	{
		$transactionList = $this->createTransactionListFactory();
		return new Fio\FioRead($this->createContext($accounts), $transactionList->createReader());
	}

	public function createTransactionListFactory()
	{
		// @todo test next class
		return new Fio\Response\Read\JsonTransactionFactory;
	}

	public function createQueue()
	{
		return new Fio\Request\Queue();
	}

	public function createContext(array $accounts)
	{
		return new Context($this->createQueue(), $this->createAccounts($accounts));
	}

	public function createAccounts(array $accounts)
	{
		return Fio\Account\AccountsFactory::create($accounts);
	}

	public function createDateFormatOriginal()
	{
		return new Date\DateFormatOriginal();
	}

}
