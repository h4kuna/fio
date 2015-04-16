<?php

namespace h4kuna\Fio;

use Tester,
    Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Milan Matějček
 */
class FioPayTest extends Tester\TestCase
{

    /** @var FioPay */
    private $fioPay;

    /** @var \Nette\DI\Container */
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    protected function setUp()
    {
        $this->fioPay = $this->container->createService('fioExtension.fioPay');
    }

    public function testSend()
    {
        \Tracy\Debugger::$maxDepth = 10;
        $national = $this->fioPay->createNational(30, '2600267402', '2010');
        $this->fioPay->addPayment($national);
        $national = $this->fioPay->createNational(50, '2600267402', '2010');
        dd($this->fioPay->send($national));
    }

}

$test = new FioPayTest($container);
$test->run();
