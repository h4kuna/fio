<?php

namespace h4kuna\Fio\Security;

use h4kuna\Fio\Utils;

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

	/** @return Account */
	public function getActive()
	{
		return $this->accountExists($this->active);
	}

	/**
	 * @return AccountFio
	 * @throws Utils\FioException
	 */
	private function accountExists($alias)
	{
		if (isset($this->accounts[$alias])) {
			return $this->accounts[$alias];
		}
		throw new Utils\FioException('This account alias does not exists. ' . $alias);
	}

	public function addAccount($alias, AccountFio $account)
	{
		$this->accounts[$alias] = $account;
		if ($this->active === NULL) {
			$this->setActive($alias);
		}
	}

}
