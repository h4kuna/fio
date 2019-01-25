<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use h4kuna\Fio;
use h4kuna\Fio\Account;
use h4kuna\Fio\Response\Read\Transaction;

class FioFactory
{

	/** @var Account\AccountCollection */
	private $accountCollection;

	/** @var Fio\Request\IQueue */
	private $queue;

	/** @var string */
	private $transactionClass;

	/** @var string */
	protected $temp;


	public function __construct(array $accounts, string $transactionClass = Transaction::class, string $temp = '')
	{
		$this->setTemp($temp);
		$this->accountCollection = $this->createAccountCollection($accounts);
		$this->queue = $this->createQueue();
		$this->transactionClass = $transactionClass;
	}


	private function setTemp(string $temp): void
	{
		if ($temp === '') {
			$temp = sys_get_temp_dir();
		}

		$this->temp = $temp;
	}


	public function createFioRead(string $name = ''): Fio\FioRead
	{
		return new Fio\FioRead($this->queue(), $this->accountCollection()->account($name), $this->createReader());
	}


	/**
	 * @param string $name Configured account name from AccountCollection
	 * @return Fio\FioPay
	 */
	public function createFioPay(string $name = ''): Fio\FioPay
	{
		return new Fio\FioPay(
			$this->queue(), $this->accountCollection()->account($name), $this->createXmlFile()
		);
	}


	/**
	 * COMMON ******************************************************************
	 * *************************************************************************
	 */
	protected function createQueue(): Fio\Request\IQueue
	{
		return new Fio\Request\Queue($this->temp);
	}


	protected function createAccountCollection(array $accounts): Account\AccountCollection
	{
		return Account\AccountCollectionFactory::create($accounts);
	}


	final protected function accountCollection(): Account\AccountCollection
	{
		return $this->accountCollection;
	}


	final protected function queue(): Fio\Request\IQueue
	{
		return $this->queue;
	}


	final protected function transactionClass(): string
	{
		return $this->transactionClass;
	}


	/**
	 * READ ********************************************************************
	 * *************************************************************************
	 */
	protected function createTransactionListFactory(): Fio\Response\Read\JsonTransactionFactory
	{
		return new Fio\Response\Read\JsonTransactionFactory($this->transactionClass());
	}


	protected function createReader(): Fio\Request\Read\Files\Json
	{
		return new Fio\Request\Read\Files\Json($this->createTransactionListFactory());
	}


	/**
	 * PAY *********************************************************************
	 * *************************************************************************
	 */
	protected function createXmlFile(): Fio\Request\Pay\XMLFile
	{
		return new Fio\Request\Pay\XMLFile($this->temp);
	}

}
