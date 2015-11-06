<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Request\IQueue,
	h4kuna\Fio\Security,
	Nette\Object;

class Context extends Object
{

	/** @var IQueue */
	private $queue;

	/** @var Security\Accounts */
	private $accounts;

	function __construct(IQueue $queue, Security\Accounts $accounts)
	{
		$this->queue = $queue;
		$this->accounts = $accounts;
	}

	public function getAccounts()
	{
		return $this->accounts;
	}

	public function getActiveAccount()
	{
		return $this->accounts->getActive();
	}

	public function getToken()
	{
		return $this->accounts->getActive()->getToken();
	}

	/** @return IQueue */
	public function getQueue()
	{
		return $this->queue;
	}

	/** @return string */
	public function getUrl()
	{
		return self::REST_URL;
	}

}
