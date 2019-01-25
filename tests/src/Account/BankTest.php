<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

class BankTest extends \Tester\TestCase
{

	public function testFull()
	{
		$account = Bank::createNational('123-123456789/0987');
		Assert::same('123', $account->getPrefix());
		Assert::same('0987', $account->getBankCode());
		Assert::same('123-123456789', $account->getAccount());
		Assert::same('123-123456789/0987', $account->getAccountAndCode());
	}


	public function testCode()
	{
		$account = Bank::createNational('123456789/0987');
		Assert::same('', $account->getPrefix());
		Assert::same('0987', $account->getBankCode());
		Assert::same('123456789/0987', $account->getAccountAndCode());
	}


	public function testPrefix()
	{
		$account = Bank::createNational('123-123456789');
		Assert::same('123', $account->getPrefix());
		Assert::same('', $account->getBankCode());
		Assert::same('123-123456789', $account->getAccount());
		Assert::same('123-123456789', $account->getAccountAndCode());
	}


	public function testMinimum()
	{
		$account = Bank::createNational('123456789');
		Assert::same('', $account->getPrefix());
		Assert::same('', $account->getBankCode());
		Assert::same('123456789', $account->getAccount());
		Assert::same('123456789', (string) $account);
		Assert::same('123456789', $account->getAccountAndCode());
	}


	public function testEuroFull()
	{
		$account = Bank::createInternational('EE123745671789355096/LAVBDD33XXX');
		Assert::same('EE123745671789355096', $account->getAccount());
		Assert::same('LAVBDD33XXX', $account->getBankCode());
		Assert::same('EE123745671789355096/LAVBDD33XXX', $account->getAccountAndCode());
	}


	public function testEuroMinimum()
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
	public function testEuroThrowBadAccount()
	{
		Bank::createInternational('EE1237456717-9355096');
	}


	/**
	 * @dataProvider bad-accounts.ini
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testBadAccount($account)
	{
		Bank::createNational($account);
	}

}

(new BankTest())->run();
