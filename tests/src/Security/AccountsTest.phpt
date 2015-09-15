<?php

namespace h4kuna\Fio\Security;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class AccountsTest extends \Tester\TestCase
{

    public function testAdd()
    {

        $account = new Account('000', new AccountBank('123-123456789/0987'));
        $acounts = new Accounts();
        $acounts->addAccount('trial', $account);
        Assert::equal($account, $acounts->getActive());
    }

    public function testActive()
    {
        $account1 = new Account('000', new AccountBank('123-123456789/0987'));
        $account2 = new Account('000', new AccountBank('123-123456789/0987'));
        $accounts = new Accounts();

        $accounts->addAccount('trial', $account1);
        Assert::equal($account1, $accounts->getActive());

        $accounts->addAccount('exam', $account2);
        Assert::equal($account2, $accounts->getActive());

        $accounts->setActive('trial');
        Assert::equal($account1, $accounts->getActive());
    }

}

$test = new AccountsTest();
$test->run();
