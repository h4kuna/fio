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

	/** @var Account\FioAccount */
	protected $account;

	public function __construct(Request\IQueue $queue, Account\FioAccount $account)
	{
		$this->queue = $queue;
		$this->account = $account;
	}

	/** @return Account\FioAccount */
	public function getAccount()
	{
		return $this->account;
	}

}
