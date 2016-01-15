<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio;

class FioFactory
{

	/** @var Fio\Account\AccountCollection */
	private $accountCollection;

	/** @var Context */
	private $queue;

	/** @var string */
	private $transactionClass;

	public function __construct(array $accounts, $transactionClass = NULL)
	{
		$this->accountCollection = $this->createAccountCollection($accounts);
		$this->queue = $this->createQueue();
		$this->transactionClass = $transactionClass;
	}

	/**
	 * @param string $name Configured account name from AccountCollection
	 * @return Fio\FioRead
	 */
	public function createFioRead($name = NULL)
	{
		return new Fio\FioRead($this->getQueue(), $this->getAccount($name), $this->createReader());
	}

	/**
	 * @param string $name Configured account name from AccountCollection
	 * @return Fio\FioPay
	 */
	public function createFioPay($name = NULL)
	{
		return new Fio\FioPay(
			$this->getQueue(), $this->getAccount($name),
			$this->createPaymentFactory($name), $this->createXmlFile()
		);
	}

	/**
	 * COMMON ******************************************************************
	 * *************************************************************************
	 */
	protected function createQueue()
	{
		return new Fio\Request\Queue();
	}

	protected function createAccountCollection(array $accounts)
	{
		return Fio\Account\AccountCollectionFactory::create($accounts);
	}

	final protected function getAccountCollection()
	{
		return $this->accountCollection;
	}

	/**
	 * @param string $name Configured account name from AccountCollection
	 * @return Fio\Account\Account
	 */
	final protected function getAccount($name)
	{
		if ($name) {
			return $this->getAccountCollection()->get($name);
		}

		return $this->getAccountCollection()->getDefault();
	}

	final protected function getQueue()
	{
		return $this->queue;
	}

	final protected function getTransactionClass()
	{
		return $this->transactionClass;
	}

	/**
	 * READ ********************************************************************
	 * *************************************************************************
	 */
	protected function createTransactionListFactory()
	{
		return new Fio\Response\Read\JsonTransactionFactory($this->getTransactionClass());
	}

	protected function createReader()
	{
		return new Fio\Request\Read\Files\Json($this->createTransactionListFactory());
	}

	/**
	 * PAY *********************************************************************
	 * *************************************************************************
	 */
	protected function createXmlFile()
	{
		return new Fio\Request\Pay\XMLFile(sys_get_temp_dir());
	}

	/**
	 * @param string $name Configured account name from AccountCollection
	 * @return Fio\Request\Pay\PaymentFactory
	 */
	protected function createPaymentFactory($name = NULL)
	{
		return new Fio\Request\Pay\PaymentFactory($this->getAccount($name));
	}

}
