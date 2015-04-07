<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Request,
    h4kuna\Fio\Response\Read\StatementFactory,
    h4kuna\Fio\Test\Queue as QueueTest,
    Tester,
    Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../data/Queue.php';
require_once __DIR__ . '/../data/StatementFactory.php';

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
        $national = $this->fioPay->createNational(500, '987654321', '1234');
        dd($this->fioPay->send($national));

        $this->fioPay->setLanguage('en');
        $this->fioPay->send($request);
    }

}

$test = new FioPayTest($container);
$test->run();
