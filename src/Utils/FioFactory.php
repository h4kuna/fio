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

	protected function createTransactionListFactory()
	{
		// @todo test next class
		return new Fio\Response\Read\JsonTransactionFactory;
	}

	protected function createQueue()
	{
		return new Fio\Request\Queue();
	}

	protected function createContext(array $accounts)
	{
		return new Context($this->createQueue(), $this->createAccounts($accounts));
	}

	protected function createAccounts(array $accounts)
	{
		return Fio\Account\AccountsFactory::create($accounts);
	}

}
