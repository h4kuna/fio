<?php

namespace h4kuna\Fio\Security;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class AccountBankTest extends \Tester\TestCase
{

	public function testFull()
	{
		$account = new AccountBank('123-123456789/0987');
		Assert::equal('123', $account->getPrefix());
		Assert::equal('0987', $account->getBankCode());
		Assert::equal('123-123456789', $account->getAccount());
		Assert::equal('123-123456789/0987', $account->getAccountAndCode());
	}

	public function testCode()
	{
		$account = new AccountBank('123456789/0987');
		Assert::equal('', $account->getPrefix());
		Assert::equal('0987', $account->getBankCode());
		Assert::equal('123456789/0987', $account->getAccountAndCode());
	}

	public function testPrefix()
	{
		$account = new AccountBank('123-123456789');
		Assert::equal('123', $account->getPrefix());
		Assert::equal('', $account->getBankCode());
		Assert::equal('123-123456789', $account->getAccount());
		Assert::equal('123-123456789', $account->getAccountAndCode());
	}

	public function testMinimum()
	{
		$account = new AccountBank('123456789');
		Assert::equal('', $account->getPrefix());
		Assert::equal('', $account->getBankCode());
		Assert::equal('123456789', $account->getAccount());
		Assert::equal('123456789', $account->getAccountAndCode());
	}

	/**
	 * @dataProvider bad-accounts.ini
	 * @throws \h4kuna\Fio\Utils\FioException
	 */
	public function testBadAccount($account)
	{
		new AccountBank($account);
	}

}

$test = new AccountBankTest();
$test->run();
