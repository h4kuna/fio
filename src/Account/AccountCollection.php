<?php

namespace h4kuna\Fio\Account;

use h4kuna\Fio\AccountException;

/**
 * @author Milan Matějček
 */
class AccountCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{

	/** @var Account[] */
	private $accounts;

	/**
	 * @return Account
	 * @throws AccountException
	 */
	public function get($alias)
	{
		if (isset($this->accounts[$alias])) {
			return $this->accounts[$alias];
		}
		throw new AccountException('This account alias does not exists: ' . $alias);
	}

	/**
	 * @return Account
	 * @throws AccountException
	 */
	public function getDefault()
	{
		return reset($this->accounts);
	}

	/**
	 * @param string $alias
	 * @param Account $account
	 * @return AccountCollection
	 */
	public function addAccount($alias, Account $account)
	{
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

	/**
	 * Replaces or appends a item.
	 * @return mixed
	 */
	public function offsetSet($alias, $account)
	{
		$this->addAccount($alias, $account);
	}

	/**
	 * Returns a item.
	 * @return mixed
	 */
	public function offsetGet($alias)
	{
		return $this->accounts[$alias];
	}

	/**
	 * Determines whether a item exists.
	 * @return bool
	 */
	public function offsetExists($alias)
	{
		return isset($this->accounts[$alias]);
	}

	/**
	 * Removes the element from this list.
	 * @return void
	 */
	public function offsetUnset($alias)
	{
		unset($this->accounts[$alias]);
	}

}
