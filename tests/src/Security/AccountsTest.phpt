<?php

namespace h4kuna\Fio\Security;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class AccountsTest extends \Tester\TestCase
{

    public function testActive()
    {
        $account1 = new Account('000', new AccountBank('123-123456789/0987'));
        $account2 = new Account('0123', new AccountBank('123-123456789/0123'));
        $accounts = new Accounts();

        $accounts->addAccount('trial', $account1);
        Assert::equal($account1, $accounts->getActive());

        $accounts->addAccount('exam', $account2);
        Assert::equal($account1, $accounts->getActive());

        $accounts->setActive('exam');
        Assert::equal($account2, $accounts->getActive());
    }

    /**
     * @throws \h4kuna\Fio\Utils\FioException
     */
    public function testUndefiniedAlias()
    {
        $account = new Account('0123', new AccountBank('123-123456789/0123'));
        $accounts = new Accounts();
        $accounts->addAccount('trial', $account);
        $accounts->setActive('foobar');
    }

}

$test = new AccountsTest();
$test->run();
