<?php

namespace h4kuna\Fio\Account;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class BankTest extends \Tester\TestCase
{

	public function testFull()
	{
		$account = new Bank('123-123456789/0987');
		Assert::equal('123', $account->getPrefix());
		Assert::equal('0987', $account->getBankCode());
		Assert::equal('123-123456789', $account->getAccount());
		Assert::equal('123-123456789/0987', $account->getAccountAndCode());
	}

	public function testCode()
	{
		$account = new Bank('123456789/0987');
		Assert::equal('', $account->getPrefix());
		Assert::equal('0987', $account->getBankCode());
		Assert::equal('123456789/0987', $account->getAccountAndCode());
	}

	public function testPrefix()
	{
		$account = new Bank('123-123456789');
		Assert::equal('123', $account->getPrefix());
		Assert::equal('', $account->getBankCode());
		Assert::equal('123-123456789', $account->getAccount());
		Assert::equal('123-123456789', $account->getAccountAndCode());
	}

	public function testMinimum()
	{
		$account = new Bank('123456789');
		Assert::equal('', $account->getPrefix());
		Assert::equal('', $account->getBankCode());
		Assert::equal('123456789', $account->getAccount());
		Assert::equal('123456789', $account->getAccountAndCode());
	}

	/**
	 * @dataProvider bad-accounts.ini
	 * @throws \h4kuna\Fio\AccountException
	 */
	public function testBadAccount($account)
	{
		new Bank($account);
	}

}

(new BankTest())->run();
