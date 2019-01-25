<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use h4kuna\Fio\Exceptions\InvalidArgument;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Tomáš Jacík
 */
class AccountCollectionTest extends \Tester\TestCase
{

	public function testAddAccount()
	{
		$account1 = new FioAccount('323536', 'foo');
		Assert::equal('', $account1->getBankCode());
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);

		Assert::same($accounts->account('foo'), $account1);
		Assert::same($accounts->account('bar'), $account2);
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testInvalidAlias()
	{
		$account1 = new FioAccount('323536', 'foo');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->account('bar');
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


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidState
	 */
	public function testEmpty()
	{
		(new AccountCollection)->account();
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testDuplicity()
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection;
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('foo', $account2);
	}


	public function testAccounCollectionFactoryThrowAccount()
	{
		Assert::throws(function () {
			AccountCollectionFactory::create([
				'foo' => [
					'token' => 'bar',
				],
			]);
		}, InvalidArgument::class, 'Key "account" is required for alias "foo".');
	}


	public function testAccounCollectionFactoryThrowToken()
	{
		Assert::throws(function () {
			AccountCollectionFactory::create([
				'foo' => [
					'account' => 'bar',
				],
			]);
		}, InvalidArgument::class, 'Key "token" is required for alias "foo".');
	}

}

(new AccountCollectionTest())->run();
