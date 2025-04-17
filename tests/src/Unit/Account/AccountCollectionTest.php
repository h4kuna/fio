<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Account;

use h4kuna\Fio\Account\AccountCollection;
use h4kuna\Fio\Account\AccountCollectionFactory;
use h4kuna\Fio\Account\FioAccount;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class AccountCollectionTest extends TestCase
{

	public function testAddAccount(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		Assert::equal('', $account1->getBankCode());
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection();
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);

		Assert::same($accounts->account('foo'), $account1);
		Assert::same($accounts->account('bar'), $account2);
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testInvalidAlias(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		$accounts = new AccountCollection();
		$accounts->addAccount('foo', $account1);
		$accounts->account('bar');
	}


	public function testCount(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection();
		Assert::equal(0, $accounts->count());

		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('bar', $account2);
		Assert::same(count($accounts), 2);
	}


	public function testIteration(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection();
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
	public function testEmpty(): void
	{
		(new AccountCollection())->account();
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testDuplicity(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection();
		$accounts->addAccount('foo', $account1);
		$accounts->addAccount('foo', $account2);
	}


	public function testAccountCollectionFactoryThrowAccount(): void
	{
		Assert::throws(function () {
			/** @phpstan-ignore-next-line */
			AccountCollectionFactory::create([
				'foo' => [
					'token' => 'bar',
				],
			]);
		}, InvalidArgument::class, 'Key "account" is required for alias "foo".');
	}


	public function testAccountCollectionFactoryThrowToken(): void
	{
		Assert::throws(function () {
			/** @phpstan-ignore-next-line */
			AccountCollectionFactory::create([
				'foo' => [
					'account' => 'bar',
				],
			]);
		}, InvalidArgument::class, 'Key "token" is required for alias "foo".');
	}

	public function testKeysLikeNumber(): void
	{
		$collections = AccountCollectionFactory::create([
			'1' => [
				'account' => '123456/0800',
				'token' => 'bar',
			],
			2 => [
				'account' => '987564/0800',
				'token' => 'foo',
			],
		]);

		Assert::same('bar', $collections->account('1')->getToken());
		Assert::same('foo', $collections->account('2')->getToken());
	}

}

(new AccountCollectionTest())->run();
