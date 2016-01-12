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

	/** @var Account\Accounts */
	protected $accounts;

	public function __construct(Request\IQueue $queue, Account\Accounts $accounts)
	{
		$this->queue = $queue;
		$this->accounts = $accounts;
	}

	/**
	 * @param atring $alias
	 * @return self
	 */
	public function setActive($alias)
	{
		$this->accounts->setActive($alias);
		return $this;
	}

	/** @return Account\Fio */
	public function getActive()
	{
		return $this->accounts->getActive();
	}

}
