<?php

namespace h4kuna\Fio\Security;

use Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class AccountTest extends \Tester\TestCase
{

    public function testFull()
    {
        $account = new Account('000', new AccountBank('123-123456789/0987'));
        Assert::equal('000', $account->getToken());
        Assert::equal('123-123456789', $account->getAccount());
        Assert::equal('0987', $account->getBankCode());
    }

}

$test = new AccountTest();
$test->run();
