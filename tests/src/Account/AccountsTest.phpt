<?php

namespace h4kuna\Fio\Account;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class AccountsTest extends \Tester\TestCase
{

	public function testAddAccount()
	{
		$account1 = new Fio('foo', new Bank('323536'));
		$account2 = new Fio('bar', new Bank('978654'));
		$accounts = new Accounts;
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);

		Assert::same($accounts->getActive(), $account1);
		$accounts->setActive('bar');
		Assert::same($accounts->getActive(), $account2);
	}

	/**
	 * @throws \h4kuna\Fio\AccountException
	 */
	public function testInvalidAlias()
	{
		$account1 = new Fio('foo', new Bank('323536'));
		$accounts = new Accounts;
		$accounts->addAccount('foo', $account1);
		$accounts->setActive('bar');
	}

}

(new AccountsTest())->run();
