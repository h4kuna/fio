<?php

namespace h4kuna\Fio;

use Nette\Object;

/**
 * @author Milan Matějček
 */
class Fio extends Object
{

	/** @var string url Fio REST API */
	const REST_URL = 'https://www.fio.cz/ib_api/rest/';

	/**
	 * @todo INKASO does not work
	 * @var string
	 */
	const FIO_API_VERSION = '1.4.4';

	/** @var Request\IQueue */
	protected $queue;

	/** @var Account\AccountCollection */
	protected $accountCollection;

	public function __construct(Request\IQueue $queue, Account\AccountCollection $accountCollection)
	{
		$this->queue = $queue;
		$this->accountCollection = $accountCollection;
	}

	/**
	 * @param atring $alias
	 * @return self
	 */
	public function setActive($alias)
	{
		$this->accountCollection->setActive($alias);
		return $this;
	}

	/** @return Account\Account */
	public function getActive()
	{
		return $this->accountCollection->getActive();
	}

}
