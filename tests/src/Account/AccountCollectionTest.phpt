<?php

namespace h4kuna\Fio\Account;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

/**
 * @author Tomáš Jacík
 * @author Milan Matějček
 */
class AccountCollectionTest extends \Tester\TestCase
{

	public function testAddAccount()
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);

		Assert::same($accounts->get('foo'), $account1);
		Assert::same($accounts->get('bar'), $account2);
	}

	/**
	 * @throws \h4kuna\Fio\AccountException
	 */
	public function testInvalidAlias()
	{
		$account1 = new FioAccount('323536', 'foo');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->get('bar');
	}

	public function testCount()
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection;
		Assert::equal(0, $accounts->count());

		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);
		Assert::same(count($accounts), 2);
	}

	public function testIteration()
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);

		Assert::same(iterator_to_array($accounts), [
			'foo' => $account1,
			'bar' => $account2,
		]);
	}

	public function testEmpty()
	{
		$accounts = new AccountCollection;
		Assert::equal(FALSE, $accounts->getDefault());
	}

	/**
	 * @throws \h4kuna\Fio\AccountException
	 */
	public function testDuplicity()
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('foo', $account2);
	}

}

(new AccountCollectionTest())->run();
