<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use Tester\Assert;
use h4kuna\Fio\Test;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class BankTest extends Test\TestCase
{

	public function testFull(): void
	{
		$account = Bank::createNational('123-123456789/0987');
		Assert::same('123', $account->getPrefix());
		Assert::same('0987', $account->getBankCode());
		Assert::same('123-123456789', $account->getAccount());
		Assert::same('123-123456789/0987', $account->getAccountAndCode());
	}


	public function testCode(): void
	{
		$account = Bank::createNational('123456789/0987');
		Assert::same('', $account->getPrefix());
		Assert::same('0987', $account->getBankCode());
		Assert::same('123456789/0987', $account->getAccountAndCode());
	}


	public function testPrefix(): void
	{
		$account = Bank::createNational('123-123456789');
		Assert::same('123', $account->getPrefix());
		Assert::same('', $account->getBankCode());
		Assert::same('123-123456789', $account->getAccount());
		Assert::same('123-123456789', $account->getAccountAndCode());
	}


	public function testMinimum(): void
	{
		$account = Bank::createNational('123456789');
		Assert::same('', $account->getPrefix());
		Assert::same('', $account->getBankCode());
		Assert::same('123456789', $account->getAccount());
		Assert::same('123456789', (string) $account);
		Assert::same('123456789', $account->getAccountAndCode());
	}


	public function testEuroFull(): void
	{
		$account = Bank::createInternational('EE123745671789355096/LAVBDD33XXX');
		Assert::same('EE123745671789355096', $account->getAccount());
		Assert::same('LAVBDD33XXX', $account->getBankCode());
		Assert::same('EE123745671789355096/LAVBDD33XXX', $account->getAccountAndCode());
	}


	public function testEuroMinimum(): void
	{
		$account = Bank::createInternational('EE123745671789355096');
		Assert::same('EE123745671789355096', $account->getAccount());
		Assert::same('', $account->getBankCode());
		Assert::same('', $account->getPrefix());
		Assert::same('EE123745671789355096', $account->getAccountAndCode());
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testEuroThrowBadAccount(): void
	{
		Bank::createInternational('EE1237456717-9355096');
	}


	/**
	 * @dataProvider bad-accounts.ini
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testBadAccount(string $account): void
	{
		Bank::createNational($account);
	}

}

(new BankTest())->run();
