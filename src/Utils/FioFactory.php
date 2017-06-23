<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio;

class FioFactory
{

	/** @var Fio\Account\AccountCollection */
	private $accountCollection;

	/** @var Fio\Request\IQueue */
	private $queue;

	/** @var string */
	private $transactionClass;

	/** @var string */
	protected $temp;

	public function __construct(array $accounts, $transactionClass = NULL, $temp = NULL)
	{
		$this->setTemp($temp);
		$this->accountCollection = $this->createAccountCollection($accounts);
		$this->queue = $this->createQueue();
		$this->transactionClass = $transactionClass;
	}

	private function setTemp($temp)
	{
		$this->temp = $temp ?: sys_get_temp_dir();
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
			$this->getQueue(), $this->getAccount($name), $this->createXmlFile()
		);
	}

	/**
	 * COMMON ******************************************************************
	 * *************************************************************************
	 */
	protected function createQueue()
	{
		return new Fio\Request\Queue($this->temp);
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
	 * @return Fio\Account\FioAccount
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
		return new Fio\Request\Pay\XMLFile($this->temp);
	}

}
