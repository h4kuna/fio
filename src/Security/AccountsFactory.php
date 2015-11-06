<?php

namespace h4kuna\Fio\Security;

/**
 * @author Milan Matějček
 */
class AccountsFactory
{

	/**
	 * @param array $accounts
	 * @return Accounts
	 * @throws FioException
	 */
	public static function create(array $accounts)
	{
		$accountsObject = new Accounts;
		foreach ($accounts as $alias => $info) {
			if (!isset($info['token'])) {
				throw new FioException("Key 'token' is required for $alias.");
			} elseif (!isset($info['account'])) {
				throw new FioException("Key 'account' is required for $alias.");
			}
			$accountsObject->addAccount($alias, new Account($info['token'], new AccountBank($info['account'])));
		}
		return $accountsObject;
	}

}
