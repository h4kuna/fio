<?php

namespace h4kuna\Fio\Account;

use h4kuna\Fio\AccountException;

/**
 * @author Milan Matějček
 */
class AccountCollectionFactory
{

	/**
	 * @param array $accounts
	 * @return AccountCollection
	 * @throws AccountException
	 */
	public static function create(array $accounts)
	{
		$accountCollection = new AccountCollection;
		foreach ($accounts as $alias => $info) {
			if (!isset($info['token'])) {
				throw new AccountException("Key 'token' is required for $alias.");
			} elseif (!isset($info['account'])) {
				throw new AccountException("Key 'account' is required for $alias.");
			}
			$accountCollection->addAccount($alias, new Fio($info['token'], new Bank($info['account'])));
		}
		return $accountCollection;
	}

}
