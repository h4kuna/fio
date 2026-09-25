<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Unit\Account;

use h4kuna\Fio\Account\AccountCollection;
use h4kuna\Fio\Account\AccountCollectionFactory;
use h4kuna\Fio\Account\FioAccount;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Exceptions\InvalidState;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function count;
use function iterator_to_array;

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

	public function testInvalidAlias(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		$accounts = new AccountCollection();
		$accounts->addAccount('foo', $account1);
		Assert::exception(static fn () => $accounts->account('bar'), InvalidArgument::class);
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

	public function testEmpty(): void
	{
		Assert::exception(static fn () => (new AccountCollection())->account(), InvalidState::class);
	}

	public function testDuplicity(): void
	{
		$account1 = new FioAccount('323536', 'foo');
		$account2 = new FioAccount('978654', 'bar');
		$accounts = new AccountCollection();
		$accounts->addAccount('foo', $account1);
		Assert::exception(static fn () => $accounts->addAccount('foo', $account2), InvalidArgument::class);
	}

	public function testAccountCollectionFactoryThrowAccount(): void
	{
		Assert::throws(static function (): void {
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
		Assert::throws(static function (): void {
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
