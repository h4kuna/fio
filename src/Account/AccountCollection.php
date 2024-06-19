<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use h4kuna\Fio\Exceptions;

/**
 * @implements \IteratorAggregate<string, FioAccount>
 */
class AccountCollection implements \Countable, \IteratorAggregate
{
	/** @var array<string, FioAccount> */
	private array $accounts = [];


	public function account(string $alias = ''): FioAccount
	{
		if ($alias === '') {
			return $this->getDefault();
		}

		return $this->get($alias);
	}


	private function get(string $alias): FioAccount
	{
		if (isset($this->accounts[$alias])) {
			return $this->accounts[$alias];
		}
		throw new Exceptions\InvalidArgument('This account alias does not exists: ' . $alias);
	}


	private function getDefault(): FioAccount
	{
		if ($this->accounts === []) {
			throw new Exceptions\InvalidState('Missing account, let\'s fill in configuration.');
		}

		return reset($this->accounts);
	}


	public function addAccount(string $alias, FioAccount $account): AccountCollection
	{
		if (isset($this->accounts[$alias])) {
			throw new Exceptions\InvalidArgument('This alias already exists: ' . $alias);
		}

		$this->accounts[$alias] = $account;

		return $this;
	}


	public function count(): int
	{
		return count($this->accounts);
	}


	/**
	 * @return \ArrayIterator<string, FioAccount>
	 */
	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->accounts);
	}

}
