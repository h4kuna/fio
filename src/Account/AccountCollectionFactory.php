<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use h4kuna\Fio\Exceptions;

class AccountCollectionFactory
{

	public static function create(array $accounts): AccountCollection
	{
		$accountCollection = new AccountCollection;
		foreach ($accounts as $alias => $info) {
			if (!isset($info['token'])) {
				throw new Exceptions\InvalidArgument(sprintf('Key "token" is required for alias "%s".', $alias));
			} elseif (!isset($info['account'])) {
				throw new Exceptions\InvalidArgument(sprintf('Key "account" is required for alias "%s".', $alias));
			}
			$accountCollection->addAccount($alias, new FioAccount($info['account'], $info['token']));
		}
		return $accountCollection;
	}

}
