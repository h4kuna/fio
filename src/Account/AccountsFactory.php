<?php

namespace h4kuna\Fio\Account;

use h4kuna\Fio\AccountException;

/**
 * @author Milan Matějček
 */
class AccountsFactory
{

	/**
	 * @param array $accounts
	 * @return Accounts
	 * @throws AccountException
	 */
	public static function create(array $accounts)
	{
		$accountsObject = new Accounts;
		foreach ($accounts as $alias => $info) {
			if (!isset($info['token'])) {
				throw new AccountException("Key 'token' is required for $alias.");
			} elseif (!isset($info['account'])) {
				throw new AccountException("Key 'account' is required for $alias.");
			}
			$accountsObject->addAccount($alias, new Fio($info['token'], new Bank($info['account'])));
		}
		return $accountsObject;
	}

}
