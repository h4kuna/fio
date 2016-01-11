<?php

namespace h4kuna\Fio\Account;

use h4kuna\Fio\AccountException;

/**
 * @author Milan Matějček
 */
class Accounts
{

	/** @var Account[] */
	private $accounts;

	/** @var string */
	private $active;

	public function setActive($name)
	{
		$this->accountExists($name);
		$this->active = $name;
		return $this;
	}

	/** @return Account\Fio */
	public function getActive()
	{
		return $this->accountExists($this->active);
	}

	/**
	 * @return Fio
	 * @throws AccountException
	 */
	private function accountExists($alias)
	{
		if (isset($this->accounts[$alias])) {
			return $this->accounts[$alias];
		}
		throw new AccountException('This account alias does not exists. ' . $alias);
	}

	public function addAccount($alias, Fio $account)
	{
		$this->accounts[$alias] = $account;
		if ($this->active === NULL) {
			$this->setActive($alias);
		}
	}

}
