<?php

namespace h4kuna\Fio\Account;

use h4kuna\Fio\AccountException;

/**
 * @author Tomáš Jacík
 * @author Milan Matějček
 */
class AccountCollection implements \Countable, \IteratorAggregate
{

	/** @var FioAccount[] */
	private $accounts = [];

	/**
	 * @param string
	 * @return FioAccount
	 * @throws AccountException
	 */
	public function get($alias)
	{
		if (isset($this->accounts[$alias])) {
			return $this->accounts[$alias];
		}
		throw new AccountException('This account alias does not exists: ' . $alias);
	}

	/** @return FioAccount|FALSE */
	public function getDefault()
	{
		return reset($this->accounts);
	}

    /**
     * @param string $alias
     * @param FioAccount $account
     * @return self
     * @throws \h4kuna\Fio\AccountException
     */
	public function addAccount($alias, FioAccount $account)
	{
		if (isset($this->accounts[$alias])) {
			throw new AccountException('This alias already exists: ' . $alias);
		}

		$this->accounts[$alias] = $account;
		return $this;
	}

	/**
	 * Returns items count.
	 * @return int
	 */
	public function count()
	{
		return count($this->accounts);
	}

	/**
	 * Returns an iterator over all items.
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->accounts);
	}

}
